<?php
add_action('wp_ajax_aveforms_contact_form', 'aveforms_handle_form');
add_action('wp_ajax_nopriv_aveforms_contact_form', 'aveforms_handle_form');

function aveforms_handle_form() {

    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aveforms_contact_form_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

	$aveform_id = isset($_POST['aveform_id']) ? intval($_POST['aveform_id']) : 0;

	if ($aveform_id <= 0) {
		wp_send_json_error('Invalid form ID');
	}

    // Prepare the query arguments
	$args = [
		'post_type' => 'aveform',
		'post_status' => 'publish',
        'p' => $aveform_id,
	];

	// Query the post
	$query = new WP_Query($args);
	
	// Check if the post exists
	if (!$query->have_posts()) {
		wp_send_json_error('Form not found');
	}
	
	// Loop through the posts
	while ($query->have_posts()) {
		$query->the_post();
		$content = get_the_content();

        $validation_ruleset_arr = get_validation_ruleset_from_content($content);
        
	}

	if (!array_key_exists('rules', $validation_ruleset_arr)) {
		wp_send_json_error('Validation ruleset is empty');
	}

	require_once plugin_dir_path(__FILE__) . '/AveValidator.php';

	$validator = new AveValidator();

	$validator->setRules($validation_ruleset_arr['rules']);
	
	if (array_key_exists('messages', $validation_ruleset_arr)) {
		$validator->setMessages($validation_ruleset_arr['messages']);
	}

	if (array_key_exists('labels', $validation_ruleset_arr)) {
		$validator->setLabels($validation_ruleset_arr['labels']);
	}
	
	
	if ($validator->validate($_POST) === true) {
		// Prepare email data
		// Here you can set the email recipient, subject, body and headers
		// You can also use the WordPress function wp_mail to send the email
		//$to = get_option('admin_email');
		//$subject = "Contact Form Submission from $first_name $last_name";
		//$body = "Name: $first_name $last_name\nEmail: $email\nMessage:\n$message";
		//$headers = ['Content-Type: text/plain; charset=UTF-8'];

		// wp_mail($to, $subject, $body, $headers);
		// For demonstration, we will just return the sanitized data
		// Send a success response
		wp_send_json_success('Message sent successfully!');
	}

	// If validation fails, return the errors
	wp_send_json_error($validator->getErrors());    
	
}

function get_validation_ruleset_from_content($content) {

    $validation_ruleset_arr = [];
    // search for the aveform shortcodes in the content
    // This regex will match aveform_input, aveform_submit, and aveform_textarea shortcodes
    // which looks like: [aveform_input type="text" name="first_name" label="First Name" placeholder="First Name" rules="required{The field is required}|string{The value must be a string}|max:255{Maximum number of characters reached}|min:3{Minimum number of characters not met}"]
    // [aveform_input type="text" name="last_name" label="Last Name" placeholder="Last Name" rules="required|string|max:255|min:3"]
    // [aveform_input type="email" name="email" label="Email" placeholder="email@email.com" rules="required|email"]
    // [aveform_textarea name="message" label="Message" placeholder placeholder="Type your message here" rows="3" rules="required|string"]

    $shortcode_types = [
        'input',
        'textarea',
        'submit',
        // We will add more types here as needed, like 'select'.
    ];


    $pattern = '/\[aveform_(' . implode('|', $shortcode_types) . ')(.*?)\]/i';
    if (preg_match_all($pattern, $content, $matches)) {
        
        // Loop through the matches and sanitize the data
        foreach ($matches[0] as $key => $match) {
            // This will be 'input', 'textarea', or 'submit'
            // we do this becaut the input type is not always inserted as a type parameter of the shortcode
            // sometimes is implied by the shortcode name [aveform_textarea] or [aveform_submit] or, later on, [aveform_select]
            $input_type = $matches[1][$key];

            // Extract the attributes from the shortcode
            if(preg_match_all('/(\w+)=["\']?([^"\']*)["\']?/', $match, $attr_matches)) {
                // $input_data should be an associative array with keys like 'name', 'rules', 'label', etc.
                $input_data = get_input_data_from_shortcode($attr_matches, $input_type);

                if(!empty($input_data['name']) && !empty($input_data['rules'])) {
                    // Add the input data to the validation ruleset array
                    $validation_ruleset_arr['input_types'][$input_data['name']] = $input_data['type'] ?? 'text'; // Default to text if type is not set
                    $validation_ruleset_arr['rules'][$input_data['name']] = $input_data['rules'];
                    if (!empty($input_data['messages'])) {
                        $validation_ruleset_arr['messages'][$input_data['name']] = $input_data['messages'];
                    }
                    if (!empty($input_data['label'])) {
                        $validation_ruleset_arr['labels'][$input_data['name']] = $input_data['label'];
                    }
                }
            }
        }
    }

    return $validation_ruleset_arr;
}

function get_input_data_from_shortcode($attr_matches, $fallback_type = 'text') {

    $input_data = [
        'name' => '',
        'type' => $fallback_type,
        'rules' => [],
        'messages' => [],
        'label' => '',
    ];
    // $attr_matches[0] contains the full attribute string
    // $attr_matches[1] contains the attribute names
    // $attr_matches[2] contains the attribute values
    foreach ($attr_matches[1] as $index => $attr_name) {

        $attr_value = sanitize_text_field($attr_matches[2][$index]);

        // Normalize the attribute name to lowercase
        $attr_name = strtolower($attr_name);

        switch ($attr_name) {
            case 'name':
                $input_data['name'] = $attr_value;
                break;
            case 'type':
                // who knows... maybe we will use it in the future
                $input_data['type'] = $attr_value;
                break;
            case 'label':
                $input_data['label'] = $attr_value;
                break;
            case 'rules':
                // If the attribute is rules, we need to process it
                // The rules are separated by '|', and each rule can have a message
                $rules = [];
                $messages = [];
                $rules_data = explode('|', $attr_value);
                foreach ($rules_data as $rule) {
                    // Split the rule and message if it exists
                    $rule_parts = explode('{', $rule);
                    $rule_name = trim($rule_parts[0]);
                    if (count($rule_parts) > 1) {
                        // If there is a message, it will be the second part
                        // Remove the closing '}' from the message
                        $messages[$rule_name] = rtrim($rule_parts[1], '}');
                    }

                    $rules[] = $rule_name;
                    
                }
                $input_data['rules'] = $rules;
                $input_data['messages'] = $messages;
                break;
            default:
                // If the attribute is not recognized, we can ignore it or handle it as needed
                break;
        }
    }

    return $input_data;
}
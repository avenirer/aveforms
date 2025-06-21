<?php

require_once plugin_dir_path(__FILE__) . '/shortcodes/aveform.php';
require_once plugin_dir_path(__FILE__) . '/shortcodes/aveformshow.php';

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

	$validation_ruleset = get_post_meta( $aveform_id, 'aveform_validation', true );

	if( empty($validation_ruleset) ) {
		wp_send_json_error('Validation ruleset not found for this form');
	}

	// Convert the validation ruleset to an array if it's a json string
	if (is_string($validation_ruleset)) {
		$validation_ruleset_arr = json_decode($validation_ruleset, true);
		
	}

	if (json_last_error() !== JSON_ERROR_NONE) {
		wp_send_json_error('Invalid validation ruleset format');
	}

	if (empty($validation_ruleset_arr) || !is_array($validation_ruleset_arr) || !array_key_exists('rules', $validation_ruleset_arr)) {
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
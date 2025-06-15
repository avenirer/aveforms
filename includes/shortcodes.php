<?php
function ave_contact_form_shortcode($atts = array(), $content = null, $tag = '') {

	// normalize attribute keys, lowercase
	
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$params = shortcode_atts(
		[
			'title' => 'Contact Form',
			'description' => 'Please fill out the form below to contact us.',
			'form_id' => 'aveforms_contact_form',
			'form_class' => 'aveforms',
			'button_text' => 'Send Message',
		], $atts, $tag
	);
    // Enqueue CSS
    wp_enqueue_style(
        'aveforms-css',
        plugins_url('../assets/css/aveforms.css', __FILE__),
        [],
        null
    );

	// Enqueue JS only when shortcode is used
    wp_enqueue_script(
        'aveforms-js',
        plugins_url('../assets/js/aveforms.js', __FILE__),
		// Dependencies, in this case, we don't have any dependencies
		// but if you have jQuery or other libraries, you can add them here
        [],
        null,
		// Use the 'defer' attribute to load the script after the document has been parsed
		// This is useful for scripts that do not need to be executed immediately
		// and the 'async' attribute to load the script asynchronously
		// This is useful for scripts that do not depend on other scripts
		// and can be executed as soon as they are loaded
		// 'in_footer' ensures that the script is loaded in the footer
        [
			'strategy' => 'defer',
			'async' => true,
			'in_footer' => true,
		]
    );
	
    // Pass ajax_url to JS
    wp_localize_script('aveforms-js', 'aveforms_ajax', array(
        'ajax_url' => esc_url(admin_url('admin-ajax.php')),
		'form_id' => $params['form_id'],
		'form_class' => $params['form_class'],
    ));
	ob_start();
	include plugin_dir_path(__FILE__) . 'contact-form.php';
	return ob_get_clean();
	
}

add_shortcode('aveform', 'ave_contact_form_shortcode');

add_action('wp_ajax_aveforms_contact_form', 'aveforms_handle_contact_form');
add_action('wp_ajax_nopriv_aveforms_contact_form', 'aveforms_handle_contact_form');

function aveforms_handle_contact_form() {


    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aveforms_contact_form_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    // Sanitize and process form data
	// We will sanitize the input data to prevent XSS attacks and other security issues
	// We will use the WordPress functions sanitize_text_field, sanitize_email and sanitize_textarea_field
	// These functions will remove any unwanted characters and ensure that the data is safe to use
    $first_name = sanitize_text_field($_POST['first_name'] ?? '');
    $last_name  = sanitize_text_field($_POST['last_name'] ?? '');
    $email      = sanitize_email($_POST['email'] ?? '');
    $message    = sanitize_textarea_field($_POST['message'] ?? '');

	// if everything is ok, we can send the email
	if (empty($first_name) || empty($last_name) || empty($email) || empty($message)) {
		wp_send_json_error('All fields are required.');
	}

	// Validate email
	if (!is_email($email)) {
		wp_send_json_error('Invalid email address.');
	}

	// Prepare email data
	// Here you can set the email recipient, subject, body and headers
	// You can also use the WordPress function wp_mail to send the email
	$to = get_option('admin_email');
    $subject = "Contact Form Submission from $first_name $last_name";
    $body = "Name: $first_name $last_name\nEmail: $email\nMessage:\n$message";
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    wp_mail($to, $subject, $body, $headers);
	// For demonstration, we will just return the sanitized data
	// Send a success response
    wp_send_json_success('Message sent successfully!');
}
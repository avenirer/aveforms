<?php
function ave_contact_form_shortcode() {
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
        'ajax_url' => esc_url(admin_url('admin-ajax.php'))
    ));
	ob_start();
	?>
		<h2>Contact Form</h2>
		<form id="contactform" class="aveforms" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">   
			<label for="first_name">First Name:</label>
			<input type="text" id="first_name" name="first_name" placeholder="First Name" required />

			<label for="last_name">Last Name:</label>
			<input type="text" id="last_name" name="last_name" placeholder="Last Name" required />

			<label for="email">Email:</label>
			<input type="email" id="email" name="email" placeholder="email@email.com" required>

			<label for="message">Message:</label>
			<textarea rows="3" id="message" name="message" placeholder="Type your message here" required></textarea>

			<input id="aveformssubmit" type="submit" value="Send message" class="aveforms-submit">
			<?php
			// We need to add a nonce in order to verify that the request is coming from our form
			// This is a security measure to prevent CSRF attacks
			// We will use the wordpress function wp_create_nonce to generate a nonce
			// and we will later verify it in the aveforms_handle_contact_form function ?>
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('aveforms_contact_form_nonce'); ?>">
			<?php
			// This is the action that will be triggered when the form is submitted
			// It will be handled by the aveforms_handle_contact_form function ?>
			<input type="hidden" name="action" value="aveforms_contact_form">
			<?php
			// This is the status div where we will display the response from the server ?>
			<div id="status" class="status"></div>
		</form>
	<?php
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

    // Example: send email (customize as needed)
    //$to = get_option('admin_email');
    //$subject = "Contact Form Submission from $first_name $last_name";
    //$body = "Name: $first_name $last_name\nEmail: $email\nMessage:\n$message";
    //$headers = ['Content-Type: text/plain; charset=UTF-8'];

    //wp_mail($to, $subject, $body, $headers);
	// For demonstration, we will just return the sanitized data
	// Send a success response
    wp_send_json_success('Message sent successfully!' . json_encode(compact('first_name', 'last_name', 'email', 'message')));
}
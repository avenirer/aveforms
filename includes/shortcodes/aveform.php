<?php
add_shortcode('aveform', 'aveform_shortcode');
function aveform_shortcode($atts = array(), $content = null, $tag = '') {

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
        plugins_url('../../assets/css/aveforms.css', __FILE__),
        [],
        null
    );

	// Enqueue JS only when shortcode is used
    wp_enqueue_script(
        'aveforms-js',
        plugins_url('../../assets/js/aveforms.js', __FILE__),
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
	echo '<div class="aveforms-container">';
		echo '<h2 class="aveforms-title">' . $params['title'] . '</h2>';
		echo '<div class="aveforms-description">' . $params['description'] . '</div>';
			echo '<form id="' . $params['form_id'] . '" class="' . $params['form_class'] . '" method="post" action="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '">';
				echo $content;
				echo '<input id="aveformssubmit" type="submit" value="' . $params['button_text'] . '" class="aveforms-submit">';
				echo '<input type="hidden" name="nonce" value="' . wp_create_nonce('aveforms_contact_form_nonce') . '">';
				echo '<input type="hidden" name="action" value="aveforms_contact_form">';
				echo '<div id="status" class="status"></div>';
			echo '</form>';
	echo '</div>';
	return ob_get_clean();
	
}
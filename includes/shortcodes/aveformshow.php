<?php
add_shortcode('aveformshow', 'aveformshow_shortcode');
function aveformshow_shortcode($atts = array(), $content = null, $tag = '') {
	// normalize attribute keys, lowercase	
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$params = shortcode_atts(
		[
			'id' => null,
			'slug' => null,
		], $atts, $tag
	);

	// Check if the ID or slug is provided
	if (empty($params['id']) && empty($params['slug'])) {
		return '';
	}

	// Prepare the query arguments
	$args = [
		'post_type' => 'aveform',
		'post_status' => 'publish',
	];

	// If ID is provided, use it; otherwise, use the slug
	// We will use the sanitize_title function to ensure that the slug is safe to use
	if (!empty($params['id'])) {
		$args['p'] = intval($params['id']);
	} elseif (!empty($params['slug'])) {
		$args['name'] = sanitize_title($params['slug']);
	}

	// Query the post
	$query = new WP_Query($args);
	
	// Check if the post exists
	if (!$query->have_posts()) {
		return '';
	}
	
	// Start output buffering
	ob_start();
	// Loop through the posts
	while ($query->have_posts()) {
		$query->the_post();
		$post_id = get_the_ID();
		// Get the content of the post
		$content = get_the_content();
		// Display the content
		$content =  str_replace(
			'[/aveform]',
			'<input type="hidden" name="aveform_id" value="' . $post_id . '" />[/aveform]',
			$content);
		echo apply_filters('the_content', $content);
	}
	return ob_get_clean();

}
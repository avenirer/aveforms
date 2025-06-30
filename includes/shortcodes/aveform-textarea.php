<?php

add_shortcode('aveform_textarea', 'aveform_textarea_shortcode');

function aveform_textarea_shortcode($atts = array(), $content = null, $tag = '') {
  
  // normalize attribute keys, lowercase
  $atts = array_change_key_case( (array) $atts, CASE_LOWER );
  
  // override default attributes with user attributes
  $params = shortcode_atts(
    [
	  'name' => '',
	  'class' => '',
	  'rows' => 3,
	  'cols' => 0,
	  'label' => '',
	  'placeholder' => '',
	  'rules' => '',
    ], $atts, $tag);
	
	if (empty($params['name'])) {
	  return true;
	}
    
    ob_start();
    
    echo '<div class="aveform-input-container ' . esc_attr($params['class']) . '">';
	
	if (!empty($params['label'])) {
	  echo '<label for="' . esc_attr($params['name']) . '" class="aveform-input-label">' . esc_attr($params['label']) . '</label>';
	}

    echo '<textarea name="'
	. esc_attr($params['name'])
	. '" id="' . esc_attr($params['name'])
	. '" class="aveform-input aveform-input-textarea" placeholder="'
	. esc_attr($params['placeholder'])
	. '"'
	. (intval(esc_attr($params['rows'])) > 0 ? ' rows="' . esc_attr($params['rows']) . '"' : '')
	. (intval(esc_attr($params['cols'])) > 0 ? ' cols="' . esc_attr($params['cols']) . '"' : '')
	. (strpos(esc_attr($params['rules']), 'required') !== false ? ' required' : '')
	. ' ></textarea>';
    
    echo '</div>';
    
    return ob_get_clean();

}
<?php

add_shortcode('aveform_input', 'aveform_input_shortcode');

function aveform_input_shortcode($atts = array(), $content = null, $tag = '') {
  // normalize attribute keys, lowercase
  $atts = array_change_key_case( (array) $atts, CASE_LOWER );
  
  // override default attributes with user attributes
  $params = shortcode_atts(
    [
        'type' => 'text',
        'name' => '',
        'class' => '',
        'label' => '',
        'placeholder' => '',
        'rules' => '',
    ], $atts, $tag);
  
  if (empty($params['name']) || empty($params['type'])) {
    return true;
  }

  ob_start();
  
  echo '<div class="aveform-input-container ' . esc_attr($params['class']) . '">';

  if (!empty($params['label'])) {
    echo '<label for="' . esc_attr($params['name']) . '" class="aveform-input-label">' . esc_attr($params['label']) . '</label>';
  }
  
  echo '<input type="' . esc_attr($params['type']) . '" name="' . esc_attr($params['name']) . '" id="' . esc_attr($params['name']) . '" class="aveform-input aveform-input-' . esc_attr($params['type']) . '" placeholder="' . esc_attr($params['placeholder']) . '"' . (strpos(esc_attr($params['rules']), 'required') !== false ? ' required' : '') . ' />';
  
  echo '</div>';
  
  return ob_get_clean();

}
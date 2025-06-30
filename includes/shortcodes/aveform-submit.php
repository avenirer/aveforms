<?php

add_shortcode('aveform_submit', 'aveform_submit_shortcode');

function aveform_submit_shortcode($atts = array(), $content = null, $tag = '') {
  // normalize attribute keys, lowercase
  $atts = array_change_key_case( (array) $atts, CASE_LOWER );
  
  // override default attributes with user attributes
  $params = shortcode_atts(
    [
      'class' => '',
      'text' => 'Save',
    ], $atts, $tag);
  
  ob_start();
  
  echo '<div class="aveform-input-container ' . esc_attr($params['class']) . '">';
  echo '<input id="aveformssubmit" type="submit" value="' . $params['text'] . '" class="aveforms-submit">';
  echo '</div>';
  
  return ob_get_clean();

}
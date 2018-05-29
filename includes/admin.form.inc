<?php

/**
 * @file
 * Admin helpers for Islandora Audio.
 */

/**
 * Validation callback for the VBR Quality field.
 *
 * @param array $element
 *   The Drupal element definition.
 * @param array $form_state
 *   The Drupal form state.
 */
function islandora_audio_vbr_quality_validate(array $element, array &$form_state) {
  if (!empty($element['#value']) || $element['#value'] === "0") {
    $quality = floatval($element['#value']);
    if ($quality >= 0 && $quality <= 9.999) {
      return;
    }
    else {
      form_error($element, t('Please enter a VBR quality value between 0 and 9.999.'));
    }
  }
  else {
    form_set_value($element, 5, $form_state);
  }
}

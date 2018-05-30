<?php

/**
 * @file
 * Admin helpers for Islandora Audio.
 */

/**
 * Ajax callback for the Lame textfield.
 *
 * @param array $form
 *   The Drupal form definition.
 * @param FormStateInterface $form_state
 *   The Drupal form state.
 *
 * @return array
 *   The element to render as part the ajax callback.
 */
function islandora_audio_admin_form_lame_ajax_callback(array $form, FormStateInterface $form_state) {
  return $form['islandora_lame_url'];
}

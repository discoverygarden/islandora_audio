<?php

namespace Drupal\islandora_audio\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Module settings form.
 */
class Admin extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'islandora_audio_admin';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('islandora_audio.settings');

    $config->set('islandora_lame_url', $form_state->getValue('islandora_lame_url'));
    $config->set('islandora_audio_defer_derivatives_on_ingest', $form_state->getValue('islandora_audio_defer_derivatives_on_ingest'));
    $config->set('islandora_audio_vbr_quality', $form_state->getValue('islandora_audio_vbr_quality'));
    $config->set('islandora_audio_obj_fallback', $form_state->getValue('islandora_audio_obj_fallback'));

    islandora_set_viewer_info('islandora_audio_viewers', $form_state->getValue('islandora_audio_viewers'));

    $config->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['islandora_audio.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->loadInclude('islandora', 'inc', 'includes/utilities');
    $form_state->loadInclude('islandora_audio', 'inc', 'includes/admin.form');
    $config = $this->config('islandora_audio.settings');

    // Check the form state first for the lame url so AJAX can work.
    $lame_url = $form_state->getValue('islandora_lame_url');
    $lame = (NULL !== $lame_url) ? $lame_url : $config->get('islandora_lame_url');

    $form['derivatives'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Derivatives'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['derivatives'] += [
      'islandora_audio_defer_derivatives_on_ingest' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Defer audio derivative generation during ingest'),
        '#description' => $this->t('Prevent audio derivatives from running during ingest,
          useful if derivatives are to be created by an external service.'),
        '#default_value' => $config->get('islandora_audio_defer_derivatives_on_ingest'),
      ],
      'islandora_lame_url' => [
        '#type' => 'textfield',
        '#title' => $this->t("Path to LAME"),
        '#description' => $this->t('@LAME is required to create derivative files.<br/>',
                        [
                          '@LAME' => Link::fromTextAndUrl($this->t('LAME'), Url::fromUri('http://lame.sourceforge.net/'))->toString(),
                        ]) . islandora_executable_available_message($lame),
        '#default_value' => $lame,
        '#size' => 20,
        '#prefix' => '<div id="lame-wrapper">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => 'islandora_audio_admin_form_lame_ajax_callback',
          'wrapper' => 'lame-wrapper',
          'effect' => 'fade',
          'event' => 'change',
        ],
      ],
      'islandora_audio_vbr_quality' => [
        '#type' => 'number',
        '#min' => 0,
        '#max' => 9.999,
        '#step' => 0.001,
        '#title' => $this->t('MP3 derivative quality'),
        '#description' => $this->t('Variable Bit Rate quality setting
          (0 = highest quality, 9.999 = lowest). Default = 5.'),
        '#size' => 5,
        '#default_value' => $config->get('islandora_audio_vbr_quality'),
      ],
      'islandora_audio_obj_fallback' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Use original file as fallback'),
        '#description' => $this->t('Attempt to play the OBJ datastream in the
          player if the PROXY_MP3 derivative is not present.'),
        '#default_value' => $config->get('islandora_audio_obj_fallback'),
      ],
    ];

    $form_state->loadInclude('islandora', 'inc', 'includes/solution_packs');
    $form += islandora_viewers_form('islandora_audio_viewers', 'audio/mpeg');
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

}

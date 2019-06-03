<?php

namespace Drupal\islandora_audio\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Upload form when ingesting audio objects.
 */
class AudioUpload extends FormBase {

  protected $fileEntityStorage;

  /**
   * Constructor.
   */
  public function __construct(EntityStorageInterface $file_entity_storage) {
    $this->fileEntityStorage = $file_entity_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('file')
    );
  }

  /**
   * Defines a file upload form for uploading the audio.
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'islandora_audio_upload_form';
  }

  /**
   * Submit handler, adds uploaded file to ingest object.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $upload_size = min((int) ini_get('post_max_size'), (int) ini_get('upload_max_filesize'));
    $audio_extensions = ['wav mp3'];
    $thumbnail_extensions = ['gif jpg png jpeg'];
    $upload_required = $this->config('islandora.settings')->get('islandora_require_obj_upload');

    return [
      'audio_file' => [
        '#title' => $this->t('Audio File'),
        '#type' => 'managed_file',
        '#required' => $upload_required,
        '#description' => $this->t('Select a file to upload.<br/>Files must be
          less than <strong>@size MB.</strong><br/>Allowed file types: <strong>
          @ext.</strong>', [
            '@size' => $upload_size,
            '@ext' => $audio_extensions[0],
          ]),
        '#default_value' => $form_state->getValue('audio_file'),
        '#upload_location' => 'temporary://',
        '#upload_validators' => [
          'file_validate_extensions' => $audio_extensions,
           // Assume its specified in MB.
          'file_validate_size' => [$upload_size * 1024 * 1024],
        ],
      ],
      'supply_thumbnail' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Upload Thumbnail'),
      ],
      'thumbnail_section' => [
        'thumbnail_file' => [
          '#title' => $this->t('Thumbnail File'),
          '#type' => 'managed_file',
          '#description' => $this->t('Select a file to upload.
            <br/>Files must be less than <strong>@size MB.</strong>
            <br/>Allowed file types: <strong>@ext.</strong>', [
              '@size' => $upload_size,
              '@ext' => $thumbnail_extensions[0],
            ]),
          '#default_value' => $form_state->getValue('thumbnail_file'),
          '#upload_location' => 'temporary://',
          '#upload_validators' => [
            'file_validate_extensions' => $thumbnail_extensions,
             // Assume its specified in MB.
            'file_validate_size' => [$upload_size * 1024 * 1024],
          ],
        ],
        'scale_thumbnail' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Scale Thumbnail'),
          '#attributes' => ['checked' => 'checked'],
        ],
        '#type' => 'item',
        '#states' => [
          'visible' => ['#edit-supply-thumbnail' => ['checked' => TRUE]],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('supply_thumbnail') &&
      !$form_state->getValue('thumbnail_file')) {
      $form_state->setErrorByName('thumbnail_file', $this->t('If you select "Upload Thumbnail" please supply a file.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('islandora', 'inc', 'includes/utilities');
    $object = islandora_ingest_form_get_object($form_state);
    if ($form_state->getValue('audio_file')) {
      if (empty($object['OBJ'])) {
        $obj = $object->constructDatastream('OBJ', 'M');
        $object->ingestDatastream($obj);
      }
      else {
        $obj = $object['OBJ'];
      }
      $audio_file = $this->fileEntityStorage->load(reset($form_state->getValue('audio_file')));
      $obj->setContentFromFile($audio_file->getFileUri(), FALSE);
      if ($obj->label != $audio_file->getFilename()) {
        $obj->label = $audio_file->getFilename();
      }
      if ($obj->mimetype != $audio_file->getMimeType()) {
        $obj->mimetype = $audio_file->getMimeType();
      }
    }
    if ($form_state->getValue('supply_thumbnail')) {
      $thumbnail_file = $this->fileEntityStorage->load(reset($form_state->getValue('thumbnail_file')));
      if ($form_state->getValue('scale_thumbnail')) {
        islandora_scale_thumbnail($thumbnail_file, 200, 200);
      }
      if (empty($object['TN'])) {
        $tn = $object->constructDatastream('TN', 'M');
        $object->ingestDatastream($tn);
      }
      else {
        $tn = $object['TN'];
      }
      $tn->setContentFromFile($thumbnail_file->getFileUri(), FALSE);
      if ($tn->label != $thumbnail_file->getFilename()) {
        $tn->label = $thumbnail_file->getFilename();
      }
      if ($tn->mimetype != $thumbnail_file->getMimeType()) {
        $tn->mimetype = $thumbnail_file->getMimeType();
      }
    }
  }

}

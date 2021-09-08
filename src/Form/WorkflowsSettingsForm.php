<?php

namespace Drupal\localgov_workflows\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class WorkflowsSettingsForm extends ConfigFormBase {

  /**
   * Return the next review date options.
   *
   * @returns string[int]
   *   An array of time frame options indexed by the number of months.
   */
  public static function getNextReviewOptions() {

    return [
      3 => t('3 months'),
      6 => t('6 months'),
      12 => t('1 year'),
      24 => t('2 years'),
      36 => t('3 years'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['localgov_workflows.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'localgov_workflows_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('localgov_workflows.settings');

    // Default content next review time.
    $form['default_next_review'] = [
      '#type' => 'select',
      '#title' => t('Default next review date'),
      '#options' => static::getNextReviewOptions(),
      '#default_value' => $config->get('default_next_review') ?? 12,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('localgov_workflows.settings');
    $config->set('default_next_review', $form_state->getValue('default_next_review'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
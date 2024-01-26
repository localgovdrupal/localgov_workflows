<?php

namespace Drupal\localgov_workflows_notifications\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure LocalGov Workflows Notifications settings for the site.
 */
final class LocalgovWorkflowsNotificationsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'localgov_workflows_notifications_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['localgov_workflows_notifications.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['email_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable email notifications'),
      '#description' => $this->t('Send email notifications to service contacts.'),
      '#default_value' => $this->config('localgov_workflows_notifications.settings')->get('email_enabled') ?? TRUE,
    ];
    $form['email_frequency'] = [
      '#type' => 'number',
      '#title' => $this->t('Email frequency (days)'),
      '#description' => $this->t('How often to send notifications to users via email.'),
      '#default_value' => $this->config('localgov_workflows_notifications.settings')->get('email_frequency') ?? 1,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {

    $this->config('localgov_workflows_notifications.settings')
      ->set('email_enabled', $form_state->getValue('email_enabled'))
      ->set('email_frequency', $form_state->getValue('email_frequency'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}

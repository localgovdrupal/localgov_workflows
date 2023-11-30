<?php declare(strict_types = 1);

namespace Drupal\localgov_review_notifications\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the service contact entity edit forms.
 */
final class LocalgovServiceContactForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\localgov_review_notifications\Entity\LocalgovServiceContact */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    // Add conditional logic to ensure either user or name and email are set.
    $form['instructions'] = [
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#markup' => $this->t('Either lookup a Drupal user or enter a name and email address for the service contact.'),
      '#weight' => -10,
    ];
    $form['email']['widget'][0]['value']['#states'] = [
      'enabled' => [
        ':input[name="user[0][target_id]"]' => ['value' => ''],
      ],
      'required' => [
        ':input[name="user[0][target_id]"]' => ['value' => ''],
      ],
    ];
    $form['name']['widget'][0]['value']['#states'] = [
      'enabled' => [
        ':input[name="user[0][target_id]"]' => ['value' => ''],
      ],
      'required' => [
        ':input[name="user[0][target_id]"]' => ['value' => ''],
      ],
    ];
    $form['user']['widget'][0]['target_id']['#states'] = [
      'enabled' => [
        ':input[name="email[0][value]"]' => ['value' => ''],
        'and',
        ':input[name="name[0][value]"]' => ['value' => ''],
      ],
      'required' => [
        ':input[name="email[0][value]"]' => ['value' => ''],
        'and',
        ':input[name="name[0][value]"]' => ['value' => ''],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);


  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->toLink()->toString()];
    $logger_args = [
      '%label' => $this->entity->label(),
      'link' => $this->entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New service contact %label has been created.', $message_args));
        $this->logger('localgov_review_notifications')->notice('New service contact %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The service contact %label has been updated.', $message_args));
        $this->logger('localgov_review_notifications')->notice('The service contact %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $result;
  }

}

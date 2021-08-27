<?php

namespace Drupal\localgov_workflows\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;

/**
 * Defines the Review status entity.
 *
 * @ingroup localgov_workflows
 *
 * @ContentEntityType(
 *   id = "review_status",
 *   label = @Translation("Review status"),
 *   base_table = "review_status",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "author",
 *   },
 *   admin_permission = "administer review status entities",
 * )
 */
class ReviewStatus extends ContentEntityBase implements ReviewStatusInterface {

  /**
   * Workflow state that transition content to on the next review date.
   */
  public const REVIEW_STATE = 'review';

  /**
   * {@inheritdoc}
   */
  public static function newReviewStatus(EntityInterface $entity, ScheduledTransitionInterface $transition, ?AccountInterface $author = NULL, $active = TRUE): ?ReviewStatus {
    if (is_null($author)) {
      $author = \Drupal::currentUser();
    }

    $review_status = static::create();
    $review_status
      ->setEntity($entity)
      ->setScheduledTransition($transition)
      ->setAuthor($author)
      ->setActive($active)
      ->setCreated(time());

    return $review_status;
  }

  /**
   * {@inheritdoc}
   */
  public static function getActiveReviewStatus(EntityInterface $entity): ?ReviewStatus {

    $review_status_storage = \Drupal::entityTypeManager()->getStorage('review_status');
    $review_status = current($review_status_storage->loadByProperties(['entity' => $entity->id(), 'active' => TRUE]));
    if ($review_status instanceof ReviewStatus) {
      return $review_status;
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    return $this->get('active');
  }

  /**
   * {@inheritdoc}
   */
  public function setActive($active) {
    $this->set('active', $active);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthor(): AccountInterface {
    return $this->get('author')->entity;
  }

  /**
   * {@inheritdoc}
   */
  protected function setAuthor(AccountInterface $author) {
    $this->set('author', $author->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreated() {
    return $this->get('created');
  }

  /**
   * {@inheritdoc}
   */
  protected function setCreated($created) {
    $this->set('created', $created);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity(): EntityInterface {
    return $this->get('entity')->entity;
  }

  /**
   * {@inheritdoc}
   */
  protected function setEntity(EntityInterface $entity) {
    $this->set('entity', $entity->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getScheduledTransition(): ?ScheduledTransitionInterface {
    return $this->get('transition')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setScheduledTransition(ScheduledTransitionInterface $transition) {
    $this->set('transition', $transition->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {

    // There should only be one active review status for any given entity.
    if ($this->isActive()) {
      $storage = \Drupal::entityTypeManager()->getStorage('review_status');
      $active = $storage->loadByProperties([
        'entity' => $this->getEntity()->id(),
        'active' => TRUE
      ]);
      foreach ($active as $review_status) {
        if ($review_status->id() !== $this->id()) {
          $review_status->setActive(FALSE);
          $review_status->save();
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['active'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active'))
      ->setDescription(t('The current active review status for the given entity.'));

    $fields['author'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(\t('Author'))
      ->setDescription(\t('The user who created the review status.'))
      ->setSetting('target_type', 'user');

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['entity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity'))
      ->setDescription(t('The entity that has been reviewed.'))
      ->setSetting('target_type', 'node');

    $fields['transition'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Review scheduled transition'))
      ->setDescription(t('The scheduled transition that will run on review date.'))
      ->setSetting('target_type', 'scheduled_transition')
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ]);

    return $fields;
  }

}

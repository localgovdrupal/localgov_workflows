<?php

namespace Drupal\localgov_workflows\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\localgov_workflows\Entity\ReviewStatus;
use Drupal\scheduled_transitions\Entity\ScheduledTransition;
use Drupal\workflows\Entity\Workflow;

/**
 * Review status field.
 *
 * @FieldType(
 *   id = "review_status",
 *   label = @Translation("Review Status"),
 *   description = @Translation("An entity field containing a review status."),
 *   no_ui = TRUE,
 *   default_widget = "review_status",
 * )
 */
class ReviewStatusItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['reviewed'] = DataDefinition::create('boolean')
      ->setLabel(t('Reviewed'));
    $properties['next_review'] = DataDefinition::create('string')
      ->setLabel(t('Next review date'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return ($this->reviewed === NULL || $this->reviewed === '') && ($this->next_review === NULL || $this->next_review === '');
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {

  }

  /**
   * {@inheritdoc}
   */
  public function postSave($update) {
    if ($this->reviewed) {

      // Content has been flagged as reviewed so ensure the review status and
      // scheduled transition entities exist.
      $entity = $this->getEntity();
      $active_review_status = ReviewStatus::getActiveReviewStatus($entity);

      if ($active_review_status) {

        // If there's scheduled transition just update the date, otherwise
        // create a new one.
        $scheduled_transition = $active_review_status->getScheduledTransition();
        if ($scheduled_transition) {
          $next_review = strtotime('+' . $this->next_review . ' months');
          $scheduled_transition->setTransitionTime($next_review);
          $scheduled_transition->save();
        }
        else {
          $scheduled_transition = $this->createScheduledTransition();
        }

        // Create a new review status.
        $review_status = ReviewStatus::newReviewStatus($entity, $scheduled_transition);
        $review_status->save();

      }
      else {

        // No current review status so create a new one with associated
        // scheduled transition.
        $scheduled_transition = $this->createScheduledTransition();
        $review_status = ReviewStatus::newReviewStatus($entity, $scheduled_transition);
        $review_status->save();
      }
    }
  }

  /**
   * Create a new scheduled transition for the entity.
   *
   * @returns ScheduledTransition
   *   The newly created scheduled transition.
   */
  protected function createScheduledTransition() {

    $entity = $this->getEntity();
    $workflow = Workflow::load('localgov_editorial');
    $current_user = \Drupal::currentUser()->id();
    $review_date = strtotime('+' . $this->next_review . ' months');
    $options = [
      ScheduledTransition::OPTION_LATEST_REVISION => TRUE,
    ];

    $scheduled_transition = ScheduledTransition::create([
      'entity' => $entity,
      'entity_revision_id' => 0,
      'entity_revision_langcode' => $entity->language(),
      'author' => $current_user,
      'workflow' => $workflow->id(),
      'moderation_state' => ReviewStatus::REVIEW_STATE,
      'transition_on' => $review_date,
      'options' => [
        $options,
      ],
    ]);
    $scheduled_transition->save();

    return $scheduled_transition;
  }

}

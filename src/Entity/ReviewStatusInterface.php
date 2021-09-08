<?php

namespace Drupal\localgov_workflows\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;

/**
 * Provides an interface for defining Review status entities.
 *
 * @ingroup localgov_content_review
 */
interface ReviewStatusInterface extends ContentEntityInterface {

  /**
   * Create a new ReviewStatus entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity that has been reviewed.
   * @param \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface $transition
   *   ScheduledTransition entity for when the content needs reviewing.
   * @param \Drupal\Core\Session\AccountInterface|null $author
   *   Account of user reviewing the content. Defaults to current user.
   * @param bool $current
   *   Is this the current revision. Defaults to TRUE.
   *
   * @return ReviewStatus
   *   The newly created ReviewStatus instance.
   */
  public static function newReviewStatus(EntityInterface $entity, ScheduledTransitionInterface $transition, ?AccountInterface $author = NULL, $current = TRUE);

  /**
   * Gets the active ReviewStatus for the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that has been reviewed.
   *
   * @return \Drupal\localgov_workflows\Entity\ReviewStatus|null
   */
  public static function getActiveReviewStatus(EntityInterface $entity): ?ReviewStatus;

  /**
   * Is this the current active review status.
   *
   * @return boolean
   */
  public function isActive(): bool;

  /**
   * Gets the user who reviewed the content.
   *
   * @return \Drupal\Core\Session\AccountInterface
   */
  public function getAuthor(): AccountInterface;

  /**
   * Gets the timestamp when the content was reviewed.
   *
   * @return int
   */
  public function getCreatedTime(): int;

  /**
   * Gets the entity that has been reviewed.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   */
  public function getEntity(): EntityInterface;

  /**
   * Gets the timestamp when the content is next due to be reviewed.
   *
   * @return int
   */
  public function getReviewTime(): int;

  /**
   * Gets the scheduled transition entity that moves the content to review.]
   *
   * @return \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface|null
   */
  public function getScheduledTransition(): ?ScheduledTransitionInterface;

  /**
   * Sets the scheduled transition entity associated with reviewing the content.
   *
   * @param \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface $transition
   *
   * @return \Drupal\localgov_workflows\Entity\ReviewStatus
   */
  public function setScheduledTransition(ScheduledTransitionInterface $transition);
}

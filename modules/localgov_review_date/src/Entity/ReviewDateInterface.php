<?php

namespace Drupal\localgov_review_date\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;

/**
 * Provides an interface for defining Review status entities.
 *
 * @ingroup localgov_content_review
 */
interface ReviewDateInterface extends ContentEntityInterface {

  /**
   * Create a new ReviewDate entity.
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
   * @return ReviewDate
   *   The newly created ReviewDate instance.
   */
  public static function newReviewDate(EntityInterface $entity, ScheduledTransitionInterface $transition, ?AccountInterface $author = NULL, $current = TRUE): ReviewDate;

  /**
   * Gets the active ReviewDate for the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that has been reviewed.
   *
   * @return \Drupal\localgov_review_date\Entity\ReviewDate|null
   *   The status.
   */
  public static function getActiveReviewDate(EntityInterface $entity): ?ReviewDate;

  /**
   * Is this the current active review date.
   *
   * @return bool
   *   Boolean, TRUE if active.
   */
  public function isActive(): bool;

  /**
   * Gets the user who reviewed the content.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   The user account.
   */
  public function getAuthor(): AccountInterface;

  /**
   * Gets the timestamp when the content was reviewed.
   *
   * @return int
   *   The timestamp.
   */
  public function getCreatedTime(): int;

  /**
   * Gets the entity that has been reviewed.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The content entity that has been reviewed.
   */
  public function getEntity(): EntityInterface;

  /**
   * Gets the timestamp when the content is next due to be reviewed.
   *
   * @return int
   *   The timestamp.
   */
  public function getReviewTime(): int;

  /**
   * Gets the scheduled transition entity that moves the content to review.
   *
   * @return \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface|null
   *   The scheduled transition entity.
   */
  public function getScheduledTransition(): ?ScheduledTransitionInterface;

  /**
   * Sets the scheduled transition entity associated with reviewing the content.
   *
   * @param \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface $transition
   *   The scheduled transition entity.
   *
   * @return \Drupal\localgov_review_date\Entity\ReviewDate
   *   The review status.
   */
  public function setScheduledTransition(ScheduledTransitionInterface $transition);

}

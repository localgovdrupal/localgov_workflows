<?php

namespace Drupal\localgov_workflows_notifications;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for WorkflowNotification.
 */
interface WorkflowNotificationInterface {

  /**
   * Enqueue a notification to be sent.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to send a notification for.
   * @param string $type
   *   The type of notification.
   * @param string $method
   *   The notification method. Only 'email' is supported at this time.
   */
  public function enqueue(ContentEntityInterface $entity, string $type, string $method = 'email'): void;

}

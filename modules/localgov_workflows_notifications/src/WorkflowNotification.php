<?php

namespace Drupal\localgov_workflows_notifications;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Queue\QueueFactory;

/**
 * Enqueue a notification to be sent.
 */
class WorkflowNotification implements WorkflowNotificationInterface {

  /**
   * Prefix of queue to add notifications to.
   *
   * This is combined with the notification method to create the queue name.
   *
   * @var string
   */
  const QUEUE_PREFIX = 'localgov_workflows_notifications_';

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected QueueFactory $queueFactory;

  /**
   * Constructs a WorkflowNotification object.
   */
  public function __construct(QueueFactory $queue_factory) {
    $this->queueFactory = $queue_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function enqueue(ContentEntityInterface $entity, string $type, string $method = 'email'): void {

    // Only email notifications are supported at this time.
    if ($method !== 'email') {
      throw new \Exception('Only email notifications are supported at this time.');
    }

    $queue_name = self::QUEUE_PREFIX . $method;
    $queue = $this->queueFactory->get($queue_name);

    // Add notifications to service contacts to queue.
    $service_contacts = $entity->get('localgov_service_contacts')->referencedEntities();
    if (!empty($service_contacts)) {
      foreach ($service_contacts as $contact) {

        // Ensure the queue contains only one item for per service contact.
        $found = FALSE;
        while (!$found && $item = $queue->claimItem(1)) {
          if ($item->data['service_contact'] == $contact->id() && $item->data['type'] == $type) {
            $item->data['entities'][] = [
              'entity_id' => $entity->id(),
              'entity_type' => $entity->getEntityTypeId(),
            ];
            $found = TRUE;
          }
          $item->releaseItem($item);
        }

        if (!$found) {
          $item = new \stdClass();
          $item->entities[] = [
            'entity_id' => $entity->id(),
            'entity_type' => $entity->getEntityTypeId(),
          ];
          $item->service_contact = $contact->id();
          $item->type = $type;
          $queue->createItem($item);
        }
      }
    }
  }

}

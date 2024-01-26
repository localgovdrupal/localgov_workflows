<?php

namespace Drupal\localgov_workflows_notifications\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\symfony_mailer\EmailFactoryInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines 'localgov_workflows_notifications_email' queue worker.
 *
 * @QueueWorker(
 *   id = "localgov_workflows_notifications_email",
 *   title = @Translation("Email queue worker"),
 *   cron = {"time" = 0}
 * )
 */
class EmailNotificationQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The email factory service.
   *
   * @var \Drupal\symfony_mailer\EmailFactoryInterface
   */
  protected EmailFactoryInterface $emailFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new EmailNotificationQueueWorker instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EmailFactoryInterface $email_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->emailFactory = $email_factory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('email_factory'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data): void {

    $type = $data->type;
    $entities = [];
    foreach ($data->entities as $entity) {
      $entities[] = $this->entityTypeManager->getStorage($entity['entity_type'])->load($entity['entity_id']);
    }
    $service_contact = $this->entityTypeManager->getStorage('localgov_service_contact')->load($data->service_contact);

    try {
      $email = $this->emailFactory->sendTypedEmail('localgov_workflows_notifications', $type, $service_contact, $entities);
      \Drupal::logger('localgov_workflows_notifications')
        ->notice('Sent email @type notification to @contact', [
          '@type' => $type,
          '@contact' => $service_contact->label(),
        ]);
    }
    catch (\Exception $e) {
      print_r($e);
      \Drupal::logger('localgov_workflows_notifications')
        ->error('Failed to send email @type notification to @contact: @error', [
          '@type' => $type,
          '@contact' => $service_contact->label(),
          '@error' => $e->getMessage(),
        ]);
    }

  }

}

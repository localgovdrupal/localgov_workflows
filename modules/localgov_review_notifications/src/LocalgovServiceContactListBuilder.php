<?php declare(strict_types = 1);

namespace Drupal\localgov_review_notifications;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the service contact entity type.
 */
final class LocalgovServiceContactListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['user'] = $this->t('Drupal user');
    $header['name'] = $this->t('Service contact name');
    $header['email'] = $this->t('Service contact email');
    $header['enabled'] = $this->t('Notifications');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\localgov_review_notifications\Entity\LocalgovServiceContactInterface $entity */
    $row['user'] = $entity->get('user')->value;
    $row['name'] = $entity->get('name')->value;
    $row['email'] = $entity->get('email')->value;
    $row['enabled'] = $entity->get('enabled')->value ? $this->t('Enabled') : $this->t('Disabled');
    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

}

<?php

namespace Drupal\Tests\localgov_review_date\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Tests loading and storing data using the ReviewSDate entity.
 *
 * @group path
 */
class ReviewDateEntityTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'content_moderation',
    'dynamic_entity_reference',
    'field',
    'filter',
    'node',
    'scheduled_transitions',
    'system',
    'text',
    'user',
    'views',
    'workflows',
    'localgov_workflows',
    'localgov_review_date'.
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setup();

    $this->installEntitySchema('content_moderation_state');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('scheduled_transition');
    $this->installEntitySchema('workflow');
    $this->installEntitySchema('review_date');
    $this->installSchema('node', ['node_access']);
    $this->installConfig([
      'content_moderation',
      'filter',
      'node',
      'scheduled_transitions',
      'system',
      'views',
      'localgov_workflows',
    ]);
  }

  /**
   * Tests creating, loading, updating and deleting aliases through PathItem.
   */
  public function testPathItem() {

  }

}

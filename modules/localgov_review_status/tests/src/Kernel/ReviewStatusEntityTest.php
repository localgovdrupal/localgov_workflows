<?php

namespace Drupal\Tests\localgov_review_status\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Tests loading and storing data using ReviewStatusItem.
 *
 * @group path
 */
class ReviewStatusEntityTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'user',
    'content_translation',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('path_alias');

    $this->installSchema('node', ['node_access']);

    $node_type = NodeType::create(['type' => 'foo']);
    $node_type->save();

    $this->installConfig(['language']);
    ConfigurableLanguage::createFromLangcode('de')->save();
  }

  /**
   * Tests creating, loading, updating and deleting aliases through PathItem.
   */
  public function testPathItem() {

  }

}

<?php

namespace Drupal\Tests\localgov_review_status\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\workflows\Entity\Workflow;

/**
 * Tests the Review Status node form UI.
 *
 * @group path
 */
class NodeFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'testing';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'localgov_review_status',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a page content type.
    $this->drupalCreateContentType(['type' => 'page', 'name' => 'Basic page']);

    // Add page to localgov_editorial workflow.
    $editorial = Workflow::load('localgov_editorial');
    $type = $editorial->getTypePlugin();
    $type->addEntityTypeAndBundle('node', 'page');
    $editorial->save();

    // Create test user and log in.
    $web_user = $this->drupalCreateUser([
      'administer scheduled transitions',
      'create page content',
    ]);
    $this->drupalLogin($web_user);
  }

  /**
   * Tests the node form ui.
   */
  public function testNodeForm() {
    $assert_session = $this->assertSession();

    // Check review status widget doesn't display if schedule transitions are
    // not configured.
    $this->drupalGet('node/add/page');
    $assert_session->elementNotExists('css', '.review-status-form');
    $assert_session->fieldNotExists('localgov_review_date[0][reviewed]');

    // Configure scheduled transitions to work with page.
    $this->drupalGet('admin/config/workflow/scheduled-transitions');
    $edit = [
      'enabled[node:page]' => TRUE,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Check review status now displays when adding a page.
    $this->drupalGet('node/add/page');
    $assert_session->elementContains('css', '.review-status-form summary', 'Review date');
    $assert_session->fieldExists('localgov_review_date[0][reviewed]');
    $assert_session->fieldExists('localgov_review_date[0][review][review_in]');
    $assert_session->fieldExists('localgov_review_date[0][review][review_date]');

    // Check review status widget can be disabled on a content type.
    \Drupal::service('entity_display.repository')->getFormDisplay('node', 'page', 'default')
      ->removeComponent('localgov_review_date')
      ->save();
    $this->drupalGet('node/add/page');
    $assert_session->elementNotExists('css', '.review-status-form');
    $assert_session->fieldNotExists('localgov_review_date[0][reviewed]');
  }

}

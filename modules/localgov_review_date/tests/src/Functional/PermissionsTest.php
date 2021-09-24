<?php

namespace Drupal\Tests\localgov_review_date\Functional;

use Drupal\localgov_roles\RolesHelper;
use Drupal\node\NodeInterface;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\workflows\Entity\Workflow;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the permissions for review statuses.
 */
class PermissionsTest extends BrowserTestBase {

  use NodeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'localgov_review_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'localgov';

  /**
   * Example page to test access to.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected NodeInterface $page;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->page = $this->createNode([
      'type' => 'localgov_services_page',
      'title' => 'Test service page',
      'body' => [
        'summary' => 'Test service page summary',
        'value' => 'Test service page text',
      ],
    ]);
  }

  /**
   * Test the alert banner user access permissions.
   */
  public function testUserAccess() {

    // Check editor can access and save nodes with review status field.
    $this->reviewPage(RolesHelper::EDITOR_ROLE, date('Y-m-d', strtotime('+3 months')));
    $this->reviewPage(RolesHelper::AUTHOR_ROLE, date('Y-m-d', strtotime('+6 months')));
    $this->reviewPage(RolesHelper::CONTRIBUTOR_ROLE, date('Y-m-d', strtotime('+9 months')));
  }

  /**
   * Check user can access and save node with review status.
   *
   * @param string $role
   *   User role to test editing with.
   * @param string $date
   *   Date to set as next review in 'Y-m-d' format.
   */
  protected function reviewPage($role, $date) {
    $assert_session = $this->assertSession();

    // Create user with role.
    $user = $this->createUser();
    $user->addRole($role);
    $user->save();
    $this->drupalLogin($user);

    // Check page editing.
    $this->drupalGet('node/add/localgov_services_page');
    $assert_session->statusCodeEquals(Response::HTTP_OK);
    $assert_session->elementExists('css', '.review-date-form');
//    $edit = [
//      'localgov_review_date[0][reviewed]' => TRUE,
//      'localgov_review_date[0][review][review_date]' => $date,
//    ];
//    $this->submitForm($edit, 'Save');
//    $assert_session->pageTextContains('has been updated');
//    print_r($this->getSession()->getPage()->getHtml());
//    $this->drupalGet('node/' . $this->page->id() . '/edit');
//    $assert_session->fieldValueEquals('next_review', $date);

    $this->drupalLogout();
  }

}

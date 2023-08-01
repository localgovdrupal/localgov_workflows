<?php

/**
 * @file
 * Post update hooks functions for the LocalGov Workflow module.
 */

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\FileStorage;
use Drupal\scheduled_transitions\Form\ScheduledTransitionsSettingsForm;
use Drupal\workflows\Entity\Workflow;

/**
 * Implements hook_install().
 */
function localgov_workflows_post_update_workflow(&$sandbox) {

  // Configure workflow for localgov_ node bundles with no other workflow.
  $editorial = Workflow::load('localgov_editorial');
  if (!$editorial) {
    $config_key = 'workflows.workflow.localgov_editorial';
    // If workflow doesn't already exist load it from this module.
    $config_file_path = \Drupal::service('extension.path.resolver')->getPath('module', 'localgov_workflows') . '/config/install';
    $config_source = new FileStorage($config_file_path);
    $editorial_config = $config_source->read($config_key);

    \Drupal::entityTypeManager()->getStorage('workflow')
      ->create($editorial_config)
      ->save();
    $editorial = Workflow::load('localgov_editorial');
  }

  $workflow_type = $editorial->getTypePlugin();
  $node_types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('node');
  $changed = FALSE;
  foreach ($node_types as $type_name => $node_type) {
    if (strpos($type_name, 'localgov_') === 0 && empty($node_type['workflow'])) {

      // Add workflow.
      $workflow_type->addEntityTypeAndBundle('node', $type_name);
      $changed = TRUE;
    }
  }

  if ($changed) {
    $editorial->save();
  }

  // Enable scheduled transitions for all node bundles.
  $scheduled_transitions_config = \Drupal::service('config.factory')->getEditable('scheduled_transitions.settings');
  $bundles = $scheduled_transitions_config->get('bundles') ?? [];
  foreach ($node_types as $bundle => $node_type) {
    if (empty($bundles) || !in_array($bundle, array_column($bundles, 'bundle'))) {
      $bundles[] = [
        'entity_type' => 'node',
        'bundle' => $bundle,
      ];
    }
  }
  $scheduled_transitions_config->set('bundles', $bundles);
  $scheduled_transitions_config->save();
  Cache::invalidateTags([
    ScheduledTransitionsSettingsForm::SETTINGS_TAG,
    'config:scheduled_transitions.settings',
  ]);

  // Ensure contributor role exists.
  localgov_workflows_update_9001();

  // Use the admin theme when checking diffs.
  if (\Drupal::moduleHandler()->moduleExists('diff')) {
    $config = \Drupal::configFactory()->getEditable('diff.settings');
    $settings = $config->get('general_settings');
    $settings['visual_inline_theme'] = 'admin';
    $config->set('general_settings', $settings);
    $config->save();
  }

  // Reload user permissions defined in hook_localgov_roles_default().
  $roles = \Drupal::service('module_handler')->invokeAll('localgov_roles_default');
  foreach ($roles as $role => $permissions) {
    user_role_grant_permissions($role, $permissions);
  }

}


<?php

namespace Drupal\localgov_workflows\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\localgov_workflows\Form\WorkflowsSettingsForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'review_status' widget.
 *
 * @FieldWidget(
 *   id = "review_status",
 *   label = @Translation("Review status"),
 *   description = @Translation("Review status widget"),
 *   field_types = {
 *     "review_status",
 *   },
 * )
 */
class ReviewStatusWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, ConfigFactory $config_factory) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('localgov_workflows.settings');

    $element['reviewed'] = [
      '#type' => 'checkbox',
      '#title' => 'Content reviewed',
      '#description' => $this->t('I have reviewed this content.'),
      '#default' => FALSE,
    ];
    $element['next_review'] = [
      '#type' => 'select',
      '#title' => $this->t('Next review in'),
      '#description' => $this->t('When is this content next due to be reviewed.'),
      '#options' => WorkflowsSettingsForm::getNextReviewOptions(),
      '#default_value' => $config->get('default_next_review') ?? 12,
    ];

    // Add to advanced settings.
    if (isset($form['advanced'])) {
      $element += [
        '#type' => 'details',
        '#title' => $this->t('Review status'),
        '#group' => 'advanced',
      ];
      $element['#weight'] = -5;
    }

    return $element;
  }

}

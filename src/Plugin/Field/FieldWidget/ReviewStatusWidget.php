<?php

namespace Drupal\localgov_workflows\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\localgov_workflows\Entity\ReviewStatus;
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

    // Get current review status object.
    $entity = $items->getEntity();
    $review_status = ReviewStatus::getActiveReviewStatus($entity);

    // Calculate next review date.
    $config = $this->configFactory->get('localgov_workflows.settings');
    $next_review = $config->get('default_next_review') ?? 12;
    $review_date = strtotime('+' . $next_review . ' months');

    // Add form items.
    $element['reviewed'] = [
      '#type' => 'checkbox',
      '#title' => 'Content reviewed',
      '#description' => $this->t('I have reviewed this content.'),
      '#default' => FALSE,
      '#attributes' => [
        'name' => 'review_status_reviewed',
      ],
    ];
    $element['review'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          ':input[name="review_status_reviewed"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];
    $element['review']['review_in'] = [
      '#type' => 'select',
      '#title' => $this->t('Next review in'),
      '#options' => WorkflowsSettingsForm::getNextReviewOptions(),
      '#default_value' => $next_review,
      '#attributes' => [
        'class' => ['review-status-review-in'],
      ],
    ];
    $element['review']['review_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Review date'),
      '#description' => $this->t('When is this content next due to be reviewed.'),
      '#default_value' => date('Y-m-d', $review_date),
      '#attributes' => [
        'class' => ['review-status-review-date'],
      ],
    ];
    $form['last_review'] = [
      '#type' => 'hidden',
      '#value' => is_null($review_status) ? '' : date('Y-m-d', $review_status->getCreatedTime()),
      '#attributes' => [
        'class' => ['review-status-last-review'],
      ],
    ];
    $form['next_review'] = [
      '#type' => 'hidden',
      '#value' => is_null($review_status) ? '' : date('Y-m-d', $review_status->getReviewTime()),
      '#attributes' => [
        'class' => ['review-status-next-review'],
      ],
    ];

    // Add to advanced settings.
    if (isset($form['advanced'])) {
      $element += [
        '#type' => 'details',
        '#title' => $this->t('Review status'),
        '#group' => 'advanced',
        '#attributes' => [
          'class' => ['review-status-form'],
        ],
        '#attached' => [
          'library' => ['localgov_workflows/localgov_workflows.review_status'],
        ],
      ];
      $element['#weight'] = -5;
    }

    return $element;
  }

}

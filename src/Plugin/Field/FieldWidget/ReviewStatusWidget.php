<?php

namespace Drupal\localgov_workflows\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

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
class ReviewStatusWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['reviewed'] = [
      '#type' => 'checkbox',
      '#title' => 'Content reviewed',
      '#description' => t('I have reviewed this content.'),
      '#default' => FALSE,
    ];
    $element['next_review'] = [
      '#type' => 'select',
      '#title' => t('Next review in'),
      '#description' => t('When is this content next due to be reviewed.'),
      '#options' => [
        3 => t('3 months'),
        6 => t('6 months'),
        12 => t('1 year'),
        24 => t('2 years'),
        36 => t('3 years'),
      ],
      '#default_value' => 12,
    ];

    // Add to advanced settings.
    if (isset($form['advanced'])) {
      $element += [
        '#type' => 'details',
        '#title' => t('Review status'),
        '#group' => 'advanced',
      ];
      $element['#weight'] = -5;
    }

    return $element;
  }
}

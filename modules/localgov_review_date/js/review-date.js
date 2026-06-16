/**
 * @file
 * Defines JavaScript behaviors for the review date widget.
 */
(function reviewDatesScript(Drupal) {
  /**
   * Show review date summary on node edit form.
   */
  Drupal.behaviors.ReviewDateSummary = {
    attach: (context) => {
      const reviewDateForms = once(
        'allReviewDateForms',
        '.review-date-form',
        context,
      );

      reviewDateForms.forEach((form) => {
        const summary = form.querySelector('summary .claro-details__summary-summary');
        const lastReview = form.querySelector(
          '.review-date-last-review',
        )?.value;
        const nextReview = form.querySelector(
          '.review-date-next-review',
        )?.value;

        if (lastReview && nextReview) {
          summary.innerHTML = Drupal.t(
            'Last reviewed on @last<br>Next review on @next',
            {
              '@last': lastReview,
              '@next': nextReview,
            },
          );
        } else {
          summary.innerHTML = Drupal.t('Not reviewed yet');
        }
      });
    },
  };

  /**
   * Update review date when next review date select changes.
   */
  Drupal.behaviors.ReviewDateNextReviewSelect = {
    attach: (context) => {
      const reviewInSelects = context.querySelectorAll(
        '.review-date-review-in',
      );

      reviewInSelects.forEach((select) => {
        select.addEventListener('change', () => {
          const reviewIn = parseInt(select.value, 10);
          const today = new Date();
          const reviewDate = new Date(
            today.setMonth(today.getMonth() + reviewIn),
          );

          const reviewDateField = context.querySelector(
            '.review-date-review-date',
          );
          if (reviewDateField) {
            reviewDateField.value = reviewDate.toISOString().slice(0, 10);
          }
        });
      });
    },
  };

  /**
   * Set content reviewed if content moderation state set to published.
   */
  Drupal.behaviors.ReviewDateSetReviewed = {
    attach: (context) => {
      const moderationStateField = context.querySelector(
        '#edit-moderation-state-0-state',
      );

      if (moderationStateField) {
        moderationStateField.addEventListener('change', () => {
          const moderationState = moderationStateField.value;
          if (moderationState === 'published') {
            const reviewedField = context.querySelector(
              '.review-date-reviewed',
            );
            if (reviewedField) {
              reviewedField.checked = true;
              reviewedField.dispatchEvent(
                new Event('change', { bubbles: true }),
              );
            }
          }
        });
      }
    },
  };
})(Drupal);

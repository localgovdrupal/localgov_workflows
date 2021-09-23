/**
 * @file
 * Defines JavaScript behaviors for the review status widget.
 */
(function ($, Drupal, drupalSettings) {

  /**
   * Show review status summary on node edit form.
   */
  Drupal.behaviors.ReviewStatusSummary = {
    attach: function attach(context) {
      const $context = $(context);
      $context.find('.review-status-form').drupalSetSummary(function (context) {
        const lastReview = $('.review-status-last-review').val();
        const nextReview = $('.review-status-next-review').val();

        if (lastReview && nextReview) {
          return Drupal.t('Last reviewed on @last<br>Next review on @next', {
              '@last': lastReview,
              '@next': nextReview,
            }
          );
        }

        return Drupal.t('Not reviewed yet');
      });
    }
  };

  /**
   * Update review date when next review date select changes.
   */
  Drupal.behaviors.ReviewStatusNextReviewSelect = {
    attach: function attach(context) {
      $('.review-status-review-in').change(function() {
        const reviewIn = parseInt($('.review-status-review-in').val());
        let today = new Date();
        const reviewDate = new Date(today.setMonth(today.getMonth() + reviewIn));

        $('.review-status-review-date').val(reviewDate.toISOString().slice(0, 10));
      });
    }
  };

  /**
   * Set content reviewed if content moderation state set to published.
   */
  Drupal.behaviors.ReviewStatusSetReviewed = {
    attach: function attach(context) {
      $('#edit-moderation-state-0-state').change(function() {
        const moderation_state = $('#edit-moderation-state-0-state').val();
        if (moderation_state === 'published') {
          const reviewed = $('.review-status-reviewed');
          reviewed.prop('checked', true);
          reviewed.trigger('change');
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);

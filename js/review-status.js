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
      var $context = $(context);
      $context.find('.review-status-form').drupalSetSummary(function (context) {
        var lastReview = $('.review-status-last-review').val();
        var nextReview = $('.review-status-next-review').val();

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
        var reviewIn = parseInt($('.review-status-review-in').val());
        var today = new Date();
        var reviewDate = new Date(today.setMonth(today.getMonth() + reviewIn));

        $('.review-status-review-date').val(reviewDate.toISOString().slice(0, 10));
      });
    }
  };

})(jQuery, Drupal, drupalSettings);

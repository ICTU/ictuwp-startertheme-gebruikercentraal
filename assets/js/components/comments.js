(function(jQuery) {

  jQuery(document).ready(function() {
    jQuery('.comment-reply-link').click(function () {
      // noteren op welke reactie we reageren
      jQuery('input[name="comment_parent"]').val(jQuery(this).data("commentid"));
      // we veranderen de titel boven het reactieformulier
      jQuery('#reply-title').text( jQuery(this).attr("aria-label") );
      // effe de animatie uitzetten
      /*
      jQuery('html, body').animate({
        scrollTop: jQuery(".comment-form").offset().top
      }, 2000);
       */
    });
  });

})( jQuery );


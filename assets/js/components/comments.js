(function($) {

  $(document).ready(function() {
    $('.comment-reply-link').click(function () {
      // noteren op welke reactie we reageren
      $('input[name="comment_parent"]').val($(this).data("commentid"));
      // we veranderen de titel boven het reactieformulier
      $('#reply-title').text( $(this).attr("aria-label") );
      // effe de animatie uitzetten
      /*
      $('html, body').animate({
        scrollTop: $(".comment-form").offset().top
      }, 2000);
       */
    });
  });

})( jQuery );


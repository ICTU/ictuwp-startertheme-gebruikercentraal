(function($) {

  $(document).ready(function() {
    $('.comment-reply-link').click(function () {
      $('input[name="comment_parent"]').val($(this).data("commentid"));
      $('html, body').animate({
        scrollTop: $(".comment-form").offset().top
      }, 2000);
    });
  });

})( jQuery );

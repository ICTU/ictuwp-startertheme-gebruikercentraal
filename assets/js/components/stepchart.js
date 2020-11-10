var setFocus = false;
var breakpoint_desktop = 560; // breakpoint for desktop styling, in pixels
var closeBtn = $('.btn--close')

// On hover or click set popover.
$('.stepchart__button').on('focus', function (e) {

  setFocus = true;
  var chartDescr = $(this).parent().find('.stepchart__description');
  setPopover(chartDescr);

}).on('click', function (e) {
  // Only set if element has no focus to prevent triggering twice (focus / click)
  if (setFocus === false) {
    var chartDescr = $(this).parent().find('.stepchart__description');
    setPopover(chartDescr);
  }

  setFocus = false;
});


var setPopover = function (popover) {
  var windowWidth = $(window).width();

  if (popover.attr('aria-hidden') === 'true') {
    // If bigger then desktop remove focus from other popovers
    if (windowWidth >= breakpoint_desktop) {
      $('.stepchart__description[aria-hidden=false]').attr('aria-hidden', 'true');
      $('.show-popover').removeClass('show-popover')
    }
    popover.attr('aria-hidden', 'false');
    popover.parent().addClass('show-popover');

  } else {
    popover.attr('aria-hidden', 'true');
    popover.parent().removeClass('show-popover');
  }
}

closeBtn.on('click', function(){
  console.log('clickc');

  $(this).parent().attr('aria-hidden', 'true');
});


// Remove all popups when we are on desktop
$(window).resize(function () {
  var windowWidth = $(window).width();

  if (windowWidth >= breakpoint_desktop) {

    $('.stepchart__description[aria-hidden=false]').attr('aria-hidden', 'true');
    $('.show-popover').removeClass('show-popover')
  }
});

// CLose when clicking outside of popover
$(document).on('mouseup', function (e) {

  var container = $('.stepchart__item.show-popover');

  // if the target of the click isn't the container nor a descendant of the container
  if (!container.is(e.target) && container.has(e.target).length === 0) {
    container.removeClass('show-popover');
    container.find('.stepchart__description').attr('aria-hidden', 'true');
  }

});


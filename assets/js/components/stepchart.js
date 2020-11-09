var setFocus = false;
var breakpoint_desktop = 560; // breakpoint for desktop styling, in pixels
var closeBtn = jQuery('.btn--close')

// On hover or click set popover.
jQuery('.stepchart__button').on('focus', function (e) {

  setFocus = true;
  var chartDescr = jQuery(this).parent().find('.stepchart__description');
  setPopover(chartDescr);

}).on('click', function (e) {
  // Only set if element has no focus to prevent triggering twice (focus / click)
  if (setFocus === false) {
    var chartDescr = jQuery(this).parent().find('.stepchart__description');
    setPopover(chartDescr);
  }

  setFocus = false;
});


var setPopover = function (popover) {
  var windowWidth = jQuery(window).width();

  if (popover.attr('aria-hidden') === 'true') {
    // If bigger then desktop remove focus from other popovers
    if (windowWidth >= breakpoint_desktop) {
      jQuery('.stepchart__description[aria-hidden=false]').attr('aria-hidden', 'true');
      jQuery('.show-popover').removeClass('show-popover')
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

  jQuery(this).parent().attr('aria-hidden', 'true');
});


// Remove all popups when we are on desktop
jQuery(window).resize(function () {
  var windowWidth = jQuery(window).width();

  if (windowWidth >= breakpoint_desktop) {

    jQuery('.stepchart__description[aria-hidden=false]').attr('aria-hidden', 'true');
    jQuery('.show-popover').removeClass('show-popover')
  }
});

// CLose when clicking outside of popover
jQuery(document).on('mouseup', function (e) {

  var container = jQuery('.stepchart__item.show-popover');

  // if the target of the click isn't the container nor a descendant of the container
  if (!container.is(e.target) && container.has(e.target).length === 0) {
    container.removeClass('show-popover');
    container.find('.stepchart__description').attr('aria-hidden', 'true');
  }

});


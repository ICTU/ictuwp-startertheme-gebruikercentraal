//
// Gebruiker Centraal - video.js
// ----------------------------------------------------------------------------------
// Additionele functionaliteit voor rijksvideo plugin
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas
// @desc.   Kleuren in vars/_video.scss


const videoBtn = $('.collapsetoggle button');
const siteContainer = $('.site-container');

videoBtn.on('click', function () {
  const target = $(this).parent().next();

  if ($(this).attr('aria-expanded') === 'true') {
    // find another active if there
    const active = $('.collapsetoggle button[aria-expanded="true"]');
    const expanded = $('.collapsible');

    if (active) {
      active.attr('aria-expanded', 'false');
      expanded.attr('hidden', 'hidden');
    }

    //set active
    $(this).attr('aria-expanded', 'true');
    target.removeAttr('hidden');

    if (!siteContainer.hasClass('show-overlay')) {
      siteContainer.addClass('show-overlay');
    }
  } else {
    $(this).attr('aria-expanded', 'false');
    target.attr('hidden', 'hidden');

    siteContainer.removeClass('show-overlay');
  }
});


// Remove show if click outside container
$(document).on('mouseup', function (e) {

    if (siteContainer.hasClass('show-overlay')) {
      const video = $('.video__video');
      const active = $('.collapsetoggle button[aria-expanded="true"]');
      const expanded = $('.collapsible');

      // if the target of the click isn't the container nor a descendant of the container
      if (!video.is(e.target) && video.has(e.target).length === 0) {
        siteContainer.removeClass('show-overlay');
        active.attr('aria-expanded', 'false');
        expanded.attr('hidden', 'hidden');
      }
    }
  }
);
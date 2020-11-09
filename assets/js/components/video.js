//
// Gebruiker Centraal - video.js
// ----------------------------------------------------------------------------------
// Additionele functionaliteit voor rijksvideo plugin
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas
// @desc.   Kleuren in vars/_video.scss


const videoBtn = jQuery('.collapsetoggle button');
const siteContainer = jQuery('.site-container');

videoBtn.on('click', function () {
  const target = jQuery(this).parent().next();

  if (jQuery(this).attr('aria-expanded') === 'true') {
    // find another active if there
    const active = jQuery('.collapsetoggle button[aria-expanded="true"]');
    const expanded = jQuery('.collapsible');

    if (active) {
      active.attr('aria-expanded', 'false');
      expanded.attr('hidden', 'hidden');
    }

    //set active
    jQuery(this).attr('aria-expanded', 'true');
    target.removeAttr('hidden');

    if (!siteContainer.hasClass('show-overlay')) {
      siteContainer.addClass('show-overlay');
    }
  } else {
    jQuery(this).attr('aria-expanded', 'false');
    target.attr('hidden', 'hidden');

    siteContainer.removeClass('show-overlay');
  }
});


// Remove show if click outside container
jQuery(document).on('mouseup', function (e) {

    if (siteContainer.hasClass('show-overlay')) {
      const video = jQuery('.video__video');
      const active = jQuery('.collapsetoggle button[aria-expanded="true"]');
      const expanded = jQuery('.collapsible');

      // if the target of the click isn't the container nor a descendant of the container
      if (!video.is(e.target) && video.has(e.target).length === 0) {
        siteContainer.removeClass('show-overlay');
        active.attr('aria-expanded', 'false');
        expanded.attr('hidden', 'hidden');
      }
    }
  }
);


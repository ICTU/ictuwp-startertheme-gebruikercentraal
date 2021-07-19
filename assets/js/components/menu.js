//
// Gebruiker Centraal - menu.js
// ----------------------------------------------------------------------------------
// Functionaliteit voor tonen / verbergen mobiel menu
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas, Paul van Buuren
// @license GPL-2.0+
// @version 5.0.32
// @link    https://github.com/ICTU/gebruiker-centraal-wordpress-theme

const breakpointNav = 1000;
const btnToggleMenu = $('.btn--toggle-menu');
const toggleSub = $('.main-menu__open-sub');
const navContainer = $('.l-header-nav');
const body = $('body');

/*

// =========================================================================================================
*/

function doNav(width) {
  if (width <= 1000) {
    // Mobile
    $('.main-menu__sublist').attr('aria-expanded', true);

    btnToggleMenu.on('click', function () {
      if (!$(this).hasClass('active')) {
        $(this).addClass('active');
        navContainer.addClass('show').attr('aria-expanded', true);
        body.addClass('show-menu');
      } else {
        $(this).removeClass('active');
        navContainer.removeClass('show').attr('aria-expanded', false);
        body.removeClass('show-menu');
      }
    });
  } else {
    $('.main-menu__sublist').attr('aria-expanded', false);

    toggleSub.unbind().on('click', function () {
      const target = $('#' + $(this).attr('aria-controls'));
      const parent = $(this).parent();

      if (target.attr('aria-expanded') === 'false') {
        // Get other actives
        const currentSub = $('.main-menu__sublist.active');

        if (currentSub.length) {
          currentSub.attr('aria-expanded', false).removeClass('active');
        }

        target.attr('aria-expanded', true).addClass('active');
        parent.addClass('sub-shown');
      } else {
        target.attr('aria-expanded', false).removeClass('active');
        parent.removeClass('sub-shown');
      }
    });

    $(document).mouseup(function (e) {
      const container = $(".main-menu__sublist.active");
      const click = $(".main-menu__sublist.active, .main-menu");

      // if the target of the click isn't the container nor a descendant of the container
      if (!click.is(e.target) && click.has(e.target).length === 0) {
        container.attr('aria-expanded', false).removeClass('active');
        $('.sub-shown').removeClass('sub-shown');
      }
    });
  }
}

var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;

if (isIE11) {
  // lalala, niks leuks voor IE11
} else {

  window.addEventListener('load', function () {
    var windowwidth = window.innerWidth;
    doNav(windowwidth);
  });

  window.addEventListener('resize', function () {
    var windowwidth = window.innerWidth;
    doNav(windowwidth);
  });

}

// =========================================================================================================

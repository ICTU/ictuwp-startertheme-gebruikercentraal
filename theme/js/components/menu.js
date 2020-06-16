//
// Gebruiker Centraal - menu.js
// ----------------------------------------------------------------------------------
// Functionaliteit voor tonen / verbergen mobiel menu
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas, Paul van Buuren
// @license GPL-2.0+
// @version 3.15.9
// @desc.   CTA-kleuren, a11y groen, sharing buttons optional, beeldbank CPT code separation.
// @link    https://github.com/ICTU/gebruiker-centraal-wordpress-theme


var toggleMenu = $('.btn--toggle-menu');
var regionNav = $('.l-header-nav');
var mainMenu = $('#mainnav');

var bp = 1000;

function doNav(width) {

  if (width < bp) {

    // Mobile
    regionNav.attr('aria-hidden', true);

    // Show al sublists
    $('.main-menu__sublist').attr('aria-hidden', 'false');

    toggleMenu.on('click', function () {
      $(this).toggleClass('active');
      $('body').toggleClass('show-menu');

      if (regionNav.attr('aria-hidden') === 'true') {
        regionNav.attr('aria-hidden', 'false');
      } else if (regionNav.attr('aria-hidden') === 'false') {
        regionNav.attr('aria-hidden', 'true');
      }
    });

  } else if (width >= bp) {
    // Desktop
    regionNav.attr('aria-hidden', false);
    $('.main-menu__sublist').attr('aria-hidden', 'true');

    // Add class on mouse enter
    $('.main-menu__item--with-sub').on('mouseenter', function () {
      if (!($(this).hasClass('open'))) {
        // Unset other active if there
        mainMenu.find('.open').removeClass('open');
        mainMenu.find('ul[aria-hidden="false"]').attr('aria-hidden', 'true');

        // Add attributes to current menu
        $(this).addClass('open').find('.main-menu__sublist').attr('aria-hidden', 'false');
        $(this).find('a:first-child').attr('aria-expanded', 'true');
      }
    });

    // And remove again on mouseleave
    $('.main-menu__item--with-sub').mouseleave(function () {
      // Add attributes to current menu
      $(this).removeClass('open');
      $(this).attr('aria-hidden', 'true');
      $(this).parent().find('a:first-child').attr('aria-expanded', 'false');
    });

    // Add toggle behaviour on click
    $('.main-menu__open-sub').on('click', function () {
      var menuItem = $(this).parent();
      var currentActive = mainMenu.find('.open');

      if (!(menuItem.hasClass('open'))) {
        //Submenu is closed, has to open
        if (currentActive.length) {
          //If there is another item open remove it
          currentActive.removeClass('open').find('.main-menu__sublist').attr('aria-hidden', true);
          currentActive.find('button').attr('aria-expanded', false);
        }

        $(this).attr('aria-expanded', true).find('span').text('Open ' + menuItem.find('a:first span').text());
        menuItem.addClass('open').find('.main-menu__sublist').attr('aria-hidden', false);

      } else if (menuItem.hasClass('open')) {
        // Submenu is open, has to close
        $(this).attr('aria-expanded', false).find('span').text('Sluit ' + menuItem.find('a:first span').text());
        menuItem.removeClass('open').find('.main-menu__sublist').attr('aria-hidden', true);
      }
    });

  }
}


$(window).on('load', function () {
  var w = $(window).width();

  doNav(w);
});

$(window).on('resize', function () {
  var w = $(window).width();

  doNav(w);
});


// =========================================================================================================

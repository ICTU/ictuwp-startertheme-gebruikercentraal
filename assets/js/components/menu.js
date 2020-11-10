//
// Gebruiker Centraal - menu.js
// ----------------------------------------------------------------------------------
// Functionaliteit voor tonen / verbergen mobiel menu
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas, Paul van Buuren
// @license GPL-2.0+
// @version 5.0.22.temp
// @desc.   CTA-kleuren, a11y groen, sharing buttons optional, beeldbank CPT code separation.
// @link    https://github.com/ICTU/gebruiker-centraal-wordpress-theme

const breakpointmenu = 1000; // op 1000px toggle tussen desktop / mobiel, zie ook 'nav': 1000px
const totalMenuElement = document.getElementById("menu-primary");
const menuItems = document.querySelectorAll("li.main-menu__item--with-sub");
const toggleMenu = document.querySelectorAll(".btn--toggle-menu");
const divMenuContainer = document.querySelectorAll(".l-header-nav");
const mainMenu = document.getElementById("mainnav");
const body = document.getElementsByTagName("body");

function hoverListItem(theObject, dinges) {

  if (theObject.classList.contains('open')) {
    // heeft wel class open, dus status is open; nieuwe status wordt: alles sluiten
    theObject.classList.remove('open');
    theObject.querySelector('a').setAttribute('aria-expanded', "false");
    if (typeof theObject.querySelector('button') != 'undefined') {
      theObject.querySelector('button').setAttribute('aria-expanded', "false");
    }
    theObject.querySelector('ul.main-menu__sublist').classList.add('visuallyhidden');
    theObject.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "false");
  } else {
    // heeft GEEN class open, dus status is niet open; nieuwe status wordt: alles openen
    theObject.classList.add('open');
    theObject.querySelector('a').setAttribute('aria-expanded', "true");
    if (typeof theObject.querySelector('button') != 'undefined') {
      theObject.querySelector('button').setAttribute('aria-expanded', "true");
    }
    theObject.querySelector('ul.main-menu__sublist').classList.remove('visuallyhidden');
    theObject.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "true");
  }

}

function openMenuItems() {

  // alle items openen en eventuele andere classes voor elk list-item verwijderen
  Array.prototype.forEach.call(menuItems, function (el, i) {

    el.classList.add("open");
    var sublist = el.querySelector("ul.main-menu__sublist");
    if (sublist) {
      sublist.classList.remove("visuallyhidden");
      sublist.setAttribute('aria-expanded', "true");
    }

    el.removeEventListener("pointerenter", function (event) {
      hoverListItem(this, 'over');
    });
    el.removeEventListener("pointerleave", function (event) {
      hoverListItem(this, 'out');
    });

  });

}

function closeMenuItems() {

  var width = window.innerWidth;
  var listitems = document.querySelectorAll(".menu-item-has-children");

  Array.prototype.forEach.call(menuItems, function (el, i) {
    el.classList.remove("open");
    el.querySelector('a').setAttribute('aria-expanded', "false");

    if (width > breakpointmenu) {

      var buttonExists = el.querySelector('button');

      if (buttonExists && typeof buttonExists != 'undefined') {
        buttonExists.setAttribute('aria-expanded', "false");
        buttonExists.classList.remove('open-list');
      }
      el.querySelector('ul.main-menu__sublist').classList.add('visuallyhidden');
    }

  });
}

function istotalMenuElementMenu(event) {
  if (totalMenuElement !== event.target && !totalMenuElement.contains(event.target)) {
    closeMenuItems();
  }
}

document.onkeydown = function (evt) {
  evt = evt || window.event;
  if (evt.keyCode == 27) {
    // close with ESC
    closeMenuItems();
  }
};
document.addEventListener('click', istotalMenuElementMenu);

// =========================================================================================================


function cleanUpMenu() {
  // verwijder eventueel al aanwezige menu-knoppen van een vorige keer (window resize bby)
  document.querySelectorAll('button.main-menu__open-sub').forEach(function (thisElement) {
    thisElement.remove();
  })

}

// =========================================================================================================


function doNav(width) {


  // Zorgen dat alle eventuele toegevoegde buttons weer weggehaald worden
  cleanUpMenu();

  if (width < breakpointmenu) {

    // classes en attributen weghalen die ervoro zorgen dat submenu-items verborgen worden
    openMenuItems();

    toggleMenu[0].addEventListener("click", function (event) {
      if (this.classList.contains('active')) {
        this.classList.remove('active');
        body[0].classList.remove('show-menu');
      } else {
        this.classList.add('active');
        body[0].classList.add('show-menu');
      }
    });

  } else {
    // Desktop


    Array.prototype.forEach.call(menuItems, function (el, i) {
      var thisListItem = el;

      console.log('desktop, loopieloopie: ' + width );

      var currentSubmenus = thisListItem.querySelector('.main-menu__sublist');
      var anchorInListItem = el.querySelector('a');
      var appendButtonAfterAnchor = '<button class="main-menu__open-sub"><span><span class="visuallyhidden">Submenu voor “' + anchorInListItem.text + '”</span></span></button>';
      anchorInListItem.insertAdjacentHTML('afterend', appendButtonAfterAnchor);

      // verberg het submenu in dit listitem
      currentSubmenus.classList.add('visuallyhidden');

      thisListItem.addEventListener("pointerenter", function (event) {
        hoverListItem(this, 'over');
      });
      thisListItem.addEventListener("pointerleave", function (event) {
        hoverListItem(this, 'out');
      });
      el.querySelector('button').addEventListener("click", function (event) {

        if (this.parentNode.classList.contains('open')) {
          // heeft wel class open, dus status is open; nieuwe status wordt: alles sluiten
          this.parentNode.classList.remove('open');
          this.parentNode.querySelector('a').setAttribute('aria-expanded', "false");
          this.parentNode.querySelector('button').setAttribute('aria-expanded', "false");
          this.parentNode.querySelector('button').classList.remove('open-list');
          this.parentNode.querySelector('ul.main-menu__sublist').classList.add('visuallyhidden');
          this.parentNode.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "false");
        } else {
          // heeft GEEN class open, dus status is niet open; nieuwe status wordt: alles openen
          this.parentNode.classList.add('open');
          this.parentNode.querySelector('a').setAttribute('aria-expanded', "true");
          this.parentNode.querySelector('button').setAttribute('aria-expanded', "true");
          this.parentNode.querySelector('button').classList.add('open-list');
          this.parentNode.querySelector('ul.main-menu__sublist').classList.remove('visuallyhidden');
          this.parentNode.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "true");
        }
        event.preventDefault();
      });


    });


  }
}


var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;

if ( isIE11 ) {
  // lalala, niks leuks voor IE11
}
else {

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

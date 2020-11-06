//
// Gebruiker Centraal - menu.js
// ----------------------------------------------------------------------------------
// Functionaliteit voor tonen / verbergen mobiel menu
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas, Paul van Buuren
// @license GPL-2.0+
// @version 5.0.20.q
// @desc.   CTA-kleuren, a11y groen, sharing buttons optional, beeldbank CPT code separation.
// @link    https://github.com/ICTU/gebruiker-centraal-wordpress-theme


console.log("Mark, wat zit je haar leuk: 5.0.20.q");


const totalMenuElement = document.getElementById('menu-primary');
const menuItems = document.querySelectorAll('li.main-menu__item--with-sub');

Array.prototype.forEach.call(menuItems, function (el, i) {
  var thisListItem = el;
  var anchorInListItem = el.querySelector('a');
  var appendButtonAfterAnchor = '<button class="main-menu__open-sub"><span><span class="visuallyhidden">Submenu voor “' + anchorInListItem.text + '”</span></span></button>';
  anchorInListItem.insertAdjacentHTML('afterend', appendButtonAfterAnchor);

  thisListItem.addEventListener("pointerenter", function (event) {
    hoverListItem(this);
  });
  thisListItem.addEventListener("pointerleave", function (event) {
    hoverListItem(this);
  });
  el.querySelector('button').addEventListener("click", function (event) {
    if (!this.parentNode.classList.contains('open')) {
      // heeft GEEN class open, dus status is niet open; nieuwe status wordt: alles openen
      this.parentNode.classList.add('open');
      this.parentNode.querySelector('a').setAttribute('aria-expanded', "true");
      this.parentNode.querySelector('button').setAttribute('aria-expanded', "true");
      this.parentNode.querySelector('ul.main-menu__sublist').classList.remove('visuallyhidden');
      this.parentNode.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "true");
    } else {
      // heeft wel class open, dus status is open; nieuwe status wordt: alles sluiten
      this.parentNode.classList.remove('open');
      this.parentNode.querySelector('a').setAttribute('aria-expanded', "false");
      this.parentNode.querySelector('button').setAttribute('aria-expanded', "false");
      this.parentNode.querySelector('ul.main-menu__sublist').classList.add('visuallyhidden');
      this.parentNode.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "false");
    }
    event.preventDefault();
  });
});



function hoverListItem(theObject) {

  if (!theObject.classList.contains('open')) {
    // heeft GEEN class open, dus status is niet open; nieuwe status wordt: alles openen
    theObject.classList.add('open');
    theObject.querySelector('a').setAttribute('aria-expanded', "true");
//    theObject.querySelector('a').style.cssText = 'border: 2px dashed green';
    theObject.querySelector('button').setAttribute('aria-expanded', "true");
    theObject.querySelector('ul.main-menu__sublist').classList.remove('visuallyhidden');
    theObject.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "true");
  } else {
    // heeft wel class open, dus status is open; nieuwe status wordt: alles sluiten
    theObject.classList.remove('open');
    theObject.querySelector('a').setAttribute('aria-expanded', "false");
    theObject.querySelector('button').setAttribute('aria-expanded', "false");
    theObject.querySelector('ul.main-menu__sublist').classList.add('visuallyhidden');
    theObject.querySelector('ul.main-menu__sublist').setAttribute('aria-expanded', "false");
  }

}

function closeMenuItems() {
  var listitems = document.querySelectorAll(".menu-item-has-children");
  [].forEach.call(listitems, function (el) {
    el.classList.remove("open");
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


// =========================================================================================================

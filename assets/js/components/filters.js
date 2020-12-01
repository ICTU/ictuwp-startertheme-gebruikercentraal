//
// Gebruiker Centraal - filters.js
// ----------------------------------------------------------------------------------
// Functionaliteit voor het toevoegen van een active class aan filters
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Tamara de Haas
//

  var filterLabel = jQuery('.form-item--filter label');

  filterLabel.click(function () {

    var formItem = jQuery(this).parent();

    if (formItem.find('input:checked').length) {
      formItem.removeClass('is-active');
    } else {
      formItem.addClass('is-active');
    }

  });

// =========================================================================================================


$(document).ready(function(){

  var $filters = $('.form-item--filter'); // find the filters
  var $works = $('.workItem'); // find the portfolio items
  var showAll = $('.showAll'); // identify the "show all" button

  var cFilter, cFilterData; // declare a variable to store the filter and one for the data to filter by
  var filtersActive = []; // an array to store the active filters

  $filters.click(function(){ // if filters are clicked
    cFilter = $(this);
    cFilterData = cFilter.attr('data-filter'); // read filter value

    highlightFilter();
    applyFilter();
  });

  function highlightFilter () {
    var filterClass = 'filter-active';
    if (cFilter.parent().hasClass(filterClass)) {
      cFilter.parent().removeClass(filterClass);
      removeActiveFilter(cFilterData);
    } else if (cFilter.hasClass('showAll')) {
      $filters.parent().removeClass(filterClass);
      filtersActive = []; // clear the array
      cFilter.parent().addClass(filterClass);
    } else {
      showAll.parent().removeClass(filterClass);
      cFilter.parent().addClass(filterClass);
      filtersActive.push(cFilterData);
    }
  }

  function applyFilter() {
    // go through all portfolio items and hide/show as necessary
    $works.each(function(){
      var i;
      var classes = $(this).attr('class').split(' ');
      if (cFilter.hasClass('showAll') || filtersActive.length == 0) { // makes sure we catch the array when its empty and revert to the default of showing all items
        $works.addClass('show-workItem'); //show them all
      } else {
        $(this).removeClass('show-workItem');
        for (i = 0; i < classes.length; i++) {
          if (filtersActive.indexOf(classes[i]) > -1) {
            $(this).addClass('show-workItem');
          }
        }
      }
    });
  }

  // remove deselected filters from the ActiveFilter array
  function removeActiveFilter(item) {
    var index = filtersActive.indexOf(item);
    if (index > -1) {
      filtersActive.splice(index, 1);
    }
  }

});

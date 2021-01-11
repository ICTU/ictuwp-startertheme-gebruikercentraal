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

  const { __, _x, _n, _nx } = wp.i18n;

  var $filters = $('.form-item--filter'); // find the filters
  var $works = $('.work-item'); // find the portfolio items
  var showall = $('.show-all'); // identify the "show all" button

  var cFilter, cFilterData, cFiltercat, cFilterCount; // declare a variable to store the filter and one for the data to filter by
  var filtersActive = []; // an array to store the active filters
  var filtersActiveCat = [];
  var filtersActiveCount = [];

  $filters.keypress(function(e){
    if((e.keyCode ? e.keyCode : e.which) == 13){
      $(this).trigger('click');
    }
  });

  $filters.click(function(){ // if filters are clicked
    cFilter = $(this);
    cFilterData = cFilter.attr('data-filter');
    cFiltercat = cFilter.attr('data-categorie');// read filter value
    cFilterCount = cFilter.attr('data-count');

    highlightFilter();
    applyFilter();
  });

  function highlightFilter () {
    var filterClass = 'filter-active';
    var textstring = '';

    if (cFilter.parent().hasClass(filterClass)) {
      cFilter.parent().removeClass(filterClass);
      removeActiveFilter(cFilterData,cFiltercat,cFilterCount);
    } else if (cFilter.hasClass('show-all')) {
      $filters.parent().removeClass(filterClass);
      filtersActive = [];
      filtersActiveCat = [];// clear the array
      filtersActiveCount = [];
      cFilter.parent().addClass(filterClass);
      textstring = ' tipkaarten geselecteerd in alle thema\'s.';
      $("#cards--text").text(textstring);
      $("#cards--cats").text("");
    } else {
      showall.parent().removeClass(filterClass);
      cFilter.parent().addClass(filterClass);
      filtersActive.push(cFilterData);
      filtersActiveCat.push(cFiltercat);
      filtersActiveCount.push(cFilterCount);
      console.log(filtersActiveCat);


      $("#cards--cats").text('');
      $("#cards--count").text('');

      sum = 0;
      $.each(filtersActiveCount,function(){sum+=parseFloat(this) || 0;});
      textstring = sum + _n( ' tipkaarten geselecteerd in het thema: ', ' tipkaarten geselecteerd in de thema\'s: ', filtersActiveCat.length, 'gctheme' );
      textstring += filtersActiveCat.join(", ").toLowerCase();
      $("#cards--text").text( textstring );
    }
  }

  function applyFilter() {
    // go through all portfolio items and hide/show as necessary
    $works.each(function(){
      var i;
      var classes = $(this).attr('class').split(' ');
      if (cFilter.hasClass('show-all') || filtersActive.length == 0) { // makes sure we catch the array when its empty and revert to the default of showing all items
        $works.addClass('show-work-item'); //show them all
        $("#cards--count").text(cFilterCount);
      } else {
        $(this).removeClass('show-work-item');
        for (i = 0; i < classes.length; i++) {
          if (filtersActive.indexOf(classes[i]) > -1) {
            $(this).addClass('show-work-item');
          }
        }
      }
    });
  }

  // remove deselected filters from the ActiveFilter array
  function removeActiveFilter(item,cat,count) {
    var index = filtersActive.indexOf(item);
    var indexCat = filtersActiveCat.indexOf(cat);
    var indexCount = filtersActiveCount.indexOf(count);

    console.log(filtersActive.length);

    if (index > -1) {
      filtersActive.splice(index, 1);

      if (filtersActive.length === 0) {
        console.log('empty');
        $(".show-all").trigger("click");
      }
    }
    if (indexCat > -1) {
      filtersActiveCat.splice(indexCat, 1);

      $("#cards--cats").text(filtersActiveCat.join(", "));

    }
    if (indexCount > -1) {
      filtersActiveCount.splice(indexCount, 1);
      sum = 0;
      $.each(filtersActiveCount,function(){sum+=parseFloat(this) || 0;});
      $("#cards--count").text(sum);
    }
  }

});

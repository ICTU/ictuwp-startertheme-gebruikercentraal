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
jQuery(document).ready(function() {
(function() {
  
  var $imgs = $('.section .grid .tipkaart');
  var $buttons = $('#buttons');
  var tagged = {};
  
  $imgs.each(function(){
    var img = this;
    var tags = $(this).data('tags');
    
    if (tags) {
      tags.split(',').forEach(function(tagName) {
        if (tagged[tagName] == null) {
          tagged[tagName] = []; 
        }
        tagged[tagName].push(img);
      });
    }
  });
// Buttons, ents, filters below...
$('<button/>', {
  text: 'Alle tipkaarten',
  class: 'active form-item--filter',
  click: function() {
    $(this)
      .addClass('active')
      .siblings()
      .removeClass('active');
    $imgs.show();
  }
}).appendTo($buttons);

$.each(tagged, function(tagName) {
  $('<button/>', {
    text: tagName + ' (' + tagged[tagName].length + ')',
    class: 'form-item--filter ' + 'cat--' + tagName,
    click: function () {
      $(this)
      .addClass('active')
      .siblings()
      .removeClass('active');
     $imgs
      .hide()
      .filter(tagged[tagName])
      .show();
    }
  }).appendTo($buttons);
});  
}());  
});

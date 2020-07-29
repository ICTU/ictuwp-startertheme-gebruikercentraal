(function( $ ) {
	'use strict';
	var Obj = {
		// All pages
		'common' : {
			init : function() {
				// JavaScript to be fired on all pages
				$( function() {
					Obj.common.voting();
				} );

				// Autosuggest
				//will check if display category is enabled through localize script
				if ( PAKB.category ) {
					$('#kb-s.autosuggest').devbridgeAutocomplete({
						serviceUrl  : PAKB.ajaxurl,
						params      : {'action':'search_title'},
						minChars    : 1,
						maxHeight   : 450,
						groupBy     : 'category',
						preventBadQueries : false,
						showNoSuggestionNotice: true,
						noSuggestionNotice: PAKB.noresult_placeholder,
						onSelect    : function(suggestion) {
							window.location = suggestion.url;
						}
					});
				} else {
					$('#kb-s.autosuggest').devbridgeAutocomplete({
						serviceUrl  : PAKB.ajaxurl,
						params      : {'action':'search_title'},
						minChars    : 1,
						maxHeight   : 450,
						preventBadQueries : false,
						showNoSuggestionNotice: true,
						noSuggestionNotice: PAKB.noresult_placeholder,
						onSelect    : function(suggestion) {
							window.location = suggestion.url;
						}
					});
				}

			},
			finalize : function() {
				// JavaScript to be fired on all pages, after page specific JS is fired
			},

			voting : function() {
				// Like
				$('#pakb-vote a.pakb-like-btn').click(function(){
					var response_div = $('#pakb-vote');
					$.ajax({
						url         : PAKB.base_url,
						data        : {'pakb_vote_like':$(this).attr('data-post-id')},
						beforeSend  : function(){
						},
						success     : function(data){
							response_div.html(data).fadeIn(900);
						},
						complete    : function(){

						}
					});
				});

				// Dislike
				$('#pakb-vote a.pakb-dislike-btn').click(function(){
					var response_div = $('#pakb-vote');
					$.ajax({
						url         : PAKB.base_url,
						data        : {'pakb_vote_dislike':$(this).attr('data-post-id')},
						beforeSend  : function(){

						},
						success     : function(data){
							response_div.html(data).fadeIn(900);
						},
						complete    : function(){

						}
					});
				});

			}
		}
	};

	// The routing fires all common scripts, followed by the page specific scripts.
	// Add additional events for more control over timing e.g. a finalize event
	var UTIL = {
		fire : function( func, funcname, args ) {
			var fire;
			var namespace = Obj;
			funcname      = (funcname === undefined) ? 'init' : funcname;
			fire          = func !== '';
			fire          = fire && namespace[ func ];
			fire          = fire && typeof namespace[ func ][ funcname ] === 'function';

			if ( fire ) {
				namespace[ func ][ funcname ]( args );
			}
		},
		loadEvents : function() {
			// Fire common init JS
			UTIL.fire( 'common' );

			// Fire page-specific init JS, and then finalize JS
			$.each( document.body.className.replace( /-/g, '_' ).split( /\s+/ ), function( i, classnm ) {
				UTIL.fire( classnm );
				UTIL.fire( classnm, 'finalize' );
			} );

			// Fire common finalize JS
			UTIL.fire( 'common', 'finalize' );
		}
	};

	// Load Events
	$( document ).ready( UTIL.loadEvents );

})( jQuery ); // Fully reference jQuery after this point.

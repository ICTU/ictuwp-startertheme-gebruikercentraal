(function( $ ) {
	'use strict';

	var Obj = {
		// All pages
		'common' : {
			init : function() {
				// JavaScript to be fired on all pages
				$( function() {
					// $( '.wp-color-picker-field' ).wpColorPicker();

					Obj.common.reset_post_vote(); //reset vote in admin
					Obj.common.reset_all_votes(); //reset all votes

					if ( PAKB_admin.reorder ) {
						if ( $( 'body' ).hasClass( 'taxonomy-knowledgebase_category' ) ) {
							Obj.common.order_taxonomies();
						} else {
							Obj.common.order_posts();
						}
					}
				} );
			},
			//reset all votes in admin area under pressapps option
			reset_all_votes : function() {
				//close notice dismiss
				$( 'body.knowledge-base_page_pakb-options-voting' ).on( 'click', '.notice-dismiss', function() {
					var message = $( this ).closest( '.wrap' ).find( '#message' );
					message.fadeOut().delay( 300 ).queue(
						function( next ) {
							message.remove();
							next();
						} );
				} );
				$( '#pakb_vote_reset_all' ).on( 'click', function( e ) {
					e.preventDefault();
					var reset_all_confirm = confirm( PAKB_admin.reset_all_confirmation );
					if ( reset_all_confirm ) {
						$.ajax( {
							url : ajaxurl,
							type : 'POST',
							dataType : 'json',
							data : {
								action : 'pakb_reset_vote_all_admin'
							},
							success : function( respond ) {
								if ( respond.success ) {
									$( '.wrap h1' ).after( '<div id="message" class="updated notice is-dismissible"><p>' + PAKB_admin.reset_success + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
								}
							},
							error : function( e ) {
								console.log( e );
							}
						} );
					}
				} );
			},
			//reset single vote for post
			reset_post_vote : function() {
				//close notice dismiss
				$( 'body.post-type-knowledgebase' ).on( 'click', '.notice-dismiss', function() {
					var message = $( this ).closest( '.wrap' ).find( '#message' );
					message.fadeOut().delay( 300 ).queue(
						function( next ) {
							message.remove();
							next();
						} );
				} );
				$( '.pakb-reset-vote' ).on( 'click', function( e ) {
					e.preventDefault();
					var reset_confirmation = confirm( PAKB_admin.reset_confirmation );
					if ( reset_confirmation ) {
						$.ajax( {
							url : ajaxurl,
							type : 'POST',
							dataType : 'json',
							data : {
								action : 'pakb_reset_vote_admin',
								post_id : $( this ).attr( 'data-post-id' ),
								reset_nonce : $( this ).attr( 'data-reset-nonce' )
							},
							success : function( respond ) {
								//will check if the update was successful and will update the count on likes column
								if ( respond.post_likes_update ) {
									$( '#post-' + respond.post_id ).find( '.likes' ).html( '<p>0</p>' );
								}

								//will check if the update was successful and will update the count on dislikes column
								if ( respond.post_dislikes_update ) {
									$( '#post-' + respond.post_id ).find( '.dislikes' ).html( '<p>0</p>' );
								}

								if ( respond.post_likes_update && respond.post_dislikes_update ) {
									$( '.wrap h1' ).after( '<div id="message" class="updated notice is-dismissible"><p>' + PAKB_admin.reset_success + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
								}
							},
							error : function( e ) {
								console.log( e );
							}
						} );
					}
				} );
			},
			order_posts : function() {
				$( '#the-list' ).sortable( {
					items : 'tr',
					opacity : 0.6,
					cursor : 'move',
					axis : 'y',
					update : function() {
						var order = $( this ).sortable( 'serialize' ) + '&action=pakb_order_update_posts';
						$.post( ajaxurl, order, function( response ) {
						} );
					}

				} );
			}, // end of order_post
			order_taxonomies : function() {
				$( '#the-list' ).sortable( {
					items : 'tr',
					opacity : 0.6,
					cursor : 'move',
					axis : 'y',
					update : function() {
						var order = $( this ).sortable( 'serialize' ) + '&action=pakb_order_update_taxonomies';
						$.post( ajaxurl, order, function( response ) {
						} );
					}
				} );
			} // end of order_taxonomies
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

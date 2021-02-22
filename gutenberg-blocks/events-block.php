<?php
// events-block.php

add_action( 'acf/init', 'gb_add_events_block' );

function gb_add_events_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/events',
			'title'           => _x( 'GC evenementenblok', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Collectie van aankomende events in een Gutenberg block', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_events_block',
			'category'        => 'gc-blocks',
			'icon'            => 'flag', // todo: eigen icon voor dit block
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}

//========================================================================================================

function gb_render_events_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	// verzamelen van de inhoud
	$context['events'] = events_block_get_data();

	// Hergebruik van section-related.html.twig, want dat voldoet prima
	Timber::render( 'sections/section-events.html.twig', $context );

}


/*
 * returns an array for the related section
 */
function events_block_get_data() {

	global $post;
	$type_block           = 'section--overview';
	$imagesize_for_thumbs = IMAGESIZE_16x9;

	if ( ( 'ja' === get_field( 'events_content_toevoegen' ) ) && ( class_exists( 'EM_Events' ) ) ) {

		$alt_content         = get_field( 'events_content_alt_content' );
		$alt_content_message = get_field( 'events_content_alt_content_message' );
		$maxnr               = ( get_field( 'maximaal_aantal_events' ) ) ? intval( get_field( 'maximaal_aantal_events' ) ) : 3;
		// events selecteren
		$args = array(
			'limit' => $maxnr,
			'array' => true
		);

		$eventcategories = get_field( 'content_block_taxonomy_events' );
		if ( $eventcategories ) {
			$args['category'] = $eventcategories;
		}

		$events = EM_Events::get( $args );

		if ( $events ) {

			if ( ( 'ja' === get_field( 'events_content_eventspage_link' ) ) && get_option( 'dbem_events_page' ) ) {

				$content_block_cta_eventspage = get_field( 'content_block_cta_eventspage' );
				if ( ! $content_block_cta_eventspage ) {
					$return['cta_title'] = get_the_title( get_option( 'dbem_events_page' ) );
				} else {
					$return['cta_title'] = wp_strip_all_tags( $content_block_cta_eventspage );
				}
				// de titel boven dit blok is exact gelijk aan de pagina-titel van de event page
				$return['cta_url'] = get_permalink( get_option( 'dbem_events_page' ) );
			}


			foreach ( $events as $event ):

				$item              = prepare_card_content( get_post( $event['post_id'] ) );
				$return['items'][] = $item;

			endforeach;

			$return['title'] = get_field( 'content_block_title' ) ? get_field( 'content_block_title' ) : '';

		} else {
			// geen events beschikbaar
			if ( 'titel_boodschap' === $alt_content ) {
				// wel iets tonen
				if ( get_field( 'content_block_title' ) ) {
					// als er een titel is ingevoerd, dan moet de eventuele foutboodschap aan de description geplakt worden
					$return['title']       = get_field( 'content_block_title' );
					$return['description'] = $alt_content_message;
				} else {
					// geen titel ingevoerd, dus is de foutboodschap de titel
					$return['title'] = $alt_content_message;
				}


			} else {
				// helemaal niks tonen
				$return = array();
			}

		}

	}

	/* Restore original Post Data */
	wp_reset_postdata();

	$columncounter = '3';
	if ( isset( $return['description'] ) ) {
		$return['descr'] = $return['description'];
	}

	if ( isset( $return['items'] ) ) {

		if ( count( $return['items'] ) === 1 ) {
			$columncounter = '1';
		} elseif ( count( $return['items'] ) === 2 ) {
			$columncounter = '2';
		}
	}

	$return['columncounter'] = $columncounter;
	$return['type_block']    = $type_block;

	return $return;

}

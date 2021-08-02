<?php
/**
 * Template Name: Overzichtspagina
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context = Timber::context();

$timber_post         = new Timber\Post();
$context['post']     = $timber_post;
$context['overview'] = overviewpage_get_items();

$category = get_queried_object();
$context['overview']['id'] = !empty($category->term_id) ? $category->term_id : '';

// Gerelateerde content metablocks
if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

	$context['related'] = related_block_get_data();

}

// Spotlight blocks
$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}

// Inleiding
if ( get_field( 'post_inleiding' ) ) {
	$intro            = get_field( 'post_inleiding' );
	$context['intro'] = wpautop( $intro );
}


Timber::render( [ 'overview.html.twig', 'page.twig' ], $context );

//========================================================================================================

function overviewpage_get_items() {

	global $post;
	$return = array();
	$thisid = get_the_id();

	if ( 'automatic' === get_field( 'overzichtspagina_method', $thisid ) ) {
		// automagische selectie

		$contenttypes = get_field( 'contenttypes', $thisid );

		$args = [
			'post_type'      => $contenttypes,
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		];

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {

				$the_query->the_post();
				$item              = array();
				$item              = prepare_card_content( $post );
				$return['items'][] = $item;

			}
		}
	} else {
		// handmagische selectie

		$items = get_field( 'overzichtspagina_content_block_items', $thisid );

		if ( $items ) {

			foreach ( $items as $post ):

				setup_postdata( $post );
				$item              = prepare_card_content( $post );
				$return['items'][] = $item ? $item : '';

			endforeach;

		}
	}

	wp_reset_query();

	/*
	 *
		echo '<pre>';
		var_dump( $return );
		echo '</pre>';
	 */

	return $return;

}

//========================================================================================================


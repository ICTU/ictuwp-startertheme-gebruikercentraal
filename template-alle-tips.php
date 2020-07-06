<?php
/**
 * Template Name: [OD] Overzicht alle tips
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context              = Timber::context();
$timber_post          = Timber::query_post();
$context['post']      = $timber_post;
$context['filters']   = [];
$context['tipkaarts'] = [];
$themakleuren         = [];
$card                 = [];

// eerst alle tipthema's langs om de kleuren op te halen
$args  = [
	'taxonomy'   => GC_TIPTHEMA,
	'hide_empty' => TRUE,
	'orderby'    => 'name',
	'order'      => 'ASC',
];
$terms = get_terms( $args );

if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
	$count = count( $terms );

	foreach ( $terms as $term ) {

		$themakleur = get_field( 'kleur_en_icoon_tipthema', GC_TIPTHEMA . '_' . $term->term_id );

		if ( $themakleur ) {

			$themakleuren[ $term->term_id ] = $themakleur;
			/*
			 * TODO: filters weer terug zetten
			 * filter array leeghouden, zodat frontend nog even zo schoon mogelijk blijft
			$context['filters'][]           = array(
				'id'    => $term->term_id,
				'name'  => $term->name,
				'class' => $themakleur,
			);
			*/

		} else {
			// kleur ontbreekt
		}
	}
}

$args = [
	// Get post type project
	'post_type'      => GC_TIP_CPT,
	// Get all posts
	'posts_per_page' => - 1,
	'post_status'    => 'publish',
	'orderby'        => [
		'date' => 'DESC',
	],
];

$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {

		$the_query->the_post();
		$card          = [];
		$taxonomie     = get_the_terms( $post->ID, GC_TIPTHEMA );
		$card['title'] = od_wbvb_custom_post_title( get_the_title() );
		$card['nr']    = sprintf( _x( 'Tip %s', 'Label tip-ummer', 'gctheme' ), get_post_meta( $post->ID, 'tip-nummer', TRUE ) );
		$card['url']   = get_the_permalink();
		$is_toptip     = get_post_meta( $post->ID, 'is_toptip', TRUE );
		if ( $is_toptip ) {
			$card['toptip']      = TRUE;
			$card['toptiptekst'] = 'Toptip';
		} else {
			$card['toptip'] = FALSE;
		}
		if ( isset( $themakleuren[ $taxonomie[0]->term_id ] ) ) {
			$card['category'] = $themakleuren[ $taxonomie[0]->term_id ];
		}

		$context['tipkaarts'][] = $card;

	}

}
/* Restore original Post Data */
wp_reset_postdata();


Timber::render( [ 'template-alle-tips.twig', 'page.twig' ], $context );

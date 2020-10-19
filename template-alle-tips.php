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
$themakleuren         = get_themakleuren();
$card                 = [];

/*
Deze argumenten zouden sorteren op tipnummer:

$args = [
	// Get post type project
	'post_type'      => GC_TIP_CPT,
	// Get all posts
	'posts_per_page' => - 1,
	'post_status'    => 'publish',
	'meta_key'       => 'tip-nummer',
	'orderby'        => 'meta_value',
	'order'          => 'DESC'
];

Maar dat helpt natuurlijk niet, want dit is een textveld, geen nummer.
Dus de volgorde is dan 1, 10, 11, 2, 20, 21 etc...

 */

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
		$card['nr']    = sprintf( _x( 'Tip %s', 'Label tip-nummer', 'gctheme' ), get_post_meta( $post->ID, 'tip-nummer', TRUE ) );
/*
		We zouden voorloopnullen kunnen gebruiken en dan in een loop even de tipnummers bijwerken:

		$tipnummermetvoorloopnullen = str_pad( intval( get_post_meta( $post->ID, 'tip-nummer', true ) ), 6, "0", STR_PAD_LEFT );
		update_post_meta( $post->ID, 'tip-nummer', $tipnummermetvoorloopnullen );

Maar dan moeten we dat wel meenemen in de redactieinstructies

en dan moeten we de tipnumemrs converteren naar een integer
		$card['nr']    = sprintf( _x( 'Tip %s', 'Label tip-nummer', 'gctheme' ), intval( get_post_meta( $post->ID, 'tip-nummer', true ) ) );


 */

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

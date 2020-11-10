<?php
/**
 * Template Name: Overzichtspagina tipgevers
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context = Timber::context();

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;
if ( get_field( 'overzichtspagina_inleiding' ) ) {
	// ACF veld 'post_inleiding' is gevuld
	$intro            = get_field( 'overzichtspagina_inleiding' );
	$context['intro'] = wpautop( $intro );

}
$context['tipgevers'] = tipgevers_get_data();


Timber::render( [ 'template-alle-tips.twig', 'page.twig' ], $context );


function tipgevers_get_data() {

	global $post;
	$return = array();


	if ( 'showsome' === get_field( 'overzichtspagina_showall_or_select', get_the_id() ) ) {
		$terms = get_field( 'overzichtspagina_kies_items', get_the_id() );
	} else {
		$terms = get_terms( array(
			'taxonomy'   => OD_CITAATAUTEUR,
			'hide_empty' => true,
		) );
	}

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

		foreach ( $terms as $term ) {

			$item             = [];
			$item['title']    = $term->name;
			$item['function'] = wp_strip_all_tags( get_field( 'tipgever_functietitel', $term ) );
			$image            = get_field( 'tipgever_foto', $term );
			$item['url']      = get_term_link( $term );
			$item['img_alt']  = sprintf( _x( 'Link to tips from %s', 'Arialabel image-link', 'gctheme' ), $item['title'] );

			if ( $image ):
				$size        = 'thumbnail';
				$item['img'] = $image['sizes'][ $size ];
			endif;
			$return['items'][] = $item;
		}
	}


	if ( $return['items'] ) {
		$return['title'] = get_field( 'downloads_title' ) ? get_field( 'downloads_title' ) : _x( 'Downloads', 'Titel boven downloads', 'gctheme' );
		$return['desc']  = get_field( 'downloads_description' );
	}

	return $return;

}

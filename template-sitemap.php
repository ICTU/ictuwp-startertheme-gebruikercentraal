<?php
/**
 * Template Name: Template sitemap
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();

$timber_post        = new Timber\Post();
$context['post']    = $timber_post;
$context['sitemap'] = array();

$count_pages = wp_count_posts( 'page' );

if ( $count_pages ) {

	$args = array(
		'title_li' => '',
		'echo'     => false
	);

	$context['pagetitle']        = _x( "Pagina's", "sitemap titel", 'gctheme' );
	$context['sitemap']['pages'] = wp_list_pages( $args );

}

$maxnr = 20;

$count_posts = wp_count_posts();

if ( $count_posts->publish > 0 ) {

	$args = array(
		'type'  => 'postbypost',
		'echo'  => false,
		'limit' => $maxnr
	);

	$context['posttitle']        = _x( "Berichten", "sitemap titel", 'gctheme' );
	$context['sitemap']['posts'] = wp_get_archives( $args );

}

if ( taxonomy_exists( GC_TIPTHEMA ) ) {

	$args = array(
		'taxonomy' => GC_TIPTHEMA,
		'hide_empty' => true,
		'orderby' => 'name',
		'order' => 'ASC',
	);

	$context['tipthemastitle']        = _x( "Tipthema's", "sitemap titel", 'gctheme' );
	$terms = get_terms( $args );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$count = count( $terms );

		foreach ( $terms as $term ) {
			$termlink                         = array();
			$termlink['link']                 = esc_url( get_term_link( $term ) );
			$termlink['name']                 = $term->name;
			$context['sitemap']['tipthema'][] = $termlink;

		}
	}

}

if ( taxonomy_exists( GC_ODSPEELSET ) ) {

	$args = array(
		'taxonomy' => GC_ODSPEELSET,
		'hide_empty' => false,
		'orderby' => 'name',
		'order' => 'ASC',
	);

	$context['tipspeelsettitle']        = _x( "Speelsets", "sitemap titel", 'gctheme' );
	$terms = get_terms( $args );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$count = count( $terms );

		foreach ( $terms as $term ) {
			$termlink                         = array();
			$termlink['link']                 = esc_url( get_term_link( $term ) );
			$termlink['name']                 = $term->name;
			$context['sitemap']['speelsets'][] = $termlink;

		}
	}
}


if ( taxonomy_exists( OD_CITAATAUTEUR ) ) {

	$args = array(
		'taxonomy' => OD_CITAATAUTEUR,
		'hide_empty' => true,
		'orderby' => 'name',
		'order' => 'ASC',
	);

	$context['tipgeverstitle']        = _x( "Tipgevers", "sitemap titel", 'gctheme' );
	$terms = get_terms( $args );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$count = count( $terms );

		foreach ( $terms as $term ) {
			$termlink                         = array();
			$termlink['link']                 = esc_url( get_term_link( $term ) );
			$termlink['name']                 = $term->name;
			$context['sitemap']['tipgevers'][] = $termlink;

		}
	}

}



Timber::render( array( 'page-sitemap.twig', 'page.twig' ), $context );

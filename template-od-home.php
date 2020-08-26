<?php
/**
 * Template Name: [OD] Homepage
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;

if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

	$context['related'] = related_block_get_data();

}


$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}


if ( 22 === 33 ) {
	Timber::render( [
		'page-' . $timber_post->post_name . '.twig',
		'page.twig',
	], $context );

} else {
	Timber::render( [ 'od-home.html.twig', 'page.twig' ], $context );
}




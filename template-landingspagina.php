<?php
/**
 * Template Name: Landingspagina
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;

if ( get_field( 'post_inleiding' ) ) {
	// ACF veld 'post_inleiding' is gevuld
	$intro            = wpautop( get_field( 'post_inleiding' ) );
	$allowedtags      = '<a><br><li><ul><ol>';
	$context['intro'] = strip_tags( $intro, $allowedtags );

}

if ( 'ja' === get_field( 'downloads_tonen' ) && get_field( 'download_items' ) ) {

	$context['downloads'] = download_block_get_data();

}

if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

	$context['related'] = related_block_get_data();

}

if ( 'ja' === get_field( 'links_tonen' ) ) {

	$context['links'] = links_block_get_data();

}

$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}

Timber::render( array( 'page-landing.twig', 'page.twig' ), $context );

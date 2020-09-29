<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/templates/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();

$timber_post = new Timber\Post();
$context['post'] = $timber_post;

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

// Get the hero image
$context['hero_image'] = get_hero_image();

Timber::render( [
	'page-' . $timber_post->post_name . '.twig',
	'page.twig',
], $context );

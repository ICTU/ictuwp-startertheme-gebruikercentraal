<?php
/**
 * Template Name: [OD] Handleiding
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

require_once( get_template_directory() . '/gutenberg-blocks/handleiding-block.php' );

$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;


$handleidingcounter = 0;

$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}


Timber::render( [ 'od-handleiding.html.twig', 'page.twig' ], $context );




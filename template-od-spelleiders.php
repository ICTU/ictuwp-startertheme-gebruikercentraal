<?php
/**
 * Template Name: [OD] Overzicht spelleiders    
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;


$handleidingcounter = 0;

$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}


Timber::render( [ 'od-handleiding.html.twig', 'page.twig' ], $context );




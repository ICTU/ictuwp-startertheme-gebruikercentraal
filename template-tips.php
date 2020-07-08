<?php
/**
 * Template Name: [OD] Template tips-pagina
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context = Timber::context();

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;
$context['tips'] = array( 'yo tips');

if ( 'ja' === get_field( 'downloads_tonen' ) && get_field( 'download_items' ) ) {

	$context['downloads'] = download_block_get_data();

}


Timber::render( array( 'page-alle-tips.twig', 'page.twig' ), $context );

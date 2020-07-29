<?php
/**
 * Template Name: Landingspagina
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();

$timber_post        = new Timber\Post();
$context['post']    = $timber_post;



Timber::render( array( 'page-sitemap.twig', 'page.twig' ), $context );

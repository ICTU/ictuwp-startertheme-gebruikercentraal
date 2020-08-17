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
	$intro            = get_field( 'post_inleiding' );
	$context['intro'] = wpautop( $intro );

}


Timber::render( array( 'page-landing.twig', 'page.twig' ), $context );

/*
echo $twig->render('landing.html.twig', [
	'site_name' => 'Optimaal Digitaal',
	'site_slogan' => 'Verbeter spelenderwijs je (online) dienstverlening',
	'title' => 'Het Optimaal Digitaal spel',
	'intro' => 'Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Cras ultricies ligula sed magna dictum porta.',
	'spotlight' => 'true',
	'theme' => 'od',
	'logo' => 'img/logo/od.svg',
]);

*/

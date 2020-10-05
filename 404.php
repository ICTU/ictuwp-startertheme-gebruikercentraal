<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();


$context['title_404']          = _x( '404 page not found', '404 title', 'gctheme' );
$context['content_404']        = sprintf( _x( 'The page you are looking for no longer exists. Perhaps you can return back to the site\'s <a href="%s">homepage</a> and see if you can find what you are looking for. Or, you can try finding it by using the search form below.', '404 text', 'gctheme' ), home_url() );
$context['content_404_search'] = get_search_form( array( 'echo' => false ) );
$context['widgets_404']        = Timber::get_widgets( 'widgets_404' );

Timber::render( '404.twig', $context );


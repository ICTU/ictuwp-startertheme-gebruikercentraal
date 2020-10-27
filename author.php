<?php
/**
 * The template for displaying Author Archive pages
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

global $posts;
global $wp_query;

$context          = Timber::context();
$context['posts'] = new Timber\PostQuery();
if ( isset( $wp_query->query_vars['author'] ) ) {
	$author            = new Timber\User( $wp_query->query_vars['author'] );
	$context['author'] = $author;
	$context['title']  = $author->name();

	// TODO: dit gedeelte moet via een nette layout gepresenteerd worden;
	// zie section-authorbox.html.twig

		$context['descr']  = sprintf( _x( 'Posts by %s.', 'Description author archive', 'gctheme' ), $author->name() );


}

foreach ( $posts as $post ) {
	$context['items'][] = prepare_card_content( $post );
}


$templates = [
	'author.twig',
	'archive.twig',
	'index.twig',
];


Timber::render( $templates, $context );

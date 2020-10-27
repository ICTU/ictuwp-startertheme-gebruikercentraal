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

	$author_id = $author->facebook;



	// TODO: dit gedeelte moet via een nette layout gepresenteerd worden;
	// zie section-authorbox.html.twig

	$context['descr']  = sprintf( _x( 'Posts by %s.', 'Description author archive', 'gctheme' ), $author->name() );

	$author_id = $author->ID;

	$socialmedia = array();

	if (!empty($author->facebook)) {
		$socialmedia[] = array("name"=>"facebook","link"=>$author->facebook);
	}
	if (!empty($author->instagram)) {
		$socialmedia[] = array("name"=>"instagram","link"=>$author->instagram);
	}
	if (!empty($author->linkedin)) {
		$socialmedia[] = array("name"=>"linkedin","link"=>$author->linkedin);
	}
	if (!empty($author->myspace)) {
		$socialmedia[] = array("name"=>"myspace","link"=>$author->myspace);
	}
	if (!empty($author->soundcloud)) {
		$socialmedia[] = array("name"=>"soundcloud","link"=>$author->soundcloud);
	}
	if (!empty($author->tumblr)) {
		$socialmedia[] = array("name"=>"tumblr","link"=>$author->tumblr);
	}
	if (!empty($author->twitter)) {
		$socialmedia[] = array("name"=>"twitter","link"=>"https://twitter.com/".$author->twitter);
	}
	if (!empty($author->youtube)) {
		$socialmedia[] = array("name"=>"youtube","link"=>$author->youtube);
	}
	if (!empty($author->wikipedia)) {
		$socialmedia[] = array("name"=>"wikipedia","link"=>$author->wikipedia);
	}




	$context['socialmediachannels'] = $socialmedia;

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

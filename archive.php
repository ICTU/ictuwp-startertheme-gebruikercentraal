<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */


$templates = [ 'archive.twig', 'index.twig' ];

$context = Timber::context();

$context['title'] = 'Archive';
if ( is_day() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'D M Y' );
} elseif ( is_month() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'M Y' );
} elseif ( is_year() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'Y' );
} elseif ( is_tag() ) {
	$context['title'] = single_tag_title( '', FALSE );
} elseif ( is_category() ) {
	$context['title'] = single_cat_title( '', FALSE );
	array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
} elseif ( is_post_type_archive() ) {
	$context['title'] = post_type_archive_title( '', FALSE );
	array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}


$context['overview'] = [];

// Set data for tipkaarts
if ( $context['pagetype'] === 'archive_tipthema' ) {

	$posts = new Timber\PostQuery();


	// Set data for overview
	$i = 0;
	foreach ( $posts as $post ) {
		$i ++;

		if ( $post->type->name == 'tips' ) {
			$terms = get_the_terms( $post->ID, 'tipthema' );

			$items[ $i ]['title']    = $post->post_title;
			$items[ $i ]['nr']       = $post->tip_nummer;
			$items[ $i ]['category'] = $terms[0]->name;
			$items[ $i ]['url'] = get_permalink($post);
		}
	}


	$context['overview']['items']    = $items;
	$context['overview']['template'] = 'card--tipkaart';
	$context['overview']['modifier'] = '4col';

} else {
	$context['posts'] = new Timber\PostQuery();
}

Timber::render( $templates, $context );

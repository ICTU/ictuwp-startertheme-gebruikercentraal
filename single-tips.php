<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context             = Timber::context();
$timber_post         = Timber::query_post();
$context['post']     = $timber_post;
$context['category'] = 'meh';


// kleur en icoon bepalen
if ( taxonomy_exists( GC_TIPTHEMA ) ) {

	$taxonomie = get_the_terms( $post->ID, GC_TIPTHEMA );

	if ( $taxonomie && ! is_wp_error( $taxonomie ) ) {
		$counter = 0;
		// tip slug
		foreach ( $taxonomie as $term ) {

			$themakleur = get_field( 'kleur_en_icoon_tipthema', GC_TIPTHEMA . '_' . $term->term_id );

			if ( $themakleur ) {
				$context['category'] = $themakleur;
			}
		}
	}
}


Timber::render( array(
	'single-tips.twig',
	'single.twig'
), $context );

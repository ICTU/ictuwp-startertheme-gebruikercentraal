<?php
/**
 * The Template for displaying all single tips
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context     = Timber::context();
$timber_post = Timber::query_post();

$context['post']     = $timber_post;
$context['category'] = 'default'; // let op: dit is NIET de blog category, maar de custom taxonomie GC_TIPTHEMA
$context['examples'] = [];
$context['links']    = [];


// kleur en icoon bepalen
if ( taxonomy_exists( GC_TIPTHEMA ) ) {

	$taxonomie = get_the_terms( $post->ID, GC_TIPTHEMA );

	if ( $taxonomie && ! is_wp_error( $taxonomie ) ) {
		$counter = 0;
		// tip slug

		foreach ( $taxonomie as $term ) {

			$term_machine_name   = strtolower( $term->name );
			$context['category'] = $term_machine_name;

			$i     = 0;
			$items = [];

			// Vullen related posts
			$related = get_posts( [
				'post_type'   => 'tips',
				'numberposts' => 4,
				'orderby'     => 'rand',
				'tax_query'   => [
					[
						'taxonomy'         => 'tipthema',
						'field'            => 'term_id',
						'terms'            => $term->term_id,
						'include_children' => FALSE,
					],
				],
			] );

			$voorbeeld = [];

			// Data klaarzetten voor related blok
			foreach ( $related as $item ) {
				$i ++;
				$items[ $i ]['title']    = $item->post_title;
				$items[ $i ]['nr']       = '1';
				$items[ $i ]['category'] = $term_machine_name;
				$items[ $i ]['nr']       = get_field( 'tip-nummer', $item->ID );

			}

			$context['related']['title'] = 'Meer ' . strtolower( $term->name ) . ' tips';
			$context['related']['items'] = $items;

			$context['related']['cta'] = [
				'title' => 'Alle ' . $term_machine_name . ' tips',
				'url'   => get_site_url() . '/tipthema/' . $term->slug,
			];
		}
	}


}

// vullen array voor goede voorbeelden
if ( have_rows( 'goed_voorbeeld' ) ):

	while ( have_rows( 'goed_voorbeeld' ) ) : the_row();

		$voorbeeld                       = [];
		$experts                         = get_sub_field( OD_CITAATAUTEUR . '_field' );
		$voorbeeld['title']              = get_sub_field( 'titel_goed_voorbeeld' );
		$voorbeeld['descr']              = get_sub_field( 'tekst_goed_voorbeeld' );
		$voorbeeld['author']             = [];
		$voorbeeld['author']['name']     = get_sub_field( 'voorbeeld-auteur-naam' );
		$voorbeeld['author']['function'] = get_sub_field( 'voorbeeld-auteur-functie' );
		$afbeelding_goed_voorbeeld       = get_sub_field( 'afbeelding_goed_voorbeeld' );

		if ( $afbeelding_goed_voorbeeld['sizes']['thumbnail'] ) {
			$voorbeeld['author']['img'] = $afbeelding_goed_voorbeeld['sizes']['thumbnail'];
		}

		if ( $experts && ( $experts[0] > 0 ) ) {

			// liever de gegevens uit de taxonomie OD_CITAATAUTEUR dan de losse velden hier ingevoerd

			foreach ( $experts as $theterm ) {

				$thetermdata = get_term( $theterm, OD_CITAATAUTEUR );

				if ( ! empty( $thetermdata->taxonomy ) ) {
					$acfid         = $thetermdata->taxonomy . '_' . $thetermdata->term_id;
					$tipgever_foto = get_field( 'tipgever_foto', $acfid );
				}

				if ( ! empty( $thetermdata->name ) ) {

					$voorbeeld['author']['name'] = $thetermdata->name;

					// aanname: alle namen bestaan uit meerdere woorden, gescheiden door een spatie
					$voornaam           = $thetermdata->name;
					$voornaamachternaam = explode( ' ', $thetermdata->name );
					if ( $voornaamachternaam[0] ) {
						$voornaam = $voornaamachternaam[0];
					}
				}

				if ( get_field( 'tipgever_functietitel', $acfid ) ) {
					$voorbeeld['author']['function'] = get_field( 'tipgever_functietitel', $acfid );
				}

				if ( ! empty( $thetermdata ) && ( $thetermdata->count > 1 ) && $voornaam ) {
					$voorbeeld['author']['url']      = get_term_link( $thetermdata->term_id );
					$voorbeeld['author']['linktext'] = sprintf( _x( 'Meer tips van %s', 'linktext auteur voorbeeld', 'gctheme' ), $voornaam );
				}
				if ( $tipgever_foto['sizes']['thumbnail'] ) {
					$voorbeeld['author']['img'] = $tipgever_foto['sizes']['thumbnail'];
				}
			}

		}

		$context['examples'][] = $voorbeeld;

	endwhile;

endif;

// vullen array voor goede voorbeelden
if ( have_rows( 'nuttige_links' ) ):

	while ( have_rows( 'nuttige_links' ) ) : the_row();

		$currenturl        = [];
		$currenturl['url'] = get_sub_field( 'url' );
		if ( get_sub_field( 'link_beschrijving' ) ) {
			$currenturl['descr'] = strip_tags( get_sub_field( 'link_beschrijving' ) );
		}
		if ( get_sub_field( 'link_titel' ) ) {
			$currenturl['title'] = get_sub_field( 'link_titel' );
		} else {
			if ( $currenturl['descr'] ) {
				$words               = explode( ' ', trim( $currenturl['descr'] ) );
				$currenturl['title'] = $words[0];
			} else {
				$currenturl['title'] = _x( 'Link', 'default link text', 'gctheme' );
			}
		}

		$context['links'][] = $currenturl;

	endwhile;

endif;

if ( get_field( 'waarom_werkt_dit_goed_voorbeeld' ) ) {
	$context['why']['title']       = _x( 'Waarom werkt dit?', 'Titel boven waaromwerktdit', 'gctheme' );
	$context['why']['description'] = get_field( 'waarom_werkt_dit_goed_voorbeeld' );
}

if ( get_field( 'inleiding-onderzoek' ) ) {

	$context['research']['title']       = _x( 'Onderzoek', 'Titel boven waaromwerktdit', 'gctheme' );
	$context['research']['description'] = get_field( 'inleiding-onderzoek' );

	if ( get_field( 'inleiding-conclusie' ) ) {
		$context['research']['conclusie']['title'] = _x( 'Conclusie', 'Titel boven conclusie', 'gctheme' );
		$context['research']['conclusie']['desc']  = get_field( 'inleiding-conclusie' );
	}

	$context['research']['blocks'] = [];

	for ( $x = 0; $x <= 3; $x ++ ) {

		if ( get_field( 'inleiding-vraag_' . $x . '_titel' ) ) {

			$cijfers          = [];
			$cijfers['title'] = get_field( 'inleiding-vraag_' . $x . '_titel' );
			$cijfers['nr']    = get_field( 'inleiding-vraag_' . $x . '_-_cijfer' );
			$cijfers['descr'] = get_field( 'inleiding-vraag_' . $x . '_-_antwoord' );

			$context['research']['blocks'][] = $cijfers;

		}
	}

	$context['why']['title']       = _x( 'Waarom werkt dit?', 'Titel boven waaromwerktdit', 'gctheme' );
	$context['why']['description'] = get_field( 'waarom_werkt_dit_goed_voorbeeld' );
}


Timber::render( [
	'single-tip.twig',
	'single.twig',
], $context );

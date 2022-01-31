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
$maxnr_tips          = 4;
$term_id_related     = 0;

// kleur en icoon van deze tip bepalen
if ( taxonomy_exists( GC_TIPTHEMA ) ) {

	$taxonomie = get_the_terms( $post->ID, GC_TIPTHEMA );

	if ( $taxonomie && ! is_wp_error( $taxonomie ) ) {

		foreach ( $taxonomie as $term ) {

			$term_machine_name   = strtolower( $term->name );
			$context['category'] = $term_machine_name;

		}
	}
}

if ( 'nee' !== get_field( 'relatedtips_show_or_not', $post ) ) {
	// het is niet nee, dus het is ja.
	// hoera, we mogen gerelateerde tips tonen

	if ( 'ja_redactioneel' == get_field( 'relatedtips_show_or_not', $post ) ) {

		// de redactie heeft zelf een aantal gerelateerde tips gekozen
		// deze kunnen allerlei tipthema's hebben, dus geen doorklik

		$featured_posts = get_field( 'relatedtips_handpicked_tips', $post );

		if ( $featured_posts ):

			$counter = 0;

			foreach ( $featured_posts as $post ):

				setup_postdata( $post );
				$counter ++;

				// Data klaarzetten voor related blok
				$tax_van_tip                   = get_the_terms( $post->ID, GC_TIPTHEMA );
				$items[ $counter ]['title']    = od_wbvb_custom_post_title( $post->post_title );
				$items[ $counter ]['url']      = get_the_permalink( $post );
				$items[ $counter ]['category'] = $tax_van_tip[0]->name;
				$items[ $counter ]['nr']       = get_field( 'tip-nummer', $post->ID );


			endforeach;

			// hier geen doorklik en een wat algemenere titel
			$context['related']['title'] = _x( 'Gerelateerde tips', 'Titel gerelateerde tips', 'gctheme' );
			$context['related']['items'] = $items;

		endif;

	} else {
		// 'relatedtips_show_or_not' is niet 'nee' en is niet 'ja_redactioneel',
		// dus we stellen zelf een lijstje van gerelateerde tips samen

		// max vier, tenzij redactie meer tips wil
		$maxnr_tips = ( intval( get_field( 'relatedtips_maxnr' ) ) > 0 ) ? get_field( 'relatedtips_maxnr' ) : 4;

		$current_term = get_the_terms( $timber_post->id, 'tipthema' );

		// Vullen lijst gerelateerde tips.
		// Letten op post status en dat we niet nog een keer dezelfde tip tonen
		$args = array(
			'post_type'    => GC_TIP_CPT,
			'numberposts'  => $maxnr_tips,
			'orderby'      => 'rand',
			'tax_query'    => [
				[
					'taxonomy'         => GC_TIPTHEMA,
					'field'            => 'term_id',
					'terms'            => $current_term[0]->term_id,
					'include_children' => false,
				],
			],
			'post__not_in' => [ $post->ID ],
			'post_status'  => 'publish',
		);

		$relatedtips = new WP_Query( $args );

		if ( $relatedtips->have_posts() ) {

			$counter = 0;


			while ( $relatedtips->have_posts() ) {


				$relatedtips->the_post();
				$counter ++;
				//tipkaart__nummer

				// Data klaarzetten voor related blok
				$items[ $counter ]['title']      = od_wbvb_custom_post_title( $post->post_title ); // TODO: deze functie tot een filter maken voor titels überhaupt
				$items[ $counter ]['url']        = get_the_permalink( $post );
				$items[ $counter ]['category']   = $term_machine_name;
				$items[ $counter ]['tip_nummer'] = sprintf( _x( 'Tip %s', 'Label tip-nummer', 'gctheme' ), get_post_meta( $post->ID, 'tip-nummer', true ) );
				$voorbeeld                       = [];


				$context['related']['title'] = 'Meer ' . strtolower( $term->name ) . ' tips';
				$context['related']['items'] = $items;

				$context['related']['cta'] = [
					'title' => 'Alle ' . $term_machine_name . ' tips',
					'url'   => get_site_url() . '/tipthema/' . $term->slug,
				];
			}

			/* Restore original Post Data */
			wp_reset_postdata();

			$context['related']['title'] = sprintf( _x( 'Meer tips over %s', 'Titel gerelateerde tips', 'gctheme' ), strtolower( $term->name ) );
			$context['related']['items'] = $items;
			$context['related']['cta']   = [
				'title' => sprintf( _x( 'Alle tips over %s', 'Linktekst overzicht tipthema', 'gctheme' ), strtolower( $term->name ) ),
				'url'   => get_term_link( $term->term_id ),
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

		if ( isset($afbeelding_goed_voorbeeld['sizes']['thumbnail'] )) {
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

				if ( ! empty( $acfid ) && get_field( 'tipgever_functietitel', $acfid ) ) {
					$voorbeeld['author']['function'] = get_field( 'tipgever_functietitel', $acfid );
				}

				if ( ! empty( $thetermdata ) && ( $thetermdata->count > 1 ) && $voornaam ) {
					$voorbeeld['author']['url']      = get_term_link( $thetermdata->term_id );
					$voorbeeld['author']['linktext'] = sprintf( _x( 'Meer tips van %s', 'linktext auteur voorbeeld', 'gctheme' ), $voornaam );
				}
				if ( ! empty( $tipgever_foto ) && $tipgever_foto['sizes']['thumbnail'] ) {
					$voorbeeld['author']['img'] = $tipgever_foto['sizes']['thumbnail'];
					if ( $tipgever_foto['alt']) {
						$voorbeeld['author']['img_alt'] = $tipgever_foto['alt'];
					} elseif ( $tipgever_foto['title'] ) {
						$voorbeeld['author']['img_alt'] = $tipgever_foto['title'];
					} elseif ( ! empty( $thetermdata->name ) ) {
						$voorbeeld['author']['img_alt'] = $thetermdata->name;
					} else {
						$voorbeeld['author']['img_alt'] = '';
					}
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

if ( 'ja' === get_field( 'downloads_tonen' ) && get_field( 'download_items' ) ) {
	$context['downloads'] = download_block_get_data();

}


Timber::render( [
	'single-tip.twig',
	'single.twig',
], $context );

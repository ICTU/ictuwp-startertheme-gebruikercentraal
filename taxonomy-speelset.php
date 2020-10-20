<?php
/**
 * Overzicht voor tips in een speelset.
 * De speelset-taxonomie heeft via ACF-velden ook de mogelijkheid om tips automagisch
 * bij een speelset te halen.
 * Via de keuze 'speelset_selectiemethode' kun je kiezen voor de standaardmanier van een
 * taxonomie gebruiken OF je kunt een automatische methode gebruiken.
 * Op dit moment (sept 2020) zijn er twee automatische methodes:
 * 1) een speelset bestaande uit alle kaarten die een toptip zijn (ACF-veld: 'is_toptip' = 1)
 * 2) een speelset bestaande uit de tips uit 1 of meer thema's (bijv. het thema 'inclusie')
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */


$context = Timber::context();
$i       = 0; // Set data for overview

// Set vars
$context['title'] = get_the_archive_title();

// If term archive
if ( isset( $context['archive_term'] ) && ! empty( $context['archive_term']['descr'] ) ) {
	$context['descr'] = $context['archive_term']['descr'];
}


$term      = get_queried_object();
$the_query = null;
$methode   = get_field( 'speelset_selectiemethode', $term );

if ( 'default' !== $methode ) {
	// we gaan alleen de query veranderen voor custom queries

	$robot_criteria = get_field( 'robot_criteria', $term );
	$args           = array(
		'numberposts' => - 1, // sowieso geen paginering
		'post_type'   => GC_TIP_CPT, // sowieso alleen tips tonen
	);

	if ( 'tips_with_star' === $robot_criteria ) {
		// 1) een speelset bestaande uit alle kaarten die een toptip zijn (ACF-veld: 'is_toptip' = 1)
		$args['meta_key']   = 'is_toptip';
		$args['meta_value'] = '1';

	} elseif ( 'tips_in_thema' === $robot_criteria ) {
		// 2) een speelset bestaande uit de tips uit 1 of meer thema's (bijv. het thema 'inclusie')
		$select_tipthema = get_field( 'select_tipthema', $term );
		if ( $select_tipthema ):
			$getterms = array();
			foreach ( $select_tipthema as $term ):
				$getterms[] = $term;
			endforeach;

			$args['tax_query'] = array(
				array(
					'taxonomy' => GC_TIPTHEMA,
					'field'    => 'term_id',
					'terms'    => $getterms,
					'operator' => 'IN',
				),
			);

		endif;

	} else {
		echo 'Onbekend selectiecriterium voor deze speelset: ' . esc_html( $robot_criteria ) . '<br>';
	}

	if ( $args ) {
		$the_query = new WP_Query( $args );
	}

	if ( $the_query->have_posts() ):
		while ( $the_query->have_posts() ) : $the_query->the_post();

			$i ++;

			$items[ $i ] = prepare_card_content( $post );

		endwhile;
	endif;

} else {
	$poststemp = new Timber\PostQuery();

	foreach ( $poststemp as $post ) {
		$i ++;
		$items[ $i ] = prepare_card_content( $post );
	}
}

// Set data for overview
$context['overview']             = [];
$context['overview']['items']    = $items;
$context['overview']['template'] = 'card--tipkaart';
$context['overview']['modifier'] = 'col-4'; // Set 4 column grid for tipgevers. Default is col-3

// Get all data from the term
$archive       = get_queried_object();
$taxonomy_name = $archive->taxonomy;
$cat           = get_term( $archive->term_id );

// Set title and short description
$context['author']['title'] = $archive->name;
$context['author']['descr'] = ( $cat->description ? $cat->description : '' );

// Set overview


/*
 *
echo '<pre>';
var_dump( $context['overview'] );
echo '</pre>';
 */

$templates = array(
	'archive-tip-tax.twig',
	'archive.twig',
	'index.twig',
);


Timber::render( $templates, $context );

<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

global $wp_query;

$templates = array( 'search.twig', 'archive.twig', 'index.twig' );

$context     = Timber::context();
$searchterm  = null;
$name_engine = 'supplemental';
if ( isset( $_GET['searchwp'] ) ) {
	// a search query for the supplemental engine
	$searchterm = sanitize_text_field( $_GET['searchwp'] );
} elseif ( isset( $_GET['s'] ) ) {
	$searchterm = sanitize_text_field( $_GET['s'] );
}
$number_results = 0;

// default title, voor als er geen zoekterm bekend is
$context['title'] = _x( "Please enter a search term", 'Search results - title', 'gctheme' );

// maak het mogelijk dat tipgevers ook gevonden worden op gedeeltes van hun naam
add_filter( 'searchwp_tax_term_or_logic', '__return_true' );

if ( $searchterm ) {

	$posts_per_page    = $wp_query->query_vars['posts_per_page'];
	$currentpagenumber = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$pagenumber_start  = ( $posts_per_page * $currentpagenumber ) - ( $posts_per_page - 1 );

	// SearchWP biedt aparte zoekfunctionaliteit zoals bijvoorbeeld
	// het uitlichten van taxonomieen, zoals tipgevers.
	// Tot aug. gebruikten we versie 3, daarna upgrade naar versie 4.

	if ( property_exists( 'SearchWP', 'instance' ) ) {
		// SearchWP v3.x
		$engine = SearchWP::instance();     // instatiate SearchWP

		// check of 'supplemntal bestaat'
		if ( $engine->settings['engines'][ $name_engine ] ) {
			// de supplemental engine bestaat,
			// dus ws kunnen we zoeken met taxonomy priority
		} else {
			// ja dan niet hoor
			$name_engine = 'default';
		}

		$posts = $engine->search( $name_engine, $searchterm, $currentpagenumber );

	} elseif ( class_exists( '\\SearchWP\\Query' ) ) {
		// SearchWP v4.x
		$search_page    = isset( $_GET['swppg'] ) ? absint( $_GET['swppg'] ) : 1;
		$searchwp_query = new \SearchWP\Query( $searchterm, [
			'engine' => $name_engine, // The Engine name.
			'fields' => 'all',          // Load proper native objects of each result.
			'page'   => $search_page
		] );

		$posts = $searchwp_query->get_results();

		// TODO
		// paginering aanpassen voor grote hoeveelheden zoekresultaten
		// zie https://trello.com/c/AM65KJpw/449-searchwp-updaten
		// en https://trello.com/c/5kxi8KXX/671-paginering-blog-zoekresultaten-od-bevinding-33
		$search_pagination = paginate_links( array(
			'format'  => '?swppg=%#%',
			'current' => $search_page,
			'total'   => $searchwp_query->max_num_pages,
		) );

	} else {
		// we gebruiken de standaard WP-zoekmachine
		// jammer, maar helaas
		// pindakaas enzo

		if ( $wp_query->have_posts() ) {
			$counter = 0;

			$number_results = $wp_query->found_posts;

			while ( $wp_query->have_posts() ) {

				$counter ++;

				$wp_query->the_post();

				$card = gc_search_prepare_card( $post );

				if ( $card ) {
					$context['results'][] = $card;
				}
			}
		}

		if ( ( $posts_per_page * $currentpagenumber ) <= ( $number_results ) ) {
			$pagenumber_end = ( $posts_per_page * $currentpagenumber );
		} else {
			$pagenumber_end = $number_results;
		}

		if ( $number_results % $posts_per_page == 0 ) {
			$totalnumberofpages = round( $number_results / $posts_per_page, 0 );
		} else {
			$totalnumberofpages = round( $number_results / $posts_per_page + .49, 0 );
		}

		if ( $pagenumber_start === $pagenumber_end ) {
			$pagenumber_start_to = $pagenumber_start;
		} else {
			$pagenumber_start_to = sprintf( _x( "%s to %s", "Search results - results per page", 'gctheme' ), $pagenumber_start, $pagenumber_end );
		}

		if ( $number_results ) {
			$context['title'] = sprintf( _n( "%s result for '%s'", "%s results for '%s'", $number_results, 'gctheme' ), $number_results, $searchterm );
			if ( $totalnumberofpages > 1 ) {
				$context['descr'] = sprintf( _x( "You are on page %s of %s, showing %s.", "Search results", 'gctheme' ), $currentpagenumber, $totalnumberofpages, $pagenumber_start_to );
			}
		} else {
			$context['title'] = sprintf( _x( "No result for '%s'", 'Search results no results', 'gctheme' ), $searchterm );
			$context['descr'] = sprintf( _x( "Sorry. Maybe check your search term and try again.", "Search results", 'gctheme' ) );
		}

		$counter = 0;


	}


	if ( $posts ) {
		$context['title'] = sprintf( _x( "Results for '%s'", 'Search results no results', 'gctheme' ), $searchterm );
	} else {
		$context['title'] = sprintf( _x( "No result for '%s'", 'Search results no results', 'gctheme' ), $searchterm );
		$context['descr'] = sprintf( _x( "Sorry. Maybe check your search term and try again.", "Search results", 'gctheme' ) );
	}
	// TODO: tonen van aantal gevonden records
	// ik zou graag het aantal resultaten tonen en het aantal pagina's maar dat werkt voor SearchWP blijkbaar anders
	// dan je zou verwachten.
	// oh well ¯\_(ツ)_/¯

	foreach ( $posts as $post ) :
		$card = gc_search_prepare_card( $post );

		if ( $card ) {
			$context['results'][] = $card;
		}

	endforeach;

	wp_reset_postdata();


}

Timber::render( $templates, $context );


/**
 * Format data from a postobjet for use within a single twig card in the search results
 *
 * @param object $post_object The object (maybe a post, page, or a taxonomy term)
 *
 * @return  array                   Array with data for card, or null
 */

function gc_search_prepare_card( $post_object ) {
	$return   = null;
	$card     = [];
	$posttype = get_post_type( $post_object->ID );

	if ( 'SearchWPTermResult' == get_class( $post_object ) ) {

		// dit is een taxonomie
		$card['title']             = $post_object->name;
		$card['link']              = $post_object->link;
		$card['ID']                = $post_object->term->taxonomy . '-' . $post_object->term->term_id;
		$card['post_excerpt']      = wp_strip_all_tags( $post_object->description );
		$card['post_type_display'] = $post_object->taxonomy;
		$posttype                  = $post_object->term->taxonomy;

		if ( 'tipgever' == $post_object->term->taxonomy ) {
			// Tipgevers is een taxonomie op Optimaal Digitaal. Deze taxonomie koppelt een tip aan
			// een tipgever, i.e. een persoon met naam, foto en contactgegevens.
			if ( get_field( 'tipgever_functietitel', $post->term ) ) {
				// tipgevers hebben een apart ACF veld
				$card['post_excerpt'] = wp_strip_all_tags( get_field( 'tipgever_functietitel', $post->term ) );
			}
		}

	} else {

		$postid                    = $post_object->ID;
		$card['title']             = od_wbvb_custom_post_title( get_the_title( $postid ) );
		$card['link']              = get_the_permalink( $postid );
		$card['ID']                = $postid;
		$card['post_excerpt']      = get_the_excerpt( $postid );
		$card['post_type_display'] = translate_posttype( $posttype );

		/*
		 * het kan voor sommige mensen in sommige omstandigheden heel informatief zijn om te weten wanneer
		 * een post voor het laatst gewijzigd is
		 */
		// $card['last_changed'] = get_the_modified_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $postid );

		if ( 'page' !== $posttype ) {
			$card['post_type_display'] = translate_posttype( $posttype );
			if ( GC_TIP_CPT !== $posttype ) {
				$card['post_date'] = get_the_date() . ' - ' . $posttype;
			}
		}
	}

	if ( 'post' === $posttype ) {
		$author_id = get_post_field( 'post_author', $postid );
		if ( $author_id ) {
			$card['author'] = get_the_author_meta( 'display_name', $author_id );
		}
		$taxonomie = get_the_terms( $postid, 'category' );

		if ( $taxonomie && ! is_wp_error( $taxonomie ) ) {
			$categories = array();
			foreach ( $taxonomie as $term ) {
				$categories[] = $term->name;
			}
		}

		$card['post_type_display'] = implode( ', ', $categories );

	}

	$card['post_type'] = $posttype;

	if ( $card['title'] ) {
		$return = $card;
	}

	return $return;

}
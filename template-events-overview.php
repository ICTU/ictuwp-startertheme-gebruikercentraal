<?php
/**
 * Template Name: Template Events-overzicht
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


// Voorkomen dat de Event Manager alsnog extra meuk toevoegt aan de content
add_action( 'wp_head', 'remove_em_content_filter', 1000 );

$context           = Timber::context();
$timber_post       = new Timber\Post();
$context['post']   = $timber_post;

$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}

$context['events'] = overviewpage_get_items();
Timber::render( [ 'archive-events.twig', 'page.twig' ], $context );




//========================================================================================================

function overviewpage_get_items() {

	global $post;

	$return              = array();

	// voor de pagination, eerst bepalen hoeveel events er klaar staan
	$count_events        = EM_Events::get( array(
		'scope'      => 'future',
		'pagination' => '0',
	) );
	$total_number_events = count( $count_events );
	// het aantal events per page, uit de EM-instellingen
	$posts_per_page = get_option( 'dbem_events_default_limit' );
	// voor pagination, het gevraagde paginanummer
	$current_page_number = ( ! empty( $_GET['pno'] ) ) ? $_GET['pno'] : 1;

	$args_selection  = array(
		'scope'      => 'future', // alleen toekomstige events tonen
		'pagination' => '1', // ja, we willen pagination
		'limit'      => $posts_per_page, // het aantal events per pagina
		'page'       => $current_page_number, // het huidige paginanummer
	);
	$EM_Events          = EM_Events::get( $args_selection );

	if ( $EM_Events ) {

		// hoera
		// er zijn events...

		$return['overview'] = 'events';

		$pagination = do_paginate(
			get_the_permalink( $post ),
			$total_number_events,
			$posts_per_page, // het aantal events per pagina
			$current_page_number, // het huidige paginanummer
			$args_selection
		);
		if ( $pagination ) {
			$return['pagination'] = $pagination;
		}

		foreach ( $EM_Events as $EM_Event ) {
			setup_postdata( $EM_Event );
			$postid               = $EM_Event->ID;
			$event_start_date     = get_post_meta( $postid, '_event_start_date', true );
			$event_start_time     = get_post_meta( $postid, '_event_start_time', true );
			$event_end_date       = get_post_meta( $postid, '_event_end_date', true );
			$event_end_time       = get_post_meta( $postid, '_event_end_time', true );
			$event_start_datetime = strtotime( $event_start_date . ' ' . $event_start_time );
			$event_end_datetime   = strtotime( $event_end_date . ' ' . $event_end_time );

			if ( date_i18n( 'Y', $event_start_datetime ) === date("Y" ) ) {
				// dit event valt in dit jaar, dus we tonen niet het jaartal in de titel
				$monthyeartitle = ucwords( date_i18n( 'F', $event_start_datetime ) );
			} else {
				// wel het jaartal tonen in de titel
				$monthyeartitle = ucwords( date_i18n( 'F Y', $event_start_datetime ) );
			}

			$monthyearkey   = date_i18n( 'Y-m', $event_start_datetime );

			$return['months'][ $monthyearkey ]['title']   = $monthyeartitle;
			$return['months'][ $monthyearkey ]['items'][] = prepare_card_content( $EM_Event );
		}
	}

	wp_reset_query();

	return $return;

}

//========================================================================================================
/*
 * Voorkomen dat em_content extra text toevoegt aan The Content
 */
function remove_em_content_filter() {
	remove_filter( 'the_content', 'em_content' );
}

//========================================================================================================
/*
 * pagination op de events overzichtpagina
 */

function do_paginate( $link, $total, $limit, $page = 1, $data = array() ) {

	if ( $total <= $total ) {
		return;
	}

	if ( $limit > 0 ) {
		$pagesToShow      = defined( 'EM_PAGES_TO_SHOW' ) ? EM_PAGES_TO_SHOW : 10;
		$url_parts        = explode( '?', $link );
		$base_link        = $url_parts[0];
		$base_querystring = '';
		$data_atts        = '';
		//Get querystring for first page without page
		if ( count( $url_parts ) > 0 ) {
			$query_arr = array();
			parse_str( $url_parts[1], $query_arr );
			//if $data was passed, strip any of these vars from both the $query_arr and $link for inclusion in the data-em-ajax attribute
			if ( ! empty( $data ) && is_array( $data ) && ( ! defined( 'EM_USE_DATA_ATTS' ) || EM_USE_DATA_ATTS ) ) {
				//remove the data attributes from $query_arr
				foreach ( array_keys( $data ) as $key ) {
					if ( array_key_exists( $key, $query_arr ) ) {
						unset( $query_arr[ $key ] );
					}
				}
				//rebuild the master link, without these data attributes
				if ( count( $query_arr ) > 0 ) {
					$link = $base_link . '?' . build_query( $query_arr );
				} else {
					$link = $base_link;
				}
				$data_atts = 'data-em-ajax="' . esc_attr( build_query( $data ) ) . '"'; //for inclusion later on
			}
			//proceed to build the base querystring without pagination arguments
			unset( $query_arr['page'] );
			unset( $query_arr['pno'] );
			$base_querystring = esc_attr( build_query( $query_arr ) );
			if ( ! empty( $base_querystring ) ) {
				$base_querystring = '?' . $base_querystring;
			}
		}
		//calculate
		$maxPages    = ceil( $total / $limit ); //Total number of pages
		$startPage   = ( $page <= $pagesToShow ) ? 1 : $pagesToShow * ( floor( $page / $pagesToShow ) ); //Which page to start the pagination links from (in case we're on say page 12 and $pagesToShow is 10 pages)
		$placeholder = urlencode( '%PAGE%' );
		$link        = str_replace( '%PAGE%', $placeholder, esc_url( $link ) ); //To avoid url encoded/non encoded placeholders
		//Add the back and first buttons
		$string = ( $page > 1 && $startPage != 1 ) ? '<li class="first btn"><a class="prev page-numbers" href="' . str_replace( $placeholder, 1, $link ) . '" aria-label="' . _x('First', 'pagination', 'gctheme') . '">&lt;&lt;</a></li>' : '';
		if ( $page == 2 ) {
			$string .= '<li><a class="prev page-numbers" href="' . esc_url( $base_link . $base_querystring ) . '" aria-label="' . _x('Previous', 'pagination', 'gctheme') . '">&lt;</a></li>';
		} elseif ( $page > 2 ) {
			$string .= '<li><a class="prev page-numbers" href="' . esc_url( $base_link . $base_querystring . '?pno=' . ( $page - 1 )  ) . '" aria-label="' . _x('Previous', 'pagination', 'gctheme') . '">&lt;</a></li>';
		}
		//Loop each page and create a link or just a bold number if its the current page
		for ( $i = $startPage; $i < $startPage + $pagesToShow && $i <= $maxPages; $i ++ ) {
			if ( $i == $page || ( empty( $page ) && $startPage == $i ) ) {
				$string .= '<li class="current"><span class="page-number page-numbers current">' . $i . '</span></li>';
			} elseif ( $i == '1' ) {
				$string .= '<li><a class="page-numbers" href="' . esc_url( $base_link . $base_querystring ) . '">' . $i . '</a></li>';
			} else {
				$string .= '<li><a class="page-numbers" href="' . esc_url( $base_link . $base_querystring . '?pno=' . $i ) . '">' . $i . '</a></li>';
			}
		}
		//Add the forward and last buttons
		$string .= ( $page < $maxPages ) ? '<li class="next btn"><a class="next page-numbers" href="' . esc_url( $base_link . $base_querystring . '?pno=' . ( $page + 1 )  ) . '" aria-label="' . _x('Next', 'pagination', 'gctheme') . '">&gt;</a></li>' : ' ';
		$string .= ( $i - 1 < $maxPages ) ? '<li class="next btn"><a class="next page-numbers" href="' . str_replace( $placeholder, $maxPages, $link ) . '" aria-label="' . _x('Last', 'pagination', 'gctheme') . '">&gt;&gt;</a></li>' : ' ';

		//Return the string
		return '<nav class="pagination-block"><ul class="pagination">' . $string . '</ul></nav>';
	}
}

//========================================================================================================


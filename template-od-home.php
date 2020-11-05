<?php
/**
 * Template Name: [OD] Homepage
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;
$defaultimage    = get_template_directory_uri() . '/flavors/optimaaldigitaal/assets/images/od-default.jpg';

if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

	$context['related'] = related_block_get_data();

}


$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}


$teaserblocks = teaser_block_get_data();

if ( $teaserblocks ) {

	$context['teaserblocks'] = $teaserblocks;

}

//$imagesize_for_thumbs = 'thumb-cardv3';
//$imagesize_for_thumbs = BLOG_SINGLE_DESKTOP;
$imagesize_for_thumbs = IMAGESIZE_16x9;

// events selecteren
if ( class_exists( 'EM_Events' ) ) {

	$args = array(
		'limit' => 2,
		'array' => true
	);

	$events = EM_Events::get( $args );

	if ( $events ) {

		if ( get_option( 'dbem_events_page' ) ) {

			// de titel boven dit blok is exact gelijk aan de pagina-titel van de event page
			$context['actueel']['events']['title']        = get_the_title( get_option( 'dbem_events_page' ) );
			$context['actueel']['events']['cta']['url']   = get_permalink( get_option( 'dbem_events_page' ) );
			$context['actueel']['events']['cta']['title'] = get_the_title( get_option( 'dbem_events_page' ) );
			$context['actueel']['events']['cta']['url']   = get_permalink( get_option( 'dbem_events_page' ) );
		}


		foreach ( $events as $event ):
			$item                 = array();
			$item['title']        = $event['event_name'];
			$item['url']          = get_the_permalink( $event['post_id'] );
			$image                = get_the_post_thumbnail_url( $event['post_id'], $imagesize_for_thumbs );
			$event_start_date     = $event['event_start_date'];
			$event_start_time     = $event['event_start_time'];
			$event_end_date       = $event['event_end_date'];
			$event_end_time       = $event['event_end_time'];
			$event_end_datetime   = strtotime( $event_end_date . ' ' . $event_end_time );
			$event_start_datetime = strtotime( $event_start_date . ' ' . $event_start_time );

			if ( $event_start_datetime === $event_end_datetime ) {
				$item['start_date'] = $event_start_datetime;
			} elseif ( $event_start_datetime && $event_end_datetime ) {
				$item['start_date'] = $event_start_datetime;
				$item['end_date']   = $event_end_datetime;
			}
			$item['arialabel']    = sprintf( _x( '%s op %s', 'Arialabel agenda home', 'gctheme' ), $event['event_name'], date_i18n( get_option( 'date_format' ) , $event_start_datetime ) );

			if ( ! $image ) {
				$item['img']     = $defaultimage;
				$item['img_alt'] = '';
			} else {
				$item['img']     = $image;
				$item['img_alt'] = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
			}
			$context['actueel']['events']['items'][] = $item;

		endforeach;

	}

}

// posts selecteren
$maxnr_posts = 2;
//	if ( isset( $context['actueel']['events'] ) ) {
//		$maxnr_posts = ( 4 - count( $context['actueel']['events']['items'] ) );
//	}
$args        = array(
	'post_type'   => 'post',
	'numberposts' => $maxnr_posts,
	'post_status' => 'publish',
);
$relatedtips = new WP_Query( $args );

if ( $relatedtips->have_posts() ) {

	$counter = 0;

	while ( $relatedtips->have_posts() ) {
		$relatedtips->the_post();
		$item  = array();
		$image = get_the_post_thumbnail_url( $post->ID, $imagesize_for_thumbs );

		if ( ! $image ) {
			$item['img']     = $defaultimage;
			$item['img_alt'] = '';
		} else {
			$item['img']     = $image;
			$item['img_alt'] = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
		}
		$item['title']                          = $post->post_title;
		$item['url']                            = get_the_permalink( $post );
		$context['actueel']['blogs']['items'][] = $item;

	}

	if ( get_option( 'default_category' ) ) {
		$termid                                      = get_option( 'default_category' );
		$context['actueel']['blogs']['cta']['url']   = get_category_link( $termid );
		$context['actueel']['blogs']['cta']['title'] = get_cat_name( $termid );
	} elseif ( get_option( 'page_for_posts' ) ) {
		$context['actueel']['blogs']['cta']['url']   = get_permalink( get_option( 'page_for_posts' ) );
		$context['actueel']['blogs']['cta']['title'] = get_the_title( get_option( 'page_for_posts' ) );
	}

	/* Restore original Post Data */
	wp_reset_postdata();
}

Timber::render( [ 'od-home.html.twig', 'page.twig' ], $context );




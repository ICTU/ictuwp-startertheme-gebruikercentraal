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


// events selecteren
if ( class_exists( 'EM_Events' ) ) {

	$args = array(
		'limit' => 2,
		'array' => true
	);

	$events = EM_Events::get( $args );

	if ( $events ) {

		foreach ( $events as $event ):
			$item                 = array();
			$startdate            = $event['event_start'];
			$event_start_datetime = strtotime( $startdate );
			$item['date']         = date_i18n( 'j M', $event_start_datetime );
			$item['title']        = $event['event_name'];
			$image                = get_the_post_thumbnail_url( $event['post_id'], 'large' );

			if ( ! $image ) {
				$item['img']     = $defaultimage;
				$item['img_alt'] = '';
			} else {
				$item['img']     = $image;
				$item['img_alt'] = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
			}
			$context['actueel']['events']['items'][] = $item;

		endforeach;

		if( get_option('dbem_events_page') ) {
			$context['actueel']['events']['cta']['url']   = get_permalink( get_option( 'dbem_events_page' ) );
			$context['actueel']['events']['cta']['title'] = get_the_title( get_option( 'dbem_events_page' ) );
			$context['actueel']['events']['cta']['url']   = get_permalink( get_option( 'dbem_events_page' ) );
		}

	}


}

// posts selecteren
$maxnr_posts  = 4;
if (isset( $context['actueel']['events'] )) {
	$maxnr_posts  = ( 4 - count( $context['actueel']['events']['items'] ) );
}
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
		$image = get_the_post_thumbnail_url( $post->ID, 'large' );

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

	if ( get_option( 'page_for_posts' ) ) {
		$context['actueel']['blogs']['cta']['url']   = get_permalink( get_option( 'page_for_posts' ) );
		$context['actueel']['blogs']['cta']['title'] = get_the_title( get_option( 'page_for_posts' ) );
	}

	/* Restore original Post Data */
	wp_reset_postdata();
}

// VOOR NU GEEN ACTUEEL
$context['actueel'] = [];

Timber::render( [ 'od-home.html.twig', 'page.twig' ], $context );




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

if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

	$context['related'] = related_block_get_data();

}


$spotlightblocks = spotlight_block_get_data();

if ( $spotlightblocks ) {

	$context['spotlight'] = $spotlightblocks;

}

// posts selecteren
$maxnr_tips  = 4;
$args        = array(
	'post_type'   => 'post',
	'numberposts' => $maxnr_tips,
	'post_status' => 'publish',
);
$relatedtips = new WP_Query( $args );
if ( $relatedtips->have_posts() ) {

	$counter      = 0;
	$defaultimage = get_template_directory_uri() . '/flavors/optimaaldigitaal/assets/images/od-default.jpg';

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

	if (  get_option( 'page_for_posts' ) ) {
		$context['actueel']['blogs']['cta']['url']   = get_permalink( get_option( 'page_for_posts' ) );
		$context['actueel']['blogs']['cta']['title'] = get_the_title( get_option( 'page_for_posts' ) );
	}

	/* Restore original Post Data */
	wp_reset_postdata();
}


// events selecteren
if ( class_exists( 'EM_Events' ) ) {
	$events = EM_Events::output( array( 'limit' => 2 ) );

	echo '<pre>';
	var_dump( $events );
	echo '</pre>';

}

if ( 22 === 33 ) {
	Timber::render( [
		'page-' . $timber_post->post_name . '.twig',
		'page.twig',
	], $context );

} else {
	Timber::render( [ 'od-home.html.twig', 'page.twig' ], $context );
}




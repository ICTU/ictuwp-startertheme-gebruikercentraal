<?php
// rijksvideo-block.php

add_action( 'acf/init', 'gb_add_rijksvideo_block' );

function gb_add_rijksvideo_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/rijksvideo',
			'title'           => _x( 'GC Rijksvideo', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Tonen van een video in een apart block', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_rijksvideo_block',
			'category'        => 'gc-blocks',
			'icon'            => 'video-alt2', // todo: eigen icon voor dit block
			'keywords'        => [ 'video', 'image' ],
		] );
	}
}

//========================================================================================================

function gb_render_rijksvideo_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['data'] = rijksvideo_block_get_data();

	Timber::render( 'components/video.html.twig', $context );

}


/*
 * returns an array for the related section
 */
function rijksvideo_block_get_data() {

	global $post;
	$return = array();

	$selecteer_een_video = get_field( 'selecteer_een_video' );

	if ( $selecteer_een_video ):

		$return['video']         = do_shortcode( '[rijksvideo id=' . $selecteer_een_video[0] . ']' );

	endif;

	return $return;

}

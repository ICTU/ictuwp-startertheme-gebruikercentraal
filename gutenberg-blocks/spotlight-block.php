<?php
// spotlight-block.php

add_action( 'acf/init', 'gb_add_spotlight_block' );

function gb_add_spotlight_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/spotlight',
			'title'           => _x( 'GC Spotlight', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Tonen van gerelateerde content in een apart block', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_spotlight_block',
			'category'        => 'gc-blocks',
			'icon'            => 'welcome-view-site', // todo: eigen icon voor dit block
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}

//========================================================================================================

function spotlight_disable_yoast_metabox() {
	remove_meta_box( 'wpseo_meta', GC_SPOTLIGHT_CPT, 'normal' );
}

add_action( 'add_meta_boxes', 'spotlight_disable_yoast_metabox', 100 );

//========================================================================================================

function gb_render_spotlight_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['spotlight'] = spotlight_block_get_data();

	Timber::render( 'sections/section-spotlight.html.twig', $context );

}


/*
 * returns an array for the related section
 */
function spotlight_block_get_data() {

	global $post;
	$return = array();

	$spotlightblok_toevoegen = get_field( 'spotlightblok_toevoegen' );
	$spotlight_blokken       = get_field( 'spotlight_blokken' );

	if ( $spotlight_blokken && 'ja' === $spotlightblok_toevoegen ):

		$return['block_id']      = get_the_id();

		foreach ( $spotlight_blokken as $post ):

			$item            = [];
			$item['title']   = get_the_title( $post );
			$item['descr']   = get_the_excerpt( $post );
			$image           = get_the_post_thumbnail_url( $post->ID, 'large' );
			$item['img']     = $image;
			$item['img_alt'] = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );

			$cta1 = get_field( 'spotlight_cta_1', $post->ID );
			$cta2 = get_field( 'spotlight_cta_2', $post->ID );

			if ( $cta1 ) {
				$cta1['class'] = 'primary';
				$item['cta'][] = $cta1;

				if ( $cta2 ) {
					// alleen tweede knop tonen als eerste knop aanwezig
					$cta2['class'] = 'primary';
					$item['cta'][] = $cta2;
				}

			}

			$return['blocks'][] = $item;

		endforeach;

		/* Restore original Post Data */
		wp_reset_postdata();

	endif;

	return $return;

}

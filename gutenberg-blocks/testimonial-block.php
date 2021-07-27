<?php

// testmonial-block.php

add_action( 'acf/init', 'gb_add_testmonial_block' );

function gb_add_testmonial_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/testmonial',
			'title'           => _x( "GC Citaat / testimonial", 'Block titel', 'gctheme' ),
			'description'     => _x( "Tonen van uitspraken van mensen", 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_testmonial_block',
			'category'        => 'gc-blocks',
			'icon'            => 'format-chat',
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}


function gb_render_testmonial_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['testmonial'] = testmonial_block_get_data();


	// Render the block.
	Timber::render( 'sections/section-quote.html.twig', $context );

}


/*
 * returns an array for the testimonials section
 */
function testmonial_block_get_data() {

	global $post;
	$return               = array();
	$type_block           = 'section--testimonials';
	$imagesize_for_thumbs = BLOG_SINGLE_DESKTOP;

	if ( 'ja' === get_field( 'testimonial_block_tonen' ) ) {

		$return['simpel_citaat'] = '1';
		$return['quote']         = get_field( 'testimonial_block_testimonial_text' );
		$return['quote_cite']    = wp_strip_all_tags( get_field( 'testimonial_block_testimonial_quotee' ) );

		if ( 'ja' === get_field( 'testimonial_block_gebruik_afbeelding' ) ) {

			$return['simpel_citaat'] = '';
			$return['quotee']        = wp_strip_all_tags( get_field( 'testimonial_block_testimonial_quote__instance' ) );
			$image                   = get_field( 'testimonial_block_testimonial_image' );
			$return['image_src']     = $image['sizes'][ $imagesize_for_thumbs ];
			if ( $image['alt'] ) {
				$return['image_alt'] = $image['alt'];
			}

		}

		$return['type_block'] = $type_block;

	}

	return $return;

}

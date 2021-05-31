<?php
// tipkaarten-block.php

add_action( 'acf/init', 'gb_add_testimonial_block' );

function gb_add_testimonial_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/testimonial',
			'title'           => _x( 'GC Testimonial ', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Collectie van 1 of meer testimonialas in een Gutenberg block', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_testimonial_block',
			'category'        => 'gc-blocks',
			'icon'            => 'format-quote', // todo: eigen icon voor dit block
			'keywords'        => [ 'quote', 'text', 'blockquote', 'citaat' ],
		] );
	}
}

//========================================================================================================

function gb_render_testimonial_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	// verzamelen van de inhoud
	$context['testimonials'] = testimonial_block_get_data();

	// Hergebruik van section-related.html.twig, want dat voldoet prima
	Timber::render( 'gutenberg-blocks/testimonial.html.twig', $context );

}


/*
 * returns an array for the related section
 */
function testimonial_block_get_data() {

	global $post;
	$imagesize_for_thumbs = IMAGESIZE_16x9;

	$return                  = array();
	$gc_gt_testimonials      = get_field( 'gc_gt_testimonials' );
	$return['classes']       = 'wp-block-quote';

	if ( $gc_gt_testimonials ) {

		$return = array();

		foreach ( $gc_gt_testimonials as $block ) {

			$item                       = array();
			$item['blockquote']         = $block['blockquote'];
			$item['cite_name']          = $block['cite_name'];
			$item['cite_name_function'] = $block['cite_name_function'];

			if ( $item ) {
				$return['items'][] = $item;
			}
		}
	}

	return $return;

}


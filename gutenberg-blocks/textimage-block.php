<?php

add_action( 'acf/init', 'gb_add_textimage_block' );

function gb_add_textimage_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/textimage',
			'title'           => _x( 'GC Text + image', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Tekst en plaatje naast elkaar', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_textimage_block',
			'category'        => 'gc-blocks',
			'icon'            => 'megaphone', // todo: eigen icon voor dit block
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}


function gb_render_textimage_block( $block, $content = '', $is_preview = FALSE ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['textimage'] = textimage_block_get_data();

	// Render the block.
	Timber::render( 'gutenberg-blocks/textimage-block.twig', $context );
}

/*
 * returns an array for the downloads section
 */
function textimage_block_get_data() {

	global $post;

	$return                = [];
	$gc_gt_textimage_text  = get_field( 'gc_gt_textimage_text' );
	$gc_gt_textimage_image = get_field( 'gc_gt_textimage_image' );
	$cssclasses           = [ 'section' ];

	if ( $gc_gt_textimage_text && $gc_gt_textimage_image ) {

		$cssclasses[] = 'section--text-image';

		$size = 'full'; // (thumbnail, medium, large, full or custom size)

		$return['text'] = $gc_gt_textimage_text;

		if ( $gc_gt_textimage_image ) {
//			$return['image'] = 'plaatje: ' . $gc_gt_textimage_image;
			$return['image'] = '<figure class="text-image__image">' . wp_get_attachment_image( $gc_gt_textimage_image, $size ) . '</figure>';
		}

		if ( 'none' != get_field( 'gc_gt_textimage_alignment' ) ) {
			$cssclasses[] = get_field( 'gc_gt_textimage_alignment' );
		}

		if ( 'none' != get_field( 'gc_gt_textimage_width' ) ) {
			$cssclasses[] = get_field( 'gc_gt_textimage_width' );
		}

		if ( 'none' != get_field( 'gc_gt_textimage_background' ) ) {
			$cssclasses[] = get_field( 'gc_gt_textimage_background' );
		}


	}


	//	section section--text-image l-has-background bg-color--light align--right


	if ( $cssclasses ) {
		$return['cssclass'] = implode( ' ', $cssclasses );
	}


	return $return;

}

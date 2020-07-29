<?php

add_action( 'acf/init', 'gb_add_calltoaction_block' );

/*
 * Initialize the Call To Action block
 */
function gb_add_calltoaction_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/ctalink',
			'title'           => _x( 'GC Call To Action (CTA)', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Opvallende link', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_calltoaction_block',
			'category'        => 'gc-blocks',
			'icon'            => 'megaphone',
			'keywords'        => [ 'link' ],
		] );
	}
}

/*
 * Render the CTA block
 */
function gb_render_calltoaction_block( $block, $content = '', $is_preview = FALSE ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['ctalink'] = cta_block_get_data();

	// Render the block.
	Timber::render( 'gutenberg-blocks/cta-block.twig', $context );
}

/*
 * Collect the fields and return them
 */
function cta_block_get_data() {

	global $post;

	$return           = [];
	$cssclasses       = 'btn--primary';
	$link             = get_field( 'gc_gb_ctalink' );
	$gc_gb_ctaclasses = get_field( 'gc_gb_ctaclasses' );

	if ( $gc_gb_ctaclasses ) {
		$cssclasses = $gc_gb_ctaclasses;
	}

	if ( $link ):
		$link_url              = $link['url'];
		$link_title            = $link['title'];
		$link_target           = $link['target'] ? $link['target'] : '_self';
		$return['link']        = '<a class="btn ' . $cssclasses . '" href="' . esc_url( $link_url ) . '" target="' . esc_attr( $link_target ) . '">' . esc_html( $link_title ) . '</a>';
		$return['linkpreview'] = '<a class="btn ' . $cssclasses . '" href="#" target="' . esc_attr( $link_target ) . '">' . esc_html( $link_title ) . '</a>';
	endif;

	return $return;

}

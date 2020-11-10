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
			'multiple'        => true,
		] );
	}
}

/*
 * Render the CTA block
 */
function gb_render_calltoaction_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['ctalink'] = cta_block_get_data();

	/*
	echo 'Preview CTA<pre>';
	var_dump( $context['ctalink'] );
	echo '</pre>';
	 *
	 */


	// Render the block.
	Timber::render( 'gutenberg-blocks/cta-block.twig', $context );

}

/*
 * Collect the fields and return them
 */
function cta_block_get_data() {

	global $post;

	$return           = [];
	$cssclasses_link1 = 'btn btn--primary';
	$link             = get_field( 'gc_gb_ctalink' );
	$gc_gb_ctaclasses = get_field( 'gc_gb_ctaclasses' );

	$link2             = get_field( 'gc_gb_ctalink2' );
	$gc_gb_ctaclasses2 = get_field( 'gc_gb_ctaclasses2' );

	if ( $gc_gb_ctaclasses ) {
		$cssclasses_link1 = $gc_gb_ctaclasses;
	}
	if ( $gc_gb_ctaclasses2 ) {
		$cssclasses_link2 = $gc_gb_ctaclasses2;
	}

	if ( $link ):
		$link_target = $link['target'] ? $link['target'] : '_self';
//		$return['link']        = '<a class="btn ' . $cssclasses_link1 . '" href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link_target ) . '">' . esc_html( $link['title'] ) . '</a>';
		$return['links'][] = array(
			'url'   => esc_url( $link['url'] ),
			'class' => 'btn ' . $cssclasses_link1,
			'title' => esc_html( $link['title'] ),
		);
		if ( $link2 ):
			$return['links'][] = array(
				'url'   => esc_url( $link2['url'] ),
				'class' => 'btn ' . $cssclasses_link2,
				'title' => esc_html( $link2['title'] ),
			);
		endif;
	else:
		$return['linkpreview'] = 'Maak een link aan via "Selecteer een link"';
	endif;


	return $return;

}

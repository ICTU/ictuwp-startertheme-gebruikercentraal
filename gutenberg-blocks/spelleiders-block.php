<?php
/*
 * spelleiders-block.php
 */

add_action( 'acf/init', 'gb_add_spelleiders_block' );

function gb_add_spelleiders_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/spelleiders',
			'title'           => _x( 'GC spelleiders block', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Blokken voor de spelleiders', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_spelleiders_block',
			'category'        => 'gc-blocks',
			'icon'            => 'id', // todo: eigen icon voor dit block
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}


function gb_render_spelleiders_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['spelleiders'] = spelleiders_block_get_data();

	// Render the block.
	Timber::render( 'gutenberg-blocks/spelleiders.twig', $context );
}

/*
 * returns an array for the downloads section
 */
function spelleiders_block_get_data() {

	global $post;

	$return                  = [];
	$gc_gt_spelleiders_title = get_field( 'gc_gt_spelleiders_organisatie' );
	$gc_gt_spelleiders_list                    = get_field( 'gc_gt_spelleiders_list' );
	if ( $gc_gt_spelleiders_list ) {

		$return['title']   = $gc_gt_spelleiders_title;
		$return['items']   = array();

		foreach ( $gc_gt_spelleiders_list as $spelleider ) {
			$spelleidernaam = $spelleider['gc_gt_spelleiders_spelleidernaam'];
			if ( $spelleidernaam ) {
				$return['items'][] = $spelleidernaam;
			}
		}
	}

	return $return;

}

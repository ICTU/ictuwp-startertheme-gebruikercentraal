<?php
/*
 * handleiding-block.php
 */

add_action( 'acf/init', 'gb_add_handleiding_block' );

function gb_add_handleiding_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/handleiding',
			'title'           => _x( 'GC Handleiding block', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Blokken voor de handleiding', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_handleiding_block',
			'category'        => 'gc-blocks',
			'icon'            => 'id', // todo: eigen icon voor dit block
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}


function gb_render_handleiding_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['handleiding'] = handleiding_block_get_data();

	// Render the block.
	Timber::render( 'gutenberg-blocks/handleiding.twig', $context );
}

/*
 * returns an array for the downloads section
 */
function handleiding_block_get_data() {

	global $post;

	$return                  = [];
	$gc_gt_handleiding_title = get_field( 'gc_gt_handleiding_title' );
	$gc_gt_handleiding_text  = get_field( 'gc_gt_handleiding_text' );
	$gc_gt_handleiding_time  = get_field( 'gc_gt_handleiding_time' );

	if ( $gc_gt_handleiding_text && $gc_gt_handleiding_title ) {

		$return['title']   = $gc_gt_handleiding_title;
		$return['text']    = $gc_gt_handleiding_text;
		$return['time']    = $gc_gt_handleiding_time;
		$return['counter'] = '(xxx)';

	}

	return $return;

}

<?php

add_action( 'acf/init', 'gb_add_linksblock' );

function gb_add_linksblock() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/links',
			'title'           => _x( 'GC Links', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Lijst met Links', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_links_block',
			'category'        => 'gc-blocks',
			'icon'            => 'download',
			'keywords'        => [ 'links', 'files', 'attachments' ],
		] );
	}
}


function gb_render_links_block( $block, $content = '', $is_preview = FALSE ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['links'] = links_block_get_data();

	// Render the block.
	Timber::render( 'gutenberg-blocks/links-block.twig', $context );

}

/*
 * returns an array for the downloads section
 */
function links_block_get_data() {

	global $post;
	$return = [];

	if ( 'ja' === get_field( 'links_tonen' ) ) {

		while ( have_rows( 'links_items' ) ) : the_row();

			$item              = [];
			$item['title']     = get_sub_field( 'link_item_title' );
			$item['descr']     = get_sub_field( 'link_item_description' );
			$item['url']       = get_sub_field( 'link_item_url' );
			$return['items'][] = $item;

		endwhile;

		if ( $return['items'] ) {
			$return['title'] = get_field( 'links_title' );
			$return['desc']  = get_field( 'links_description' );
		}

	}

	return $return;

}

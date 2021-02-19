<?php

// partnerlogos-block.php

add_action( 'acf/init', 'gb_add_partners_block' );

function gb_add_partners_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/logopartners',
			'title'           => _x( "GC Partner-logo's", 'Block titel', 'gctheme' ),
			'description'     => _x( "Tonen van logo's van parners in een apart block", 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_partners_block',
			'category'        => 'gc-blocks',
			'icon'            => 'tickets-alt',
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}


function gb_render_partners_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['partnerlogos'] = partners_block_get_data();

	// Render the block.
	Timber::render( 'sections/section-brands.html.twig', $context );

}


/*
 * returns an array for the partners section
 */
function partners_block_get_data() {

	global $post;
	$return               = array();
	$type_block           = 'section--partners';
	$imagesize_for_thumbs = IMAGESIZE_16x9;

	if ( 'ja' === get_field( 'partners_block_tonen' ) ) {

		$partners_block_logos = get_field( 'partners_block_logos' );

		if ( $partners_block_logos ) {

			$return = array();

			foreach ( $partners_block_logos as $block ) {

				$item                  = array();
				$image                 = $block['partners_block_logo'];
				$item['link_url']      = esc_url( $block['partners_block_url'] );
				$item['link_linktext'] = wp_strip_all_tags( $block['partners_block_linktext'] );
				$item['image_src']     = $image['sizes'][ $imagesize_for_thumbs ];
				$item['image_alt']     = $image['alt'];

				if ( $item ) {
					$return['items'][] = $item;
				}
			}
		}

		$columncounter = '4';

		if ( isset( $return['items'] ) ) {
			if ( count( $return['items'] ) === 1 ) {
				$columncounter = '1';
			} elseif ( count( $return['items'] ) === 2 ) {
				$columncounter = '2';
			} elseif ( count( $return['items'] ) === 3 ) {
				$columncounter = '3';
			}

			$return['description'] = get_field( 'partners_block_description' ) ? get_field( 'partners_block_description' ) : '';
			$return['descr']       = $return['description'];
			$return['title']       = get_field( 'partners_block_title' ) ? get_field( 'partners_block_title' ) : '';

			if ( get_field( 'content_block_items_extra_link' ) ) {
				$cta                    = get_field( 'content_block_items_extra_link' );
				$return['cta']['title'] = $cta['title'];
				$return['cta']['url']   = $cta['url'];
			}

		}

		$return['columncounter'] = $columncounter;
		$return['type_block']    = $type_block;


		if ( 'none' !== get_field( 'content_block_modifier' ) ) {
			// we moeten wel een achtergrondje tonen
			$return['modifier'] = get_field( 'content_block_modifier' );
		}

	}

	return $return;

}

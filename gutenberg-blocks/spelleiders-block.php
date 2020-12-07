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
	$imagesize_for_thumbs = IMAGESIZE_16x9;

	$return                  = array();
	$gc_gt_spelleiders_list  = get_field( 'gc_gt_spelleiders_blokken' );
	$gc_gt_spelleiders_titel = get_field( 'gc_gt_spelleiders_titel' );


	if ( $gc_gt_spelleiders_list ) {

		$return = array();
		if ( $gc_gt_spelleiders_titel ) {
			$return['blocktitel'] = $gc_gt_spelleiders_titel;
		}

		foreach ( $gc_gt_spelleiders_list as $block ) {

			$item     = array();
			$typeblok = $block['gc_gt_spelleiders_typeblok'];

			if ( 'gc_gt_spelleiders_typeblok_foto' === $typeblok ) {
				// een foto tonen
				$image             = $block['gc_gt_spelleiders_foto'];
				$item['image']     = $image['sizes'][ $imagesize_for_thumbs ];
				$item['image_alt'] = $image['alt'];

			} else {
				// we gaan een lijstje met namen opstellen
				$namen = $block['gc_gt_spelleiders_list'];

				if ( $namen ):
					// er zitten namen in de lijst
					$item['title'] = $block['gc_gt_spelleiders_organisatie'];

					foreach ( $namen as $naam ) {
						$item['namen'][] = $naam['gc_gt_spelleiders_spelleidernaam'];
					}

				endif;
			}


			if ( $item ) {
				$return['items'][] = $item;
			}
		}
	}

	return $return;

}

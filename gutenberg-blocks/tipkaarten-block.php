<?php
// tipkaarten-block.php

add_action( 'acf/init', 'gb_add_tipkaarten_block' );

function gb_add_tipkaarten_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/tipkaarten',
			'title'           => _x( 'GC Tipkaarten', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Collectie van van tipkaarten in een Gutenberg block', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_tipkaarten_block',
			'category'        => 'gc-blocks',
			'icon'            => 'flag', // todo: eigen icon voor dit block
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}

//========================================================================================================

function gb_render_tipkaarten_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	// verzamelen van de inhoud
	$context['related'] = tipkaarten_block_get_data();

	// Hergebruik van section-related.html.twig, want dat voldoet prima
	Timber::render( 'sections/section-related.html.twig', $context );

}


/*
 * returns an array for the related section
 */
function tipkaarten_block_get_data() {

	global $post;
	$type_block = 'section--overview';
	$args       = array(
		'post_type'      => GC_TIP_CPT,
		'post_status'    => 'publish',
		'posts_per_page' => - 1,

	);
	$return     = array();
	$tax_terms  = '';
	$taxonomy   = get_field( 'tipkaartenblock_type' );
	if ( taxonomy_exists( $taxonomy ) ) {
		// $taxonomy is een van deze waarden:
		// geen : Geen taxonomie
		// tipthema : Tipthema
		// speelset : Speelset
		// tipgever : Tipgever
		$tax_terms = get_field( 'tipkaartenblock_tax_' . $taxonomy );

		if ( $tax_terms ) {

			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $tax_terms
				)
			);
		}
	} else {
		// Gelijk nokken, want $taxonomy is geen bestaande taxonomie of is 'geen'
		return $return;
	}

	$featured_posts     = new WP_Query( $args );
	$return['block_id'] = get_the_id();

	if ( $featured_posts->have_posts() ) {

		$counter = 0;

		while ( $featured_posts->have_posts() ) {

			$featured_posts->the_post();
			$item              = prepare_card_content( $post );
			$return['items'][] = $item;
		}
	}


	/* Restore original Post Data */
	wp_reset_postdata();

	$columncounter = '3';

	if ( isset( $return['items'] ) ) {
		if ( count( $return['items'] ) < 2 ) {
			$columncounter = '1';
		} elseif ( count( $return['items'] ) === 4 ) {
			$columncounter = '3';
		} elseif ( count( $return['items'] ) > 2 ) {
			$columncounter = '3';
		}
	}

	$return['columncounter'] = $columncounter;
	$return['type_block']    = $type_block;

	return $return;

}

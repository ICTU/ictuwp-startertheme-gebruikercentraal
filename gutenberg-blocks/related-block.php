<?php


add_action( 'acf/init', 'gb_add_related_block' );

function gb_add_related_block() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/related',
			'title'           => _x( 'GC gerelateerde content', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Tonen van gerelateerde content in een apart block', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_related_block',
			'category'        => 'gc-blocks',
			'icon'            => 'tickets-alt',
			'keywords'        => [ 'link', 'text', 'image' ],
		] );
	}
}


function gb_render_related_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['related'] = related_block_get_data();

	// Render the block.
	Timber::render( 'sections/section-related.html.twig', $context );
}


/*
 * returns an array for the related section
 */
function related_block_get_data() {

	global $post;
	$return               = array();
	$type_block           = 'section--related';
	$imagesize_for_thumbs = IMAGESIZE_16x9;

	if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

		if ( 'posts' === get_field( 'content_block_types' ) ) {

			$featured_posts = get_field( 'content_block_items' );
			$type_block     = 'section--overview';

			if ( $featured_posts ):

				foreach ( $featured_posts as $post ):

					$item              = prepare_card_content( $post );
					$return['items'][] = $item;

				endforeach;

			endif;

		} elseif ( 'vrije_invoer' === get_field( 'content_block_types' ) ) {
			// Vrije invoer
			$freeform_items = get_field( 'content_block_freeform_items' );
			$type_block     = 'section--overview';

			if ( $freeform_items ):

				foreach ( $freeform_items as $freeform_item ):
					$item               = array();
					$link               = $freeform_item['content_block_freeform_item_link'];
					$image              = $freeform_item['content_block_freeform_item_image'];
					$item['post_title'] = wp_strip_all_tags( $link['title'] );
					$item['title']      = $item['post_title']; // dit is dubbelop en overbodig en meer dan nodig, maar in de twig-files wordt afwisselend 'title' en 'post_title' gebruikt. Dat laatste is de meest correcte vorm
					$item['descr']      = $freeform_item['content_block_freeform_item_description'];
					$item['post_type']  = 'page'; // lijkt me de meest neutrale
					$item['type']       = $item['post_type']; // dit is dubbelop en overbodig en meer dan nodig, maar in de twig-files wordt afwisselend 'type' en 'post_type' gebruikt. Dat laatste is de meest correcte vorm
					$item['url']        = esc_url( $link['url'] );
					if ( $image ) {
						$item['img'] = '<img src="' . esc_url( $image['sizes'][ $imagesize_for_thumbs ] ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';
					}

					$return['items'][] = $item;

				endforeach;

			endif;

		} elseif ( 'taxonomie_tipgevers' === get_field( 'content_block_types' ) ) {

			$tipgever_items = get_field( 'content_block_taxonomy_tipgever' );
			$type_block     = 'section--overview';

			if ( $tipgever_items ):

				foreach ( $tipgever_items as $tipgever_item ):
					$item               = array();
					$term               = get_term( $tipgever_item, OD_CITAATAUTEUR );
					$acfid              = OD_CITAATAUTEUR . '_' . $tipgever_item;
					$image              = get_field( 'tipgever_foto', $acfid );
					$item['post_title'] = wp_strip_all_tags( $term->name );
					$item['title']      = $item['post_title']; // dit is dubbelop en overbodig en meer dan nodig, maar in de twig-files wordt afwisselend 'title' en 'post_title' gebruikt. Dat laatste is de meest correcte vorm
					$item['post_type']  = OD_CITAATAUTEUR;
					$item['type']       = $item['post_type']; // dit is dubbelop en overbodig en meer dan nodig, maar in de twig-files wordt afwisselend 'type' en 'post_type' gebruikt. Dat laatste is de meest correcte vorm
					$item['url']        = esc_url( get_term_link( $term ) );
					if ( $image ) {
						$item['img'] = '<img src="' . esc_url( $image['sizes'][ $imagesize_for_thumbs ] ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';
					}

					$return['items'][] = $item;

				endforeach;

			endif;

		} elseif ( 'taxonomie_speelset' === get_field( 'content_block_types' ) ) {

			$terms = get_field( 'content_block_taxonomy_speelsets' );

			if ( $terms ):

				foreach ( $terms as $term ):

					$item          = [];
					$item['title'] = esc_html( $term->name );
					//					$item['type']  = 'speelset';
					$item['descr'] = esc_html( $term->description );
					$item['url']   = esc_url( get_term_link( $term ) );
					$image         = get_field( 'speelset_uitgelichte_afbeelding', $term );

					if ( $image ) {

						$size        = 'medium';
						$item['img'] = '<img src="' . esc_url( $image['sizes'][ $size ] ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';

					}
					$return['items'][] = $item;

				endforeach;
			endif;

		}
		wp_reset_postdata();

		$columncounter = '2';

		if ( isset( $return['items'] ) ) {
			if ( count( $return['items'] ) < 2 ) {
				$columncounter = '1';
			} elseif ( count( $return['items'] ) === 4 ) {
				$columncounter = '2';
			} elseif ( count( $return['items'] ) > 2 ) {
				$columncounter = '3';
			}

			$return['description'] = get_field( 'content_block_description' ) ? get_field( 'content_block_description' ) : '';
			$return['descr']       = $return['description'];
			$return['title']       = get_field( 'content_block_title' ) ? get_field( 'content_block_title' ) : '';

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

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
	$return = array();

	if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

		if ( 'posts' === get_field( 'content_block_types' ) ) {

			$featured_posts = get_field( 'content_block_items' );
			$themakleuren   = array();

			if ( $featured_posts ):

				foreach ( $featured_posts as $post ):

					$item['title'] = get_the_title( $post );

					$item          = array();
					$item['title'] = get_the_title( $post );
					$item['descr'] = get_the_excerpt( $post );
					$item['type']  = get_post_type( $post );
					$item['url']   = get_the_permalink( $post );
					$image         = get_the_post_thumbnail( $post->ID, 'large', [] );
					$item['img']   = $image;

					if ( 'tips' == get_post_type( $post ) ) {

						// zorgen dat de titel netjes afgebroken wordt binnen de smalle ruimte van het kaartje
						$item['title'] = od_wbvb_custom_post_title( get_the_title( $post ) );

						// het is een tip
						// eerst checken of we al alle themakleuren hebben
						if ( ! $themakleuren ) {
							$themakleuren = get_themakleuren();
						}

						$item['nr']     = sprintf( _x( 'Tip %s', 'Label tip-nummer', 'gctheme' ), get_post_meta( $post->ID, 'tip-nummer', true ) );
						$item['toptip'] = false;
						$is_toptip      = get_post_meta( $post->ID, 'is_toptip', true );

						if ( $is_toptip ) {
							$item['toptip']      = true;
							$item['toptiptekst'] = _x( 'Toptip', 'Toptiptekst bij tip', 'gctheme' );
						}

						$taxonomie = get_the_terms( $post->ID, GC_TIPTHEMA );

						if ( isset( $themakleuren[ $taxonomie[0]->term_id ] ) ) {
							$item['category'] = $themakleuren[ $taxonomie[0]->term_id ];
						}


					} elseif ( 'post' == get_post_type( $post ) ) {

						$item['meta'][] = [
							'title' => 'author',
							'descr' => get_the_author_meta( 'display_name', $post->post_author ),
						];

						$item['meta'][] = [
							'title' => 'date',
							'descr' => get_the_time( get_option( 'date_format' ), $post->ID ),
						];

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
			$return['title']       = get_field( 'content_block_title' ) ? get_field( 'content_block_title' ) : '';

		}

		$return['columncounter'] = $columncounter;

		if ( ! 'none' === get_field( 'content_block_modifier' ) ) {
			// we moeten wel een achtergrondje tonen
			$return['modifier'] = get_field( 'content_block_modifier' );
		}



	}

	return $return;

}

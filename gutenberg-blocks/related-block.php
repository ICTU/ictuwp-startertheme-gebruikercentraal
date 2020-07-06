<?php


/*
 * returns an array for the related section
 */
function related_block_get_data() {

	global $post;
	$return = [];

	if ( 'ja' === get_field( 'gerelateerde_content_toevoegen' ) ) {

		$featured_posts = get_field( 'content_block_items' );

		if ( $featured_posts ):

			foreach ( $featured_posts as $post ):

				$item['title']  = get_the_title( $post );

				$item           = [];
				$item['title']  = get_the_title( $post );
				$item['descr']  = get_the_excerpt( $post );
				$item['type']   = get_post_type( $post );
				$item['url']    = get_the_permalink( $post );
				$image          = get_the_post_thumbnail( $post->ID, 'large', [] );
				$item['img']    = $image;

				if ( 'post' ==  get_post_type( $post ) ) {

					$item['meta'][]  = [
						'title' => 'date',
						'descr' => get_the_time( get_option('date_format'), $post->ID),
					];

					$item['meta'][]  = [
						'title' => 'author',
						'descr' => get_the_author_meta( 'display_name', $post->post_author ),
					];
				}

				$return['items'][] = $item;

			endforeach;

		endif;

		if ( $return['items'] ) {
			$return['title'] = get_field( 'content_block_title' ) ? get_field( 'content_block_title' ) : _x( 'Gerelateerd', 'Titel boven gerelateerde links', 'gctheme' );
			$return['desc']  = 'DESC HIER: ' . get_field( 'downloads_description' );
		}

	}

	return $return;

}

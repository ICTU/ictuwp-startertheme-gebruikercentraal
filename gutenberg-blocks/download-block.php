<?php

add_action( 'acf/init', 'gb_add_downloadblock' );

function gb_add_downloadblock() {

	// Check function exists.
	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a testimonial block.
		acf_register_block_type( [
			'name'            => 'gc/downloads',
			'title'           => _x( 'GC Downloads', 'Block titel', 'gctheme' ),
			'description'     => _x( 'Lijst met downloads', 'Block description', 'gctheme' ),
			'render_callback' => 'gb_render_download_block',
			'category'        => 'gc-blocks',
			'icon'            => 'download',
			'keywords'        => [ 'download', 'files', 'attachments' ],
		] );
	}
}


function gb_render_download_block( $block, $content = '', $is_preview = false ) {

	$context = Timber::context();

	// Store block values.
	$context['block'] = $block;

	// Store field values.
	$context['fields'] = get_fields();

	// Store $is_preview value.
	$context['is_preview'] = $is_preview;

	$context['downloads'] = download_block_get_data();

	// Render the block.
	Timber::render( 'gutenberg-blocks/download-block.twig', $context );
}

/*
 * returns an array for the downloads section
 */
function download_block_get_data() {

	global $post;
	$return = [];

	if ( 'ja' === get_field( 'downloads_tonen' ) ) {

		while ( have_rows( 'download_items' ) ) : the_row();

			$item             = [];
			$aria_label_type  = '';
			$aria_label_size  = '';
			$aria_label       = sprintf( _x( 'Download %s', 'Lange linktekst voor een download', 'gctheme' ), get_sub_field( 'downloaditem_title' ) );
			$item['title']    = get_sub_field( 'downloaditem_title' );
			$item['descr']    = ( get_sub_field( 'downloaditem_description' ) ) ? get_sub_field( 'downloaditem_description' ) : '&nbsp;';
			$item['linktext'] = _x( 'Download', 'Korte linktekst voor een download', 'gctheme' );

			if ( 'extern' === get_sub_field( 'downloaditem_file' ) ) {

				$item['url'] = get_sub_field( 'downloaditem_link' );

				if ( get_sub_field( 'downloaditem_filetype' ) ) {
					$item['meta'][]  = [
						'title' => 'filetype',
						'descr' => strtoupper( get_sub_field( 'downloaditem_filetype' ) ),
					];
					$aria_label_type = get_sub_field( 'downloaditem_filetype' );
				}

				if ( get_sub_field( 'downloaditem_filesize' ) ) {
					$item['meta'][]  = [
						'title' => 'filesize',
						'descr' => get_sub_field( 'downloaditem_filesize' ),
					];
					$aria_label_size = get_sub_field( 'downloaditem_filesize' );
				}
			} else {
				if ( get_sub_field( 'downloaditem_media' ) ) {

					$file = get_sub_field( 'downloaditem_media' );

					if ( ! $item['title'] && $file['title'] ) {
						$item['title'] = $file['title'];
					}

					if ( $file['subtype'] ) {
						$item['meta'][]  = [
							'title' => 'filetype',
							'descr' => translate_mime_type( $file['subtype'] ),
						];
						$aria_label_type = translate_mime_type( $file['subtype'] );
					}

					if ( $file['filesize'] ) {
						$item['meta'][]  = [
							'title' => 'filesize',
							'descr' => gc_wbvb_get_human_filesize( $file['filesize'] ),
						];
						$aria_label_size = gc_wbvb_get_human_filesize( $file['filesize'] );
					}

					$item['url'] = $file['url'];

				}
			}

			if ( $aria_label_type && $aria_label_size ) {
				$item['aria_label'] = esc_attr( $aria_label . ' (' . $aria_label_type . ', ' . $aria_label_size . ')' );
			} elseif ( $aria_label_type || $aria_label_size ) {
				$item['aria_label'] = esc_attr( $aria_label . ' (' . $aria_label_type . $aria_label_size . ')' );
			}

			// een item toevoegen heeft alleen zin als de URL gevuld is.
			$return['items'][] = $item;

		endwhile;

		if ( $return['items'] ) {
			$return['title'] = get_field( 'downloads_title' ) ? get_field( 'downloads_title' ) : _x( 'Downloads', 'Titel boven downloads', 'gctheme' );
			$return['desc']  = get_field( 'downloads_description' );
		}

	}

	return $return;

}

<?php
/*
 * Settings for Gutenberg blocks
 *
*/

/*
 * create special category for Gebruiker Centraal Gutenberg blocks
 */

add_filter( 'block_categories_all', 'gb_define_categories', 10, 2 );

function gb_define_categories( $categories, $post ) {
	return array_merge( $categories, [
			[
				'slug'  => 'gc-blocks',
				'title' => _x( 'Gebruiker Centraal', 'Block-categorie', 'gctheme' ),
			],
		] );
}

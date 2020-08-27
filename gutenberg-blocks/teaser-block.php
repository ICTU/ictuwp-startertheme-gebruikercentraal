<?php
// teaser-block.php

//========================================================================================================

/*
 * returns an array for the related section
 */
function teaser_block_get_data() {

	global $post;
	$return = array();

	$return['block_id'] = get_the_id();

	if ( have_rows( 'home_template_teasers', get_the_id() ) ):

		// Loop through rows.
		while ( have_rows( 'home_template_teasers' ) ) : the_row();


			$item = [];

			$item['title']  = get_sub_field( 'home_template_teaser_title' );
			$item['descr']  = get_sub_field( 'home_template_teaser_text' );
			$link_primary   = get_sub_field( 'link_primary' );
			$link_secondary = get_sub_field( 'link_secondary' );
			if ( $link_primary ) {
				$item['link_primary']['url']  = $link_primary['url'];
				$item['link_primary']['text'] = $link_primary['title'];
			}
			if ( $link_secondary ) {
				$item['link_secondary']['url']  = $link_secondary['url'];
				$item['link_secondary']['text'] = $link_secondary['title'];
			}

			$return['blocks'][] = $item;

		endwhile;

	else:
		$return['inhoud'] = "In de instellingen voor dit block, kies: <br>'<em>Ja, voeg spotlightblok toe</em>' en selecteer een of twee blokken";

	endif;

	return $return;

}

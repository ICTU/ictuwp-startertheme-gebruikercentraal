<?php

if ( ! function_exists( 'is_pakb_main' )  ) {
	/**
	 * Check whether the main page for the knowledgebase is being displayed
	 *
	 * @return bool
	 */
	function is_pakb_main(){
		return false;
	}
}

if ( ! function_exists( 'is_pakb_category' )  ) {
	/**
	 * Check whether a page is a category page and has a taxonomy of knowledgebase_category
	 *
	 * @return bool
	 */
	function is_pakb_category(){

		return is_tax( 'knowledgebase_category' );
	}
}

if ( ! function_exists( 'is_pakb_single' )  ) {
	/**
	 * Check whether a page is a single page and has a post type of knowledgebase
	 *
	 * @return bool
	 */
	function is_pakb_single(){

		return is_singular( 'knowledgebase' );

	}
}

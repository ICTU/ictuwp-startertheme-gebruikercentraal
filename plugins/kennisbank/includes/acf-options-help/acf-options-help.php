<?php

if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( !class_exists('acf_field_options_help') ) :
class acf_field_options_help extends acf_field {

	function initialize() {
		
		// vars
		$this->name = 'info';
		$this->label = __("Options Help Page",'acf');
		$this->category = 'layout';
		
	}
	
	/*
	*  render_field()
	*  Create the HTML interface for your field
	*/
	function render_field( $field ) {
		
		include( PAKB_PLUGIN_DIR . 'admin/acf-options-help.php' );

	}
}
// initialize
new acf_field_options_help();
// class_exists check
endif;

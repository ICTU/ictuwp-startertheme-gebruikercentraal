<?php

if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( !class_exists('acf_field_page_template') ) :
class acf_field_page_template extends acf_field {
	/*
	*  __construct
	*  This function will setup the field type data
	*/
	function __construct() {
		$this->name = 'page_template';
		$this->label = __('Page Templates', 'acf-page_template');
		$this->category = 'relational';
		$this->defaults = array(
			'post_type'		=> 'page',
			'template'		=> array(),
			'allow_null' 	=> 0,
			'return_format'	=> 'object',
			'multiple'      => '',
			'ajax'          => '',
		);
    	parent::__construct();
	}

	/*
	*  render_field()
	*  Create the HTML interface for your field
	*/
	function render_field( $field ) {
		// Change Field into a select
		$field['type'] = 'select';
		$field['choices'] = array_merge( array( '' => 'Default Template' ), wp_get_theme()->get_page_templates() ); //array('' => 'Select a template');
		$field['ui'] = 1;

		acf_render_field( $field );
	}
}
// initialize
new acf_field_page_template();
// class_exists check
endif;

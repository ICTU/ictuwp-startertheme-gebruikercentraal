<?php

/**
 * This is the template that renders the testimonial block.
 *
 * @param   array $block The block settings and attributes.
 * @param   bool $is_preview True during AJAX preview.
 */

global $pakb_loop, $pakb_helper;

$include = get_field('pakb_block_kb_categories') ? get_field('pakb_block_kb_categories') : [];

$pakb_loop->process_kbpage( $include );

echo $pakb_helper->load_file( $pakb_helper->get_template_files( 'knowledgebase' ) );

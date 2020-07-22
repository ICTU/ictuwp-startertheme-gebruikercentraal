<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://pressapps.co
 * @since      1.0.0
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/public/partials
 */

global $pakb_helper;

//$options = get_option( 'now_hiring_options' );
	//include( plugin_dir_path( __FILE__ ) . 'now-hiring-public-display-single-' . esc_attr( $options['layout'] ) . '.php' );
?>

<ul class="uk-list uk-list-medium pakb-list pakb-secondary-color pakb-link">
	<?php foreach ( $items->posts as $item ) { ?>
		<li><a href="<?php echo get_permalink( $item->ID ); ?>"><?php echo esc_attr( $item->post_title ); ?></a></li>
	<?php } ?>
</ul>

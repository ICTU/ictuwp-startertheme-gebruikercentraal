<?php

/**
 * Plugin Name:       PressApps Knowledge Base
 * Description:       Add knowledge base to your existing site in minutes that will decrease customer queries
 * Version:           4.2.1
 * Author:            PressApps
 * Author URI:        https://codecanyon.net/user/unboundstudio
 * Text Domain:       pressapps-knowledge-base
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'PAKB_PLUGIN_DIR' ) or define( 'PAKB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'PAKB_ICON_DIR' ) or define( 'PAKB_ICON_DIR', plugin_dir_path( __FILE__ ) . 'includes/acf-icon-picker/assets/img/acf/' );

/**
 * Detect ACF Pro plugin. Include if not present.
 * if ACF Pro plugin does not currently exist
 */
if ( !class_exists('acf') ) {
	/**
	 * Customize ACF path
	 */
	add_filter('acf/settings/path', 'pakb_acf_settings_path');
	function pakb_acf_settings_path( $path ) {
	  $path = plugin_dir_path( __FILE__ ) . 'includes/acf/';
	  return $path;
	}

	/**
	 * Customize ACF dir
	 */
  add_filter('acf/settings/dir', 'pakb_acf_settings_dir');
  function pakb_acf_settings_dir( $path ) {
    $dir = plugin_dir_url( __FILE__ ) . 'includes/acf/';
    return $dir;
  }

	/**
	 * Hide ACF field group menu item
	 */
  // add_filter('acf/settings/show_admin', '__return_false');

  /**
   * Include ACF
   */
  include_once( plugin_dir_path( __FILE__ ) . 'includes/acf/acf.php' );

	/**
	 * Create JSON save point
	 */
  add_filter('acf/settings/save_json', 'pakb_acf_json_save_point');

  function pakb_acf_json_save_point( $path ) {
    $path = plugin_dir_path( __FILE__ ) . 'includes/acf-json/';
    return $path;
  }

  /**
   * Create JSON load point
   */
  add_filter('acf/settings/load_json', 'pakb_acf_json_load_point');

  /**
   * Stop ACF upgrade notifications
   */
  add_filter( 'site_transient_update_plugins', 'pakb_stop_acf_update_notifications', 11 );
  function pakb_stop_acf_update_notifications( $value ) {
		if($value) {
	    unset( $value->response[ plugin_dir_path( __FILE__ ) . 'includes/acf/acf.php' ] );
		}
    return $value;
  }

} else { // else ACF Pro plugin does exist

  /**
   * Create JSON load point
   */
  add_filter('acf/settings/load_json', 'pakb_acf_json_load_point');
}

/**
 * Add ACF options page
 */
if( function_exists('acf_add_options_page') ) {

	$parent = acf_add_options_page(array(
		'page_title' 	=> 'Knowledge Base Settings',
		'menu_title'	=> 'Knowledge Base',
		'menu_slug' 	=> 'pakb-options',
		'capability'	=> 'edit_posts',
		'icon_url' 		=> 'dashicons-admin-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'General Settings',
		'menu_title' 	=> 'General',
		'parent_slug' 	=> $parent['menu_slug'],
		'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
		'menu_slug' 	=> 'pakb-options-general',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Single Settings',
		'menu_title' 	=> 'Single',
		'parent_slug' 	=> $parent['menu_slug'],
		'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
		'menu_slug' 	=> 'pakb-options-single',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Search Settings',
		'menu_title' 	=> 'Search',
		'parent_slug' 	=> $parent['menu_slug'],
		'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
		'menu_slug' 	=> 'pakb-options-search',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Voting Settings',
		'menu_title' 	=> 'Voting',
		'parent_slug' 	=> $parent['menu_slug'],
		'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
		'menu_slug' 	=> 'pakb-options-voting',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Style Settings',
		'menu_title' 	=> 'Style',
		'parent_slug' 	=> $parent['menu_slug'],
		'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
		'menu_slug' 	=> 'pakb-options-style',
	));

	if ( class_exists( 'Pressapps_Knowledge_Base_Sidebar' ) ) {
		acf_add_options_sub_page(array(
			'page_title' 	=> 'Sidebar Addon Settings',
			'menu_title' 	=> 'Sidebar',
			'parent_slug' 	=> $parent['menu_slug'],
			'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
			'menu_slug' 	=> 'pakb-options-sidebar',
		));
	}

	$theme = wp_get_theme(); // gets the current theme

	if ( 'Knowledge Base' == $theme->name || 'Knowledge Base' == $theme->parent_theme ) {

		acf_add_options_sub_page(array(
			'page_title' 	=> 'Theme Settings',
			'menu_title' 	=> 'Theme',
			'parent_slug' 	=> $parent['menu_slug'],
			'update_button'	=> __('Save Changes', 'pressapps-knowledge-base'),
			'menu_slug' 	=> 'pakb-options-theme',
		));			
	}

	acf_add_options_sub_page(array(
		'page_title' 	=> 'We Are Here to Help',
		'menu_title' 	=> 'Help',
		'parent_slug' 	=> $parent['menu_slug'],
		'menu_slug' 	=> 'pakb-options-help',
	));
	
}

/**
 * Function to create JSON load point
 */
function pakb_acf_json_load_point( $paths ) {
  $paths[] = plugin_dir_path( __FILE__ ) . 'includes/acf-json/';
  return $paths;
}

/**
 * Include ACF field icon picker
 */
require plugin_dir_path( __FILE__ ) . 'includes/acf-icon-picker/acf-icon-picker.php';

/**
 * Include ACF field page templates
 */
require plugin_dir_path( __FILE__ ) . 'includes/acf-page-templates/acf-page-templates.php';

/**
 * Include ACF field for options help content
 */
require plugin_dir_path( __FILE__ ) . 'includes/acf-options-help/acf-options-help.php';

/**
 * Include ACF field post types
 */
require plugin_dir_path( __FILE__ ) . 'includes/acf-post-type-selector/acf-post-type-selector.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pressapps-knowledge-base-activator.php
 */
function activate_pressapps_knowledge_base() {

	// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
	if ( version_compare( PHP_VERSION, '5.3.0', '<' )  ) {
		deactivate_plugins( plugin_basename( dirname( __FILE__ )  ) );
		wp_die( __( 'The minimum PHP version required for this plugin is 5.3.0 Please upgrade the PHP version or contact your hosting provider to do it for you.', 'pressapps-knowledge-base' ) );
	}
	if ( class_exists('acf') ) {
		if ( !defined( 'ACF_PRO' ) ) {
			deactivate_plugins( plugin_basename( dirname( __FILE__ )  ) );
			wp_die( __( 'Advanced Custom Fields PRO is required for this plugin, is bundled with this plugin. Please deactivate your Advanced Custom Fields plugin.', 'pressapps-knowledge-base' ) );
			//add_action( 'admin_notices', 'display_activation_error' );
		}
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pressapps-knowledge-base-activator.php';
	Pressapps_Knowledge_Base_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pressapps-knowledge-base-deactivator.php
 */
function deactivate_pressapps_knowledge_base() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pressapps-knowledge-base-deactivator.php';
	Pressapps_Knowledge_Base_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pressapps_knowledge_base' );
register_deactivation_hook( __FILE__, 'deactivate_pressapps_knowledge_base' );

/**
 * Redirect user to help page after activation
 */
function redirect_to_options_pressapps_knowledge_base( $plugin ) {
  if( $plugin == plugin_basename( __FILE__ ) ) {
		exit( wp_redirect( admin_url( 'admin.php?page=pakb-options-help' ) ) );
  }
}

add_action( 'activated_plugin', 'redirect_to_options_pressapps_knowledge_base' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pressapps-knowledge-base.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pressapps_knowledge_base() {

	$plugin = new Pressapps_Knowledge_Base();
	$plugin->run();

}
run_pressapps_knowledge_base();

if ( class_exists( 'PAKB_Loop' ) && ! isset( $pakb_loop ) ) {
	$pakb_loop   = new PAKB_Loop();
}

if ( class_exists( 'PAKB_Helper' ) && ! isset( $pakb_helper ) ) {
	$pakb_helper = new PAKB_Helper();
}

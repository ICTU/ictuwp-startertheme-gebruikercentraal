<?php

/**
 * Fired during plugin activation
 *
 * @link       http://pressapps.co
 * @since      1.0.0
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/includes
 * @author     PressApps
 */
class Pressapps_Knowledge_Base_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option('PAKB_FLUSH_REWRITE_RULE',TRUE);
		flush_rewrite_rules();		
	}

}

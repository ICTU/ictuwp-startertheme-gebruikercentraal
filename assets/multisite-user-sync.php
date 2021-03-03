<?php
/*
Multisite user sync script
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'restricted access' );
}

/*
 * This is a function that call admin side css and js.
 */
if ( ! function_exists( 'wmus_admin_include_css_and_js' ) ) {
	add_action( 'admin_enqueue_scripts', 'wmus_admin_include_css_and_js' );
	function wmus_admin_include_css_and_js() {

		$base_url =  network_home_url() . 'wp-content/themes/ictuwp-theme-gc2020/assets/';

		/* admin style */
		wp_register_style( 'wmus-style', $base_url . 'css/wmus-style.css', FALSE, '1.0.0' );
		wp_enqueue_style( 'wmus-style' );

		/* admin script */
		wp_register_script( 'wmus-script', $base_url . 'js/wmus-script.js', [ 'jquery' ] );
		wp_enqueue_script( 'wmus-script' );
	}
}

/*
 * This is a function that add custom cron schedule.
 */
if ( ! function_exists( 'wmus_cron_schedules' ) ) {
	add_filter( 'cron_schedules', 'wmus_cron_schedules' );
	function wmus_cron_schedules( $schedules ) {
		$schedules['wmus_one_minute'] = [
			'interval' => 60,
			'display'  => esc_html__( 'Every one minute' ),
		];

		return $schedules;
	}
}


/*
 * This is one time function, it's set one minute custom cron.
 */

function wmus_run_only_once() {

	if ( get_option( 'wmus_run_only_once_01' ) != 'completed' ) {

		if ( ! wp_next_scheduled( 'wmus_one_minute_event' ) ) {
			wp_schedule_event( time(), 'wmus_one_minute', 'wmus_one_minute_event' );
		}

		$sync_type = get_site_option( 'wmus_auto_sync' );
		if ( ! $sync_type ) {
			update_site_option( 'wmus_auto_sync', 'auto' );
		}

		$auto_sync_type = get_site_option( 'wmus_auto_sync_type' );
		if ( ! $auto_sync_type ) {
			update_site_option( 'wmus_auto_sync_type', 'all-sites' );
		}

		update_option( 'wmus_run_only_once_01', 'completed' );
	}
}

add_action( 'admin_init', 'wmus_run_only_once' );


/*
 * This is a function file for network settings.
 * Add network admin menu
 * Add network pages
 */
require plugin_dir_path( __FILE__ ) . '../includes/wmus-network.php';

/*
 * This is a file for sync/unsync functions.
 */
//require plugin_dir_path( __FILE__ ) . '../includes/wmus-sync-unsync.php';

//========================================================================================================

// bij het wijzigen van de avatar van een slaan we de URL voor de foto op als een
// globaal beschikbare waarde voor de gebruiker.
// zo is de avatar die je invoerde op site [x] ook beschikbaar op site [y]
// de user variable 'auteursfoto_url' kan ook door andere themes (zoals gebruiker-centraal)
// worden gebruikt.

add_action( 'acf/save_post', 'gc_wbvb_update_auteursfoto' );

function gc_wbvb_update_auteursfoto( $post_id ) {

	$user_id     = str_replace( "user_", "", $post_id );
	$auteursfoto = get_user_meta( $user_id, 'auteursfoto', true );
	$size        = 'thumb-cardv3';

	if ( $auteursfoto ) {
		$image = wp_get_attachment_image_src( $auteursfoto, $size );
		if ( $image[0] ) {
			update_user_meta( $user_id, 'auteursfoto_url', $image[0] );
		}
	}

}

//========================================================================================================

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

        /* admin style */
        wp_register_style( 'wmus-style', plugin_dir_url( __FILE__ ) . '../assets/css/wmus-style.css', false, '1.0.0' );
        wp_enqueue_style( 'wmus-style' );

        /* admin script */
        wp_register_script( 'wmus-script', plugin_dir_url( __FILE__ ) . '../assets/js/wmus-script.js', array( 'jquery' ) );
        wp_enqueue_script( 'wmus-script' );
    }
}

/*
 * This is a function that add custom cron schedule.
 */
if ( ! function_exists( 'wmus_cron_schedules' ) ) {
    add_filter( 'cron_schedules', 'wmus_cron_schedules' );
    function wmus_cron_schedules( $schedules ) {
        $schedules['wmus_one_minute'] = array(
            'interval' => 60,
            'display'  => esc_html__( 'Every one minute' ),
        );

        return $schedules;
    }
}


/*
 * This is one time function, it's set one minute custom cron.
 */

function wmus_run_only_once() {

	if ( get_option( 'wmus_run_only_once_01' ) != 'completed' ) {

		if (! wp_next_scheduled ( 'wmus_one_minute_event' )) {
			wp_schedule_event(time(), 'wmus_one_minute', 'wmus_one_minute_event' );
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
require  plugin_dir_path( __FILE__ ) . '../includes/wmus-network.php';

/*
 * This is a file for sync/unsync functions.
 */
require  plugin_dir_path( __FILE__ ) . '../includes/wmus-sync-unsync.php';

add_action('acf/save_post', 'my_acf_save_post');
function my_acf_save_post( $post_id ) {

	// Get newly saved values.
	$values = get_fields( $post_id );


	$user_id = str_replace("user_", "", $post_id);

	// Check the new value of a specific field.
	$hero_image = get_user_meta( $user_id, 'auteursfoto' , true );

	update_user_meta($user_id,'auteursfoto_url', wp_get_attachment_url( $hero_image ));

}

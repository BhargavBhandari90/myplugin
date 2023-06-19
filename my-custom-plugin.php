<?php
/**
Plugin Name: My Custom Plugin
Plugin URI: https://wordpress.com/
Description: Plugin Description goes here.
Author: BuntyWP
Version: 1.0.0
Text Domain: buntywp
Author URI: http://bhargavb.wordpress.com/
*/

define( 'MY_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MY_PLUGIN_VER', '1.0.0' );

include plugin_dir_path( __FILE__ ) . 'includes/custom-post-type.php';
include plugin_dir_path( __FILE__ ) . 'includes/class-custom-widget.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-ajax.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-actions-filters.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-fancybox-gallery.php';
include plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
include plugin_dir_path( __FILE__ ) . 'includes/admin.php';
// include plugin_dir_path( __FILE__ ) . 'includes/acf-timezone.php';
include plugin_dir_path( __FILE__ ) . 'includes/common-functions.php';
include plugin_dir_path( __FILE__ ) . 'includes/permalink.php';
include plugin_dir_path( __FILE__ ) . 'includes/acf-form.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-api.php';
// include plugin_dir_path( __FILE__ ) . 'includes/class-custom-api.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-cli-commands.php';
include plugin_dir_path( __FILE__ ) . 'includes/wpdb-methods.php';
include plugin_dir_path( __FILE__ ) . 'includes/image-uploader.php';
include plugin_dir_path( __FILE__ ) . 'includes/image-cropper.php';
include plugin_dir_path( __FILE__ ) . 'includes/blocks.php';

/**
 * Add new time interval for cron job.
 *
 * Here we are adding 1 minite cron job.
 *
 * @param array $schedules
 * @return void
 */
function custom_every_minute_schedule( $schedules ) {

	// add a 'everyminute' schedule to the existing set
	$schedules['everyminute'] = array(
		'interval' => 60,
		'display'  => __( 'Custom Every Minute', 'bwp-core' ),
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'custom_every_minute_schedule' );

/**
 * Scedule cron job.
 *
 * @return void
 */
function custom_core_activate() {
	if ( ! wp_next_scheduled( 'custom_every_minute_event' ) ) {
		wp_schedule_event( time(), 'everyminute', 'custom_every_minute_event' );
	}
}

register_activation_hook( __FILE__, 'custom_core_activate' );

add_action( 'custom_every_minute_event', 'custom_every_minute_cronjob' );

/**
 * Do whatever you want to do in the cron job.
 */
function custom_every_minute_cronjob() {
	error_log( date( 'Y-m-d H:i:s', time() ) );
	add_option( 'custom_crone_run_at', date( 'Y-m-d H:i:s', time() ) );
}

/**
 * Clear cron scedular.
 *
 * @return void
 */
function custom_deactivation() {
	wp_clear_scheduled_hook( 'custom_every_minute_event' );
}

register_deactivation_hook( __FILE__, 'custom_deactivation' );



/**
 * Apply transaltion file as per WP language.
 */
function bwp_text_domain_loader() {

	// Get mo file as per current locale.
	$mofile = trailingslashit( MY_PLUGIN_PATH ) . 'languages/buntywp-' . get_locale() . '.mo';

	// If file does not exists, then applu default mo.
	if ( ! file_exists( $mofile ) ) {
		$mofile = trailingslashit( MY_PLUGIN_PATH ) . 'languages/default.mo';
	}

	load_textdomain( 'buntywp', $mofile );
}

add_action( 'plugins_loaded', 'bwp_text_domain_loader' );

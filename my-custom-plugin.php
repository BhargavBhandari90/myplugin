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
// include plugin_dir_path( __FILE__ ) . 'includes/custom-api.php';
include plugin_dir_path( __FILE__ ) . 'includes/class-custom-api.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-cli-commands.php';
include plugin_dir_path( __FILE__ ) . 'includes/wpdb-methods.php';
include plugin_dir_path( __FILE__ ) . 'includes/image-uploader.php';

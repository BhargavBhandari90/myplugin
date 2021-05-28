<?php
/**
Plugin Name: My Custom Plugin
Plugin URI: https://wordpress.com/
Description: Plugin Description goes here.
Author: BuntyWP
Version: 1.0.0
Author URI: http://bhargavb.wordpress.com/
*/

include plugin_dir_path( __FILE__ ) . 'includes/custom-post-type.php';
include plugin_dir_path( __FILE__ ) . 'includes/class-custom-widget.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-ajax.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-actions-filters.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-fancybox-gallery.php';
include plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';

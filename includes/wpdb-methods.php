<?php

function bwp_wbdb_methods() {

	$can_insert = $_GET['insert'];

	if ( empty( $can_insert ) ) {
		return;
	}

	global $wpdb;

	$table = $wpdb->prefix . 'bwp';
	$data  = array(
		'first_name' => 'Bunty',
		'last_name'  => 'WP',
	);

	$wpdb->insert(
		$table,
		$data
	);

	echo 'Data inserted...';

}

add_action( 'wp_head', 'bwp_wbdb_methods' );

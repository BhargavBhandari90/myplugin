<?php

function bwp_wbdb_methods() {

	global $wpdb;

	$table = $wpdb->prefix . 'bwp';

	$can_insert = $_GET['insert'];

	if ( ! empty( $can_insert ) ) {

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

	$can_update = $_GET['update'];

	if ( ! empty( $can_update ) ) {

		$wpdb->update(
			$table,
			array(
				'first_name' => 'ABC',
				'last_name'  => 'XYZ',
			),
			array(
				'age'       => '20',
				'last_name' => 'WP',
			),
		);

		echo 'Data Updated...';

	}

	$can_delete = $_GET['delete'];

	if ( ! empty( $can_delete ) ) {

		$count = $wpdb->delete(
			$table,
			array(
				'first_name' => 'ABC',
				'age'        => '33',
			)
		);

		echo $count . ' rows delete';

	}

}

add_action( 'wp_head', 'bwp_wbdb_methods' );

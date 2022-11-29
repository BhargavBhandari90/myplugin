<?php

function bwp_wbdb_methods() {

	global $wpdb;

	$table = $wpdb->prefix . 'bwp';

	$can_insert = $_GET['insert'];

	if ( ! empty( $can_insert ) ) {

		$data  = array(
			'first_name' => 'Bunty1',
			'last_name'  => 'WP1',
			'age'        => 41,
		);

		$inserted = $wpdb->insert(
			$table,
			$data,
			array(
				'%s',
				'%d',
				'%s',
			)
		);

		var_dump( $inserted );

		// echo 'Data inserted...';

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
			array(
				'%s',
				'%s',
			),
			array(
				'%d',
				'%s',
			)
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
			),
			array(
				'%s',
				'%d'
			)
		);

		echo $count . ' rows delete';

	}

	$can_select = $_GET['select'];

	if ( ! empty( $can_select ) ) {

		$sql = $wpdb->prepare(
			"SELECT `first_name` FROM {$table} WHERE `age` = %d AND `last_name` = %s",
			55,
			'Kalam'
		);

		$result = $wpdb->get_var( $sql );

		echo 'Result is : ' . $result;

	}

	$get_row = $_GET['get_row'];

	if ( ! empty( $get_row ) ) {

		$result = $wpdb->get_row( "SELECT * FROM {$table} WHERE `first_name` = 'ABC' ", 'OBJECT', 1 );

		echo '<pre>';print_r($result);echo '</pre>';

	}

	$get_column = $_GET['get_column'];

	if ( ! empty( $get_column ) ) {

		$result = $wpdb->get_col( "SELECT `last_name` FROM {$table}" );

		echo '<pre>';print_r($result);echo '</pre>';

	}

	$get_results = $_GET['get_results'];

	if ( ! empty( $get_results ) ) {

		$result = $wpdb->get_results( "SELECT `first_name`, `last_name` FROM {$table} LIMIT 2" );

		echo '<pre>';print_r($result);echo '</pre>';

	}

	$get_query = $_GET['get_query'];

	if ( ! empty( $get_query ) ) {

		$result = $wpdb->query( "DELETE FROM {$table} WHERE `first_name` = 'ABC'" );

		echo '<pre>';print_r($result);echo '</pre>';

	}

}

add_action( 'wp_head', 'bwp_wbdb_methods' );

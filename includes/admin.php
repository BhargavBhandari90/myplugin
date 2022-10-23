<?php


function wporg_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'current-date',
		'What is Today?',
		'wp_show_current_date'
	);
}

add_action( 'wp_dashboard_setup', 'wporg_add_dashboard_widgets' );

function wp_show_current_date() {
	echo date_i18n( 'Y-m-d H:i:s' );
}

// add_action( 'admin_init', 'admin_explore' );

function admin_explore() {

	$url   = wp_nonce_url( 'themes.php?page=example', 'example-theme-options' );
	$creds = request_filesystem_credentials( $url, '', false, false, null );
	error_log( '***********' );
	error_log( print_r( $creds, true ) );
	if ( false === ( $creds ) ) {
		return; // stop processing here
	}

	global $wp_filesystem;
	$wp_filesystem->put_contents(
		'/tmp/example.txt',
		'Example contents of a file',
		FS_CHMOD_FILE // predefined mode settings for WP files
	);

}

/**
 * Add Setting page for ACF
 *
 * @return void
 */
function buntywp_acf_setting_pages() {

	// Bail, if anything goes wrong.
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Bunty ACF Settings', 'default' ),
			'menu_title'  => __( 'Bunty ACF Settings', 'default' ),
			'parent_slug' => 'options-general.php',
		)
	);

}

// Add admin menu for custom ACF setting.
add_action( 'admin_menu', 'buntywp_acf_setting_pages', 90 );

function favourite_books_options( $fields ) {

	$books_query = new WP_Query(
		array(
			'post_type'      => 'book',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
		)
	);

	$books = isset( $books_query->posts ) ? $books_query->posts : '';

	if ( empty( $books ) ) {
		return $fields;
	}

	foreach ( $books as $book ) {

		$fields['choices'][ $book->ID ] = $book->post_title;
	}

	return $fields;

}

add_filter( 'acf/load_field/name=favourite_books', 'favourite_books_options' );


/**
 * Add user profile extra fields.
 *
 * @param object $user
 * @return void
 */
function buntywp_extra_user_profile_fields( $user ) {

	$extra_field = get_user_meta( $user->ID, 'extra_field', true );

	?>
	<h3><?php _e( 'Extra profile fields', 'default' ); ?></h3>

	<table class="form-table">
	  <tr>
		<th><label for="extra_field"><?php _e( 'Extra Field' ); ?></label></th>
		<td>
		  <input type="text" name="extra_field" id="extra_field" value="<?php echo esc_attr( $extra_field ); ?>" class="regular-text" /><br />
		  <p class="description"><?php _e( 'Description goes gere.', 'default' ); ?></p>
		</td>
	  </tr>
	</table>
	<?php
}

add_action( 'edit_user_profile', 'buntywp_extra_user_profile_fields', 99 );

/**
 * Save user profile extra fields.
 *
 * @param int $user_id
 * @return void
 */
function buntywp_save_extra_user_profile_fields( $user_id ) {

	if ( empty( $user_id ) ) {
		return;
	}

	// Verify the nonce.
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
		return;
	}

	// Only allow admin.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Set company creator.
	update_user_meta( $user_id, 'extra_field', $_POST['extra_field'] );
}

add_action( 'edit_user_profile_update', 'buntywp_save_extra_user_profile_fields' );


// register custom meta tag field
function myguten_register_post_meta() {
    register_post_meta( 'post', 'myguten_meta_block_field', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ) );
}
// add_action( 'init', 'myguten_register_post_meta' );
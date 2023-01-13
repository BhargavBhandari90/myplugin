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
	register_post_meta(
		'post',
		'myguten_meta_block_field',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);
}
// add_action( 'init', 'myguten_register_post_meta' );


/** Custom admin menu page form - start */

/**
 * Register a custom menu page.
 */
function bwp_my_custom_menu_page() {

	add_menu_page(
		__( 'BWP Settings', 'buntywp' ),
		__( 'BWP Settings', 'buntywp' ),
		'manage_options',
		'bwp_settings',
		'bwp_my_custom_menu_page_callback',
		'',
		6
	);
}
add_action( 'admin_menu', 'bwp_my_custom_menu_page' );

function bwp_my_custom_menu_page_callback() {
	?>

	<div class="wrap">
		<h1><?php echo __( 'BWP Settings', 'buntywp' ); ?></h1>
		<form method="post" action="options.php" novalidate="novalidate">
			<?php settings_fields( 'bwp_settings' ); ?>
			<table class="form-table" role="presentation">
			<?php do_settings_fields( 'bwp_settings', 'default' ); ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>

	<?php
}


function bwp_register_my_setting() {

	$args = array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => null,
	);

	register_setting( 'bwp_settings', 'bwp_field_1', $args );

	add_settings_field(
		'bwp_field_1',
		esc_html__( 'Field 1', 'buntywp' ),
		'bwp_setting_field_callback',
		'bwp_settings'
	);

	register_setting( 'bwp_settings', 'bwp_select', $args );

	add_settings_field(
		'bwp_select',
		esc_html__( 'Books', 'buntywp' ),
		'bwp_setting_book_field_callback',
		'bwp_settings'
	);
}

add_action( 'admin_init', 'bwp_register_my_setting' );

function bwp_setting_field_callback() {

	$value = get_option( 'bwp_field_1' );

	echo '<input type="text" name="bwp_field_1" value="' . esc_attr( $value ) . '" />';
}

function bwp_setting_book_field_callback() {

	$value = get_option( 'bwp_select' );

	?>
	<select name="bwp_select">
		<option>-Select-</option>
		<?php

		$args = array(
			'post_type'      => 'book',
			'status'         => 'publish',
			'posts_per_page' => 10,
		);

		// The Query
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				// $selected = ( get_the_title() == $value ) ? 'selected' : '';
				$selected = selected( get_the_title(), $value, false );

				echo '<option ' . $selected . ' value="' . get_the_title() . '">' . get_the_title() . '</option>';
			}

		} else {
			// no posts found
		}
		/* Restore original Post Data */
		wp_reset_postdata();

		?>
	</select>
	<?php
}

/** Custom admin menu page form - End */

function bwp_admin_sub_menus() {

	add_posts_page(
		__( 'BWP Page Title', 'default' ),
		__( 'BWP Menu Title', 'default' ),
		'manage_options',
		'bwp-page-slug',
		'bwp_admin_sub_menu_callback'
	);
}

add_action( 'admin_menu', 'bwp_admin_sub_menus' );

function bwp_admin_sub_menu_callback() {
	echo '<p>This is sub menu page.</p>';
}

// add_action( 'admin_init', 'bwp_remove_admin_menu' );

function bwp_remove_admin_menu() {
	remove_menu_page( 'tools.php' );
	remove_menu_page( 'bp-activity' );

	remove_submenu_page( 'plugins.php', 'plugin-install.php' );
	remove_submenu_page( 'bp-groups', 'edit-tags.php?taxonomy=bp_group_type' );

}

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

    $url = wp_nonce_url( 'themes.php?page=example', 'example-theme-options' );
    $creds = request_filesystem_credentials( $url, '', false, false, null );
    error_log('***********');
    error_log(print_r($creds,true));
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
            'post_type' => 'book',
            'post_status' => 'publish',
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

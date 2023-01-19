<?php

function metadata_handle_callback( $post_id, $post, $update ) {

	if ( $update ) {
		return;
	}

	// add_post_meta( $post_id, 'test_meta_key', 'yes' );

	// update_post_meta( $post_id, 'test_meta_key', 'no' );

	delete_post_meta( $post_id, 'test_meta_key' );

}

add_action( 'save_post', 'metadata_handle_callback', 10, 3 );

// Add meta box.
function wporg_add_custom_box() {

	$screens = [ 'post', 'book' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'wporg_box_id',                 // Unique ID
			'Custom Meta Box Title',      // Box title
			'wporg_custom_box_html',  // Content callback, must be of type callable
			$screen,                            // Post type
			'normal'
		);
	}

}

add_action( 'add_meta_boxes', 'wporg_add_custom_box' );

function wporg_custom_box_html( $post ) {

	$value = get_post_meta( $post->ID, 'custom_meta_key', true );
	$color = get_post_meta( $post->ID, 'color', true );
	$big_content = get_post_meta( $post->ID, 'big_content', true );
	$big_content_new = get_post_meta( $post->ID, 'big_content_new', true );

	$img_url = wp_get_attachment_url( 1040 );
	$text = 'ABC';

	echo '<img src="' . esc_url( $img_url ) . '" />';
	echo '<a href="' . esc_url( $img_url ) . '" >' . esc_html( $text ) . '</a>';

	?>
	<label for="wporg_field">Custom Field</label>
	<br/>
	<input type="text" name="custom_meta_key" value="<?php echo esc_attr( $value ); ?>">
	<br/>
	<label for="color">Color</label>
	<br/>
	<input type="text" name="color" value="<?php echo esc_attr( $color ); ?>">
	<br/>
	<label for="color">Text Area</label>
	<br/>
	<textarea name="big_content" rows="4"><?php echo $big_content; ?></textarea>
	<br/>
	<label for="color">Text Area 2</label>
	<br/>
	<textarea name="big_content_new" rows="4"><?php echo $big_content_new; ?></textarea>
	<?php
}

function wporg_save_postdata( $post_id ) {

	if ( array_key_exists( 'custom_meta_key', $_POST ) || array_key_exists( 'color', $_POST ) || array_key_exists( 'big_content', $_POST ) ) {

		update_post_meta(
			$post_id,
			'custom_meta_key',
			sanitize_text_field( $_POST['custom_meta_key'] )
		);

		update_post_meta(
			$post_id,
			'color',
			sanitize_text_field( $_POST['color'] )
		);

		update_post_meta(
			$post_id,
			'big_content',
			sanitize_textarea_field( $_POST['big_content'] )
		);

		$allowed_html = array(
			'div' => array(
				'id' => array(),
				'class' => array(),
			),
			'a' => array(),
		);

		update_post_meta(
			$post_id,
			'big_content_new',
			wp_kses( $_POST['big_content_new'], $allowed_html )
		);
	}
}

add_action( 'save_post', 'wporg_save_postdata' );

function wp_custom_body_class( $classes ) {

	// if ( is_single() )
	{
		$classes[] = 'buntywp';
	}

	return $classes;

}

add_filter( 'body_class', 'wp_custom_body_class' );

function wp_custom_css_js() {

	wp_enqueue_script(
		'myplugin-script',
		trailingslashit( MY_PLUGIN_URL ) . 'assets/js/plugin.js',
		array(
			'jquery',
		),
		MY_PLUGIN_VER,
		true
	);

	$args = array(
		'ajaxurl'         => admin_url( 'admin-ajax.php' ),
		'current_user_id' => get_current_user_id(),
		'bwp_nounce'      => wp_create_nonce( 'bwp_ajax_action' ),
	);

	wp_localize_script( 'myplugin-script', 'bwp_obj', $args );

	wp_enqueue_style(
		'myplugin-style',
		trailingslashit( MY_PLUGIN_URL ) . 'assets/css/plugin.css'
	);
}

add_action( 'wp_enqueue_scripts', 'wp_custom_css_js' );

function bunty_custom_rule() {

	global $wp_filesystem;

	include_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	$states_json = $wp_filesystem->get_contents( trailingslashit( MY_PLUGIN_PATH ) . 'assets/states.json' );

	$states = json_decode( $states_json, true );

}

add_action( 'init', 'bunty_custom_rule' );

function buntywp_footer_script() {
	?>
	<script>
		var editor = wp.data.subscribe('core/editor');

		console.log(editor);

		// editor.savePost = function (options) {
		// 	console.log('custom submit');
		// };
	</script>
	<?php
}

// add_action( 'admin_footer', 'buntywp_footer_script' );

function test_emai_callback() {
	$abc = wp_mail( 'abc@xyz.com', 'Mail Subject', 'Mail Content' );
}

add_action( 'init', 'test_emai_callback' );

function buntywp_singular_plural_text() {

	$count = 0;

	echo $count . ' ' . _n( 'Student', 'Students', $count );

}

// add_action( 'init', 'buntywp_singular_plural_text' );


function bwp_delete_trasient( $post_id ) {

	delete_transient( 'bwp_post_list' );

}

add_action( 'save_post_book', 'bwp_delete_trasient' );

<?php

add_shortcode( 'post_list', 'bunty_shortcode' );

function bunty_shortcode( $attr ) {

	$attr = shortcode_atts( array(
		'number'    => 5,
		'post_type' => 'book',
	), $attr );

	$number    = $attr['number'];
	$post_type = $attr['post_type'];

	$data = get_transient( 'bwp_post_list' );

	if ( false !== $data ) {

		$the_query = $data;

	} else {

		$args = array(
			'post_type'      => $post_type,
			'status'         => 'publish',
			'posts_per_page' => $number,
		);

		// The Query
		$the_query = new WP_Query( $args );

		set_transient( 'bwp_post_list', $the_query, 6 * HOUR_IN_SECONDS );

	}

	ob_start();

	include_once ABSPATH . 'wp-admin/includes/image-edit.php';

	wp_image_editor( 26288 ); // change this with attachment ID.

	// The Loop
	if ( $the_query->have_posts() ) {
		echo '<ul>';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$checked = checked( get_the_title(), 'Ram Setu', false );

			echo '<li><input ' . $checked . ' type="checkbox" name="book_title[]" value="' . get_the_title() . '" /> ' . get_the_title() . '</li>';
		}
		echo '</ul>';

	} else {
		// no posts found
	}
	/* Restore original Post Data */
	wp_reset_postdata();

	return ob_get_clean();

}

function bwp_script_callback() {

	wp_enqueue_style( 'imgareaselect' );

	// wp_enqueue_script( 'wp-ajax-response' );
	wp_enqueue_script(
		'image-edit',
		trailingslashit( site_url( 'wp-admin' ) ) . 'js/image-edit.js',
		array( 'wp-i18n', 'jquery', 'jquery-ui-core', 'json2', 'imgareaselect', 'wp-a11y', 'wp-ajax-response' )
	);
}

add_action( 'wp_enqueue_scripts', 'bwp_script_callback' );

function bwp_head() {
	?>
	<script>
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
	</script>
	<?php
}

add_action( 'wp_head', 'bwp_head' );
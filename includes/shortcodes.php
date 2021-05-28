<?php

add_shortcode( 'post_list', 'bunty_shortcode' );

function bunty_shortcode( $attr ) {

	$number    = isset( $attr['number'] ) ? $attr['number'] : 10;
	$post_type = isset( $attr['post_type'] ) ? $attr['post_type'] : 'post';

	$args = array(
		'post_type'      => $post_type,
		'status'         => 'publish',
		'posts_per_page' => $number,
	);

	// The Query
	$the_query = new WP_Query( $args );

	ob_start();

	// The Loop
	if ( $the_query->have_posts() ) {
		echo '<ul>';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			echo '<li>' . get_the_title() . '</li>';
		}
		echo '</ul>';
	} else {
		// no posts found
	}
	/* Restore original Post Data */
	wp_reset_postdata();

	return ob_get_clean();

}
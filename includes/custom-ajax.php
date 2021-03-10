<?php

add_action( 'wp_footer', 'my_action_javascript', 9999 ); // Write our JS below here

function my_action_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var data = {
			'action': 'custom_ajax_action',
			'abc'   : '123',
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post( ajax_object.ajaxurl, data, function( response ) {

			jQuery( '#post-list' ).html(response);

		});
	});
	</script> <?php
}

add_action( 'wp_ajax_custom_ajax_action', 'custom_ajax_callback' );
add_action( 'wp_ajax_nopriv_custom_ajax_action', 'custom_ajax_callback' );

function custom_ajax_callback() {

	$args = array(
		'post_type'      => 'post',
		'status'         => 'publish',
		'posts_per_page' => 10,
	);

	$posts_titles = '';

	// The Query
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		$posts_titles = '<ul>';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$posts_titles .= '<li>' . get_the_title() . '</li>';
		}
		$posts_titles .= '</ul>';
	}
	/* Restore original Post Data */
	wp_reset_postdata();

	echo $posts_titles;

	exit;

}

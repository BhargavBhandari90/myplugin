<?php

add_action( 'rest_api_init', 'bwp_rest_api_init_callback' );

function bwp_rest_api_init_callback() {

	register_rest_field(
		'post',
		'featured_image_url',
		array(
			'get_callback' => 'bwp_featured_image_url',
		)
	);

}

function bwp_featured_image_url( $obj ) {

	$post_id = $obj['id'];

	return get_the_post_thumbnail_url( $post_id );

}
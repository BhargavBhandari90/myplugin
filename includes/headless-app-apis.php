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

	register_rest_route(
		'myplugin/v1',
		'bwp-settings',
		array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'bwp_get_custom_settings',
			'args'     => array(
				'setting-name' => array(
					'required' => true,
				),
				'field-name' => array(
					'required' => false,
				),
			),
		)
	);

}

function bwp_featured_image_url( $obj ) {

	$post_id = $obj['id'];

	$image_url = get_the_post_thumbnail_url( $post_id );

	if ( ! $image_url )  {
		$image_url = 'https://dummyimage.com/720x400';
	}

	return $image_url;

}

function bwp_get_custom_settings( WP_REST_Request $request ) {

	$setting_name = $request->get_param( 'setting-name' );
	$field_name   = $request->get_param( 'field-name' );
	$data         = acf_get_custom_setting( $setting_name, $field_name );

	if ( empty( $data ) ) {
		return new WP_Error(
			'no_setting',
			esc_html__( 'No setting found', 'bwp-core' ),
			array( 'status' => 404 )
		);
	}

	return rest_ensure_response( $data );

}

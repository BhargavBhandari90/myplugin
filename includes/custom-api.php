<?php

function bwp_rest_api_init() {
	register_rest_field(
		'book',
		'book_authors',
		array(
			'get_callback' => 'bwp_get_custom_field',
		)
	);

	register_rest_route(
		'myplugin/v1',
		'acf-books',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'bwp_get_acf_books',
			// 'permission_callback' => 'bwp_permission_callback',
			'args'                => array(
				'meta-key'   => array(
					// 'required'          => true,
					'enum'              => array( 'red', 'green', 'blue' ),
					// 'validate_callback' => function ( $param, $request, $key ) {
					// 	return ! is_numeric( $param );
					// },
				),
				'meta-value' => array(
					// 'required'          => true,
					'default'           => 1,
					'validate_callback' => function ( $param, $request, $key ) {
						return is_numeric( $param );
					},
				),
			),
			'schema'              => 'bwp_get_post_schema',
		)
	);

}

add_action( 'rest_api_init', 'bwp_rest_api_init' );

function bwp_get_custom_field( $obj ) {

	$post_id = $obj['id'];

	return array(
		'author_1' => get_post_meta( $post_id, 'field_1', true ),
		'author_2' => get_post_meta( $post_id, 'field_2', true ),
	);

}

function bwp_get_acf_books( WP_REST_Request $request ) {

	$meta_key   = $request->get_param( 'meta-key' );
	$meta_value = $request->get_param( 'meta-value' );

	$args = array(
		'post_type'      => 'book',
		'status'         => 'publish',
		'posts_per_page' => 10,
		'meta_query'     => array(
			array(
				'key'   => $meta_key,
				'value' => $meta_value,
			),
		),
	);

	// The Query
	$the_query = new WP_Query( $args );
	$acf_books = $the_query->posts;

	if ( empty( $acf_books ) ) {
		return new WP_Error(
			'no_data_found',
			'No data found',
			array(
				'status' => 404,
				'bvbv'   => 'asdas',
			)
		);
	}

	foreach ( $acf_books as $book ) {
		$response = bwp_rest_prepare_post( $book, $request );
		$data[]   = bwp_prepare_for_collection( $response );
	}

	return rest_ensure_response( $data );

}

function bwp_permission_callback() {

	if ( is_user_logged_in() ) {
		return true;
	}

	return false;
}

/**
 * Get our sample schema for comments.
 */
function bwp_get_post_schema() {

	$schema = array(
		// This tells the spec of JSON Schema we are using which is draft 4.
		'$schema'    => 'http://json-schema.org/draft-04/schema#',
		// The title property marks the identity of the resource.
		'title'      => 'acf-book',
		'type'       => 'object',
		// In JSON Schema you can specify object properties in the properties attribute.
		'properties' => array(
			'id'      => array(
				'description' => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
				'type'        => 'integer',
			),
			'author'  => array(
				'description' => esc_html__( 'The id of the user object, if author was a user.', 'my-textdomain' ),
				'type'        => 'integer',
			),
			'content' => array(
				'description' => esc_html__( 'The content for the object.', 'my-textdomain' ),
				'type'        => 'string',
			),
			'title'   => array(
				'description' => esc_html__( 'The title for the object.', 'my-textdomain' ),
				'type'        => 'string',
			),
		),
	);

	return $schema;
}

/**
 * Matches the comment data to the schema we want.
 *
 * @param WP_Comment $comment The comment object whose response is being prepared.
 */
function bwp_rest_prepare_post( $post, $request ) {

	$post_data = array();

	$schema = bwp_get_post_schema();

	// We are also renaming the fields to more understandable names.
	if ( isset( $schema['properties']['id'] ) ) {
		$post_data['id'] = (int) $post->ID;
	}

	if ( isset( $schema['properties']['author'] ) ) {
		$post_data['author'] = (int) $post->post_author;
	}

	if ( isset( $schema['properties']['content'] ) ) {
		$post_data['content'] = apply_filters( 'post_text', $post->post_content, $post );
	}

	if ( isset( $schema['properties']['title'] ) ) {
		$post_data['title'] = $post->post_title;
	}

	return rest_ensure_response( $post_data );
}

/**
 * Prepare a response for inserting into a collection of responses.
 *
 * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
 *
 * @param WP_REST_Response $response Response object.
 * @return array Response data, ready for insertion into collection data.
 */
function bwp_prepare_for_collection( $response ) {
	if ( ! ( $response instanceof WP_REST_Response ) ) {
		return $response;
	}

	$data  = (array) $response->get_data();
	$links = rest_get_server()::get_compact_response_links( $response );

	if ( ! empty( $links ) ) {
		$data['_links'] = $links;
	}

	return $data;
}

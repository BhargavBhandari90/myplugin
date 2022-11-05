<?php


class My_REST_ACF_Books_Controller extends WP_REST_Controller {

	// Here initialize our namespace and resource name.
	public function __construct() {
		$this->namespace     = '/myplugin/v1';
		$this->resource_name = 'acf-books';
	}

	// Register our routes.
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name,
			array(
				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Check permissions for the posts.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_items_permissions_check( $request ) {

		if ( is_user_logged_in() ) {
			return true;
		}

		return false;
	}

	/**
	 * Grabs the five most recent posts and outputs them as a rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_items( $request ) {

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
			return rest_ensure_response( $acf_books );
		}

		foreach ( $acf_books as $book ) {
			$response = $this->prepare_item_for_response( $book, $request );
			$data[]   = $this->prepare_response_for_collection( $response );
		}

		// Return all of our comment response data.
		return rest_ensure_response( $data );
	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @param WP_Post $post The comment object whose response is being prepared.
	 */
	public function prepare_item_for_response( $post, $request ) {

		$post_data = array();

		$schema = $this->get_item_schema();

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
	 * Get our sample schema for a post.
	 *
	 * @return array The sample schema for a post
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			// Since WordPress 5.3, the schema can be cached in the $schema property.
			return $this->schema;
		}

		$this->schema = array(
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

		return $this->schema;
	}

}

// Function to register our new routes from the controller.
function prefix_register_my_rest_routes() {
	$controller = new My_REST_ACF_Books_Controller();
	$controller->register_routes();
}

add_action( 'rest_api_init', 'prefix_register_my_rest_routes' );

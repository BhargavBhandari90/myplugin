<?php


class My_REST_File_Upload extends WP_REST_Controller {

	// Here initialize our namespace and resource name.
	public function My_REST_File_Upload() {
		$this->namespace     = '/myplugin/v1';
		$this->resource_name = 'bwp-upload';
	}

	// Register our routes.
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name,
			array(
				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'upload_item' ),
					// 'permission_callback' => array( $this, 'get_items_permissions_check' ),
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
	public function upload_item( $request ) {

		$file = $request->get_file_params();

		if ( empty( $file ) ) {
			return new WP_Error(
				'bwp_file_missing',
				__( 'Sorry, you have not uploaded any file.', 'bwp-core' ),
				array(
					'status' => 400,
				)
			);
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$attachment_id = media_handle_upload( 'file', 0 );

		if ( ! is_wp_error( $attachment_id ) ) {
			$attachment = get_post( $attachment_id );
			$attachment = $this->prepare_item_for_response( $attachment, $request );
		}

		// Return all of our comment response data.
		return rest_ensure_response( $attachment );
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

		if ( isset( $schema['properties']['link'] ) ) {
			$post_data['link'] = wp_get_attachment_url( $post->ID );
		}

		// if ( isset( $schema['properties']['content'] ) ) {
		// 	$post_data['content'] = apply_filters( 'post_text', $post->post_content, $post );
		// }

		// if ( isset( $schema['properties']['title'] ) ) {
		// 	$post_data['title'] = $post->post_title;
		// }

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
			'title'      => 'bwp-upload',
			'type'       => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => array(
				'id'      => array(
					'description' => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
					'type'        => 'integer',
				),
				'link'  => array(
					'description' => esc_html__( 'Link of the attachment.', 'my-textdomain' ),
					'type'        => 'integer',
				),
			),
		);

		return $this->schema;
	}

}

// Function to register our new routes from the controller.
function bwp_register_my_rest_routes() {
	$controller = new My_REST_File_Upload();
	$controller->register_routes();
}

add_action( 'rest_api_init', 'bwp_register_my_rest_routes' );

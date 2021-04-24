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

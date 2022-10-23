<?php

function bwp_rest_api_init() {
    register_rest_field(
        'book',
        'book_authors',
        array(
            'get_callback' => 'bwp_get_custom_field'
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
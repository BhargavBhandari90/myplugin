<?php

function bwp_acf_form_front( $attr ) {

    if ( ! function_exists( 'acf_form' ) ) {
        return;
    }

   //  ob_start();

    $args = array(
        'id'           => 'bwp-acf-form',
        'post_id'      => 'new_post',
        'new_post'     => array(
            'post_type'   => 'book',
            'post_status' => 'publish',
        ),
        'field_groups' => array( $attr['field_grp_id'] ),
        'post_title'   => true,
        'post_content' => true,
        'submit_value' => __( 'Add Book', 'acf' ),
        'updated_message' => __( "Book updated", 'acf' ),
    );

    acf_form( $args );

    // return ob_get_clean();

}

add_shortcode( 'bwp_acf_form', 'bwp_acf_form_front' );

function bwp_acf_head() {

    if ( ! function_exists( 'acf_form_head' ) ) {
        return;
    }

    acf_form_head();

}

add_action( 'wp_head', 'bwp_acf_head', 2 );

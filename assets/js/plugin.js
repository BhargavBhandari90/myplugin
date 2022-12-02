
console.log( 'Custom JS' );

jQuery(document).ready(function($) {

    var data = {
        'action'   : 'custom_ajax_action',
        'abc'      : '123',
        'author'   : bwp_obj.current_user_id,
        'security' : bwp_obj.bwp_nounce,
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post( bwp_obj.ajaxurl, data, function( response ) {

        jQuery( '#post-list' ).html(response);

    });
});
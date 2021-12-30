<?php


function wporg_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'current-date',
        'What is Today?',
        'wp_show_current_date'
    );
}

add_action( 'wp_dashboard_setup', 'wporg_add_dashboard_widgets' );

function wp_show_current_date() {
    echo date_i18n( 'Y-m-d H:i:s' );
}
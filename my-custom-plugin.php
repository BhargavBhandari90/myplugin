<?php
/**
Plugin Name: My Custom Plugin
Plugin URI: https://wordpress.com/
Description: Plugin Description goes here.
Author: BuntyWP
Version: 1.0.0
Author URI: http://bhargavb.wordpress.com/
*/

include plugin_dir_path( __FILE__ ) . 'includes/custom-post-type.php';
include plugin_dir_path( __FILE__ ) . 'includes/class-custom-widget.php';
include plugin_dir_path( __FILE__ ) . 'includes/custom-ajax.php';

add_action( 'wp_enqueue_scripts', 'vs_con_enqueue_scripts' );

function vs_con_enqueue_scripts(){
 wp_register_script( 
   'ajaxHandle', 
   plugins_url('valid.js', __FILE__), 
   array('jquery'), 
   false, 
   true 
 );
 wp_enqueue_script( 'ajaxHandle');
 wp_localize_script( 
   'ajaxHandle', 
   'ajax_object', 
   array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) 
 );
 wp_enqueue_style( 'bootstrap-style','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );

}



add_action( "wp_ajax_contact", "vs_contact_form" );
add_action( "wp_ajax_nopriv_contact", "vs_contact_form" );


function vs_contact_form(){

   global $wpdb;

       $name = $_POST["name"];
       $email = $_POST["email"];
       $mobileno = $_POST["mobileno"];
       $messsage = $_POST["message"];

       $data = array( 
               'name' => $name, 
               'email' => $email, 
               'mobileno' => $mobileno,
               'message' => $messsage 
           );

       error_log(print_r($data,true));
       
       $tablename = $wpdb->prefix.'contactdetails';
       $insert_row = $wpdb->insert( 
         $tablename, 
           $data
         );
       var_dump($insert_row);
           // if row inserted in table
        if($insert_row){
           echo json_encode(array('res'=>true, 'message'=>__('Message Sent Successfully')));
       }else{
          echo json_encode(array('res'=>false, 'message'=>__('Something went wrong. Please try again later.')));
       }
       wp_die();
}

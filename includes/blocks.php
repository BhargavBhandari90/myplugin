<?php


add_action( 'init', 'register_acf_blocks' );
function register_acf_blocks() {
	register_block_type( trailingslashit( MY_PLUGIN_PATH ) . 'blocks/testimonial' );
}

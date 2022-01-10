<?php

/**
 * This will allow WP to consider changinf the permalink structure for single post.
 *
 * For ex.
 * Default Post URL: http://example.com/post-slug/
 * Allowed by this new rule: http://example.com/post/post-slug/
 *
 * @return void
 */
function custom_rewrite_rules() {

	add_rewrite_rule( '^post/([^/]+)/?$', 'index.php?name=$matches[1]', 'top' );
    // add_rewrite_rule( '^book/([^/]+)/?$', 'index.php?name=$matches[1]', 'top' );
}

add_action( 'init', 'custom_rewrite_rules' );

/**
 * To change ppermalink structure for single post.
 *
 * This will add "post" in the permalink structure.
 *
 * @param string $permalink
 * @param object $post
 * @param string $leavename
 * @return string
 */
function custom_post_link( $permalink, $post, $leavename ) {

	// If it's post, then change the permalink.
	if ( ! empty( $post ) && 'post' === get_post_type( $post->ID ) ) {
		$permalink = home_url( '/post/' . $post->post_name . '/' );
	}

    if ( ! empty( $post ) && 'page' === get_post_type( $post->ID ) ) {
		$permalink = home_url( '/page/' . $post->post_name . '/' );
	}

    if ( ! empty( $post ) && 'book' === get_post_type( $post->ID ) ) {
		$permalink = home_url( '/book/' . $post->post_name . '/' );
	}

	return $permalink;
}

add_filter( 'post_link', 'custom_post_link', 10, 3 );

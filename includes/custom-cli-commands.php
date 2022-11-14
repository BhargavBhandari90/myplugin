<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

class BWP_Command {

	/**
	 * Prints a greeting.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the person to greet.
	 *
	 * [--type=<type>]
	 * : Whether or not to greet the person with success or error.
	 * ---
	 * default: success
	 * options:
	 *   - success
	 *   - error
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *    wp bwp hello BuntyWP --type=error
	 *
	 * @alias hi
	 */
	public function hello( $args, $assoc_args ) {

		$name = $args[0];

		WP_CLI::debug( $args[0], 'namevar' );

		if ( 'success' == $assoc_args['type'] ) {
			WP_CLI::success( 'Hello '. $name .'!' );
		} else {
			WP_CLI::error( 'Hello '. $name .'!' );
		}
	}

	/**
	 * Just a Demo Command for alias.
	 *
	 * ## EXAMPLES
	 *
	 *    wp bwp sort
	 *
	 * @alias sort
	 */
	public function _sort( $args ) {

		WP_CLI::line( 'This is sort command' );

	}

	/**
	 * Prints a result from post type.
	 *
	 * ## OPTIONS
	 *
	 * <posttype>
	 * : Post type.
	 *
	 * [--type=<type>]
	 * : Which format type you want to have an output.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - yaml
	 *   - ids
	 *   - count
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *    wp bwp getitems post --type=csv
	 *
	 * @alias hi
	 */
	public function getitems( $args, $assoc_args ) {

		$post_type = $args[0];
		$type      = $assoc_args['type'];

		if ( ! post_type_exists( $post_type ) ) {
			WP_CLI::error( $post_type . ' Post type doesn\'t exist. Check the post type.' );
			// $post_type = 'post';
		}

		$args = array(
			'post_type'      => $post_type,
			'status'         => 'publish',
			'posts_per_page' => 10,
		);

		$items = array();

		// The Query
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {

			$c = 0;

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$items[$c]['id']    = get_the_ID();
				$items[$c]['title'] = get_the_title();

				$c++;
			}

		}
		/* Restore original Post Data */
		wp_reset_postdata();

		WP_CLI\Utils\format_items( $type, $items, array( 'id', 'title' ) );

	}

	/**
	 * Update options.
	 *
	 * ## OPTIONS
	 *
	 * [--count=<count>]
	 * : How many options to update.
	 * ---
	 * default: 5
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *    wp bwp bwp_progress --count=10
	 */
	public function bwp_progress( $args, $assoc_args ) {

		$count = $assoc_args['count'];

		$progress = WP_CLI\Utils\make_progress_bar( 'Updating Options Progress:', $count );

		for ( $i=0; $i < $count; $i++ ) {

			update_option( 'bwp_progress_' . $i, 'yes' );

			sleep(1);

			$progress->tick();

		}

		$progress->finish();

		WP_CLI::line( WP_CLI::colorize( "%Progress is done...." ) );

	}

	public function bwp_colorise( $args, $assoc_args ) {

		WP_CLI::line( WP_CLI::colorize( "%MBuntyWP\n%YBuntyWP\n%bBuntyWP" ) );

	}

	public function bwp_debug( $args, $assoc_args ) {

		WP_CLI::debug( 'BuntyWP Debuging.....', 'custom-subcommmand' );

	}

	public function bwp_warning( $args, $assoc_args ) {

		WP_CLI::warning( 'Bunty WP test.' );

		WP_CLI::line( 'This is after warning...' );

	}

	public function bwp_error( $args, $assoc_args ) {

		WP_CLI::error( 'Bunty WP test.' );

		WP_CLI::line( 'This is after warning...' );

	}

}

WP_CLI::add_command( 'bwp', 'BWP_Command' );


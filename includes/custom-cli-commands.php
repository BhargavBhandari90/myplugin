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

}

WP_CLI::add_command( 'bwp', 'BWP_Command' );


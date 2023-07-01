<?php

/**
 * Get custom settings. This works for group type field only.
 *
 * @param string $tab
 * @param string $field
 *
 * @return void
 */
function acf_get_custom_setting( $tab, $field = '' ) {

	// Prevent unnecessary function execution.
	if ( ! function_exists( 'have_rows' ) || empty( $tab ) ) {
		return;
	}

	$value = '';

	// Get field value.
	if ( have_rows( $tab, 'option' ) ) {

		if ( ! empty( $field ) ) {

			while ( have_rows( $tab, 'option' ) ) {
				the_row();
				$value = get_sub_field( $field );
			}

		} else {
			$value = get_field( $tab, 'option' );
		}
	}

	return $value;

}
<?php

class acf_field_timezone extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'timezone';

		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __( 'Timezone', 'acf-timezone' );

		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'choice';

		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('timezone', 'error');
		*/

		$this->l10n = array(
			'error' => __( 'Error! Please select a timezone.', 'acf-timezone' ),
		);

		// do not delete!
		parent::__construct();

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/

		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Default Timezone', 'my-plugin' ),
				'instructions' => __( 'e.g. UTC', 'my-plugin' ),
				'type'         => 'text',
				'name'         => 'default_time_zone',
			)
		);
	}


	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {

		/*
		*  Create a select dropdown with all available timezones
		*/
        $fieldValue = trim( $field['value'] );
		if ( ! $fieldValue && $field['default_time_zone'] ) {
			$fieldValue = trim( $field['default_time_zone'] );
		}
		?>
		<select name="<?php echo esc_attr( $field['name'] ); ?>">
			<?php echo wp_timezone_choice( $fieldValue ); ?>
		</select>
		<?php
	}
}

// create field
new acf_field_timezone();

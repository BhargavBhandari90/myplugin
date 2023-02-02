<?php

add_shortcode( 'bwp_uploader', 'bwp_uploader_callback' );

function bwp_uploader_callback() {

	ob_start();

	?>

	<form name="uploader" method="post" enctype="multipart/form-data">
		<input type="file" name="myfile" />
		<input type="hidden" name="bwp_upload" value="1" />
		<input type="submit" name="upload_file" />
	</form>

	<?php

	$file_url = get_option( 'my_upload_image_url' );

	if ( ! empty( $file_url ) ) {
		echo '<img src="'. esc_url( $file_url ) .'" />';
	}

	return ob_get_clean();

}

add_action( 'init', 'bwp_submit_form' );

function bwp_submit_form() {

	if ( isset( $_POST['bwp_upload'] ) ) {

		$file = $_FILES['myfile'];

		$override = array(
			'test_form' => false,
		);

		$uploaded_file = wp_handle_upload( $file, $override );

		if ( ! is_wp_error( $uploaded_file ) ) {

			// echo '<pre>';print_r( $uploaded_file );echo '</pre>';
			update_option( 'my_upload_image_url' , $uploaded_file['url'] );

		}

	}

}

<?php
/**
 * Enqueues the css and js required by the Image Crop.
 */
function blp_core_cover_image_scripts() {
	wp_enqueue_script( 'jcrop', array( 'jquery' ) );
	wp_enqueue_script( 'plupload', array(), false, false );
	wp_enqueue_style( 'jcrop' );
}

add_shortcode( 'bwp_image_cropper', 'bwp_cropper_callback' );

/**
 * Image Crop Shortcode.
 */
function bwp_cropper_callback() {

	if ( ! is_user_logged_in() ) {
		echo "Please Login";
		return;
	}

	blp_core_cover_image_scripts(); // Enqueue style & script.

	ob_start();

	?>

	<div class="bp-uploader-window">
		<div id="bp-upload-ui" style="position: relative;" class="drag-drop">
		<div id="drag-drop-area" style="position: relative;">
			<div class="drag-drop-inside">
				<p class="drag-drop-info">Drop your image here</p>
				<p class="drag-drop-buttons">
					<label for="bp-browse-button" class="bp-screen-reader-text">Select your file</label>
					<input id="browse" type="button" value="Select your file" class="button" style="position: relative; z-index: 1;">
				</p>
			</div>
		</div>
	</div>

	<br />

	<ul id="filelist"></ul>

	<pre id="console"></pre>

	<br/><br/>

	<div id="image-crop-section"></div>

	<script type="text/javascript">
		jQuery(document).ready(function () {

			// plupload uploader.
			var uploader = new plupload.Uploader({

				browse_button: 'browse',
				// General settings
				runtimes : 'html5,flash,silverlight',
				multipart_params : {
					action : 'blp_upload_file'
				},
				url : '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',

				container: 'bp-upload-ui',
				drop_element: 'drag-drop-area',

				multi_selection: false,
		
				// Maximum file size
				max_file_size : '2mb',
		
				chunk_size: '1mb',
		
				// Specify what files to browse for
				filters : [
					{title : "Image files", extensions : "jpg,gif,png"},
					{title : "Zip files", extensions : "zip,avi"}
				],
		
				// Rename files by clicking on their titles
				rename: true,
				
				// Sort files
				sortable: true,
		
				// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
				dragdrop: true,
		
				// Views to activate
				views: {
					list: true,
					thumbs: true, // Show thumbs
					active: 'thumbs'
				},
		
				// Flash settings
				flash_swf_url : '<?php echo includes_url( 'js/plupload/plupload.flash.swf' ); ?>',
			
				// Silverlight settings
				silverlight_xap_url : '<?php echo includes_url( 'js/plupload/plupload.silverlight.xap' ); ?>'
			});

			uploader.init(); // Init the uploader.

			uploader.bind('FilesAdded', function(up, files) {
				var html = '';
				plupload.each(files, function(file) {
					html += '<li id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></li>';
				});
				document.getElementById('filelist').innerHTML += html;
				uploader.start();
			});
				
			uploader.bind('UploadProgress', function(up, file) {
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			});
				
			uploader.bind('Error', function(up, err) {
				document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			});

			uploader.bind('FileUploaded', function(up, file, result) {
				
				const obj = JSON.parse(result.response);
				jQuery( '#image-crop-section' ).html( obj.content );


			});

		});//end document ready fn.
	</script>
	<?php

	return ob_get_clean();

}

add_action( 'wp_ajax_blp_upload_file', 'blp_upload_file_callback' );

/**
 * Upload file.
 */
function blp_upload_file_callback() {

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	$uploadedfile = $_FILES['file'];

	$upload_overrides = array(
		'test_form' => false,
	);

	// Upload.
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

	if ( $movefile && ! isset( $movefile['error'] ) ) {

		ob_start();
		?>
		<div>
			<img src="<?php echo esc_url( $movefile['url'] ); ?>" id="cropImageID" />
			<input type="hidden" name="image-path" id="image-path" value="<?php echo esc_attr( $movefile['file'] ); ?>" />
		</div>
		<div id="btn">
			<input type='button' id="cropBtnID" value="Crop" />
		</div>
		<div>
			<img src="#" id="outputImage" style="display:none;">
		</div>

		<script>
			jQuery( document ).ready( function() {

				// jCrop init.
				var size;
				jQuery('#cropImageID').Jcrop({
					allowSelect  : true,
					allowMove    : true,
					allowResize  : true,
					fixedSupport : true,
					setSelect    : [ 0, 0 , 100 , 100 ],
					onSelect     : function (c) {
						size = {
							x: c.x,
							y: c.y,
							w: c.w,
							h: c.h
						};
	
						jQuery( '#cropBtnID' ).css( 'visibility', 'visible' );

					}//end onSelect
				});//end Jcrop method
	
				jQuery("#cropBtnID").click(function () {

					var img  = jQuery( '#cropImageID' ).attr( 'src' );
					var data = {
						'action'        : 'bwp_crop_image',
						'image_url'     : img,
						'crop_w'        : size.w,
						'crop_h'        : size.h,
						'crop_x'        : size.x,
						'crop_y'        : size.y,
						'original_file' : jQuery( '#image-path' ).val()
					};

					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					jQuery.post( ajaxurl, data, function( response ) {

						jQuery( '#outputImage' )
							.attr( 'src', response.data.file_url )
							.show();

					});
				});

			} );
		</script>

		<?php

		$cropper_content = ob_get_clean();

		$data = array(
			'file'    => $movefile,
			'content' => $cropper_content,
		);

		wp_send_json( $data );
	} else {
		/*
		* Error generated by _wp_handle_upload()
		* @see _wp_handle_upload() in wp-admin/includes/file.php
		*/
		wp_send_json( $movefile['error'] );
	}

	exit;

}

add_action( 'wp_ajax_bwp_crop_image', 'bwp_crop_image' );

/**
 * Crop the Image.
 */
function bwp_crop_image() {

	// Crop.
	$cropped_file = wp_crop_image(
		$_POST['original_file'],
		$_POST['crop_x'],
		$_POST['crop_y'],
		$_POST['crop_w'],
		$_POST['crop_h'],
		$_POST['crop_w'],
		$_POST['crop_h']
	);

	if ( is_wp_error( $cropped_file ) ) {
		wp_send_json_error();
	}

	$cropped_file = wp_unslash( $cropped_file );

	$uploads  = wp_get_upload_dir();
	$arr      = explode( $uploads['basedir'], $cropped_file );
	$arr      = array_values( array_filter( $arr ) );
	$file_url = $uploads['baseurl'] . $arr[0];

	wp_send_json_success(
		array(
			'file_path'     => $cropped_file,
			'file_url'      => $file_url,
			'original_file' => $_POST['original_file'],
		)
	);
}

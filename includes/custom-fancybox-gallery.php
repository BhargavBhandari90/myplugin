<?php

add_filter( 'post_gallery', 'wp_fancybox_image_html', 10, 2 );

function wp_fancybox_image_html( $string, $attr ) {

	$coumns = isset( $attr['columns'] ) ? $attr['columns'] : 3;
	$id     = get_the_ID();
	$size   = isset( $attr['size'] ) ? $attr['size'] : 'thumbnail';
	$posts  = get_posts(
		array(
			'include'   => $attr['ids'],
			'post_type' => 'attachment',
		)
	);

	ob_start();

	echo '<div id="gallery-' . $id . '" class="gallery galleryid-22 gallery-columns-' . $coumns . ' gallery-size-' . $size . '">';

	foreach ( $posts as $imagePost ) {

		?>
		<figure class="gallery-item">
			<div class="gallery-icon landscape">
				<a data-fancybox="gallery" href="<?php echo wp_get_attachment_image_url( $imagePost->ID, $size ); ?>">
					<?php echo wp_get_attachment_image( $imagePost->ID, $size ); ?>
				</a>
			</div>
		</figure>
		<?php
	}

	echo '</div>';

	$string = ob_get_clean();

	return $string;
}

add_action( 'wp_enqueue_scripts' , 'fancybox_scripts' );

function fancybox_scripts() {

	wp_enqueue_style(
		'wp-fancy-style',
		'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css'
	);

	wp_enqueue_script(
		'wp-fancy-script',
		'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js',
		array( 'jquery' )
	);
}


<?php

class WP_Widget_Custom_Post_List extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'widget_custom_post_list',
			'description'                 => __( 'This is Custom post list.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom_post_list', _x( 'Custom post list', 'Custom post list widget' ), $widget_ops );

		add_action(
			'widgets_init',
			function() {
				register_widget( 'WP_Widget_Custom_Post_List' );
			}
		);

	}

	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		?>

		<h1>Custom Widget</h1>

		<?php

		$url = 'http://localhost:8888/wp2/wp-json/wp/v2/posts';

		$request = wp_remote_get( $url );

		if ( ! is_wp_error( $request ) ) {

			$body = wp_remote_retrieve_body( $request );
			$data = json_decode( $body );

			echo '<ul>';

			foreach ( $data as $bwp_post ) {

				echo '<li>' . $bwp_post->title->rendered . '</li>';

			}

			echo '</ul>';

		}

		echo $args['after_widget'];

	}

	public function form( $instance ) {

		$title              = $instance['title'];
		$selected_post_type = $instance['post_type'];
		$limit              = $instance['limit'];
		?>

		<!-- Title -->
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

		<!-- Post Type selection -->
		<p><label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:' ); ?>
		<select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
			<?php

			$ptype_arg = array(
				'public' => true,
			);

			$post_types = get_post_types( $ptype_arg, 'objects' );

			if ( $post_types ) { // If there are any custom public post types.

				echo '<ul>';

				foreach ( $post_types  as $post_type ) {

					echo '<option ' . selected( $selected_post_type, $post_type->name ) . ' value="' . esc_attr( $post_type->name ) . '">' . esc_html( $post_type->label ) . '</option>';

				}

				echo '<ul>';

			}

			?>
		</select>
		</p>

		<!-- Limit -->
		<p><label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" /></label></p>

		<?php

	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}

new WP_Widget_Custom_Post_List();

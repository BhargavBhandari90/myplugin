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

    	?>

    	 <form  class="form-group" method="POST" id="form" action="">
	        <label>Name</label><br>
	        <input class="form-control" type="text" id="name" name="name" ><br>
	        <label>Mobile</label><br>
	        <input type="text" class="form-control" id="mobileno" name="mobileno" required><br>
	        <label>Email</label><br>
	        <input class="form-control"  type="email" id="email" name="email" ><br>
	        <label>Message</label><br>
	        <textarea class="form-control" id="message" name="message"  maxlength="10" onKeyPress="lengthcheck()"></textarea><br><br>
	        <button  class="btn btn-warning" type="submit" id="submit">Send Message</button>
	    </form>

	    <?php

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

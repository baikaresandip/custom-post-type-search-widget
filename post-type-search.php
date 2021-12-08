<?php
/*
	Plugin Name: Custom Post Type Search Widget
	Plugin URI: https://github.com/baikaresandeep
	Description: This custom post type search widget will search from the custom post types which you select in the widget setting.
	Version: 1.0.1
	Author: Baikare Sandeep
  	Author URI: https://github.com/post-type-search
  	Text Domain:       post-type-search
	License:           GPL-2.0+
*/

/**
 * Core class used to implement a Search widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Post_Type_Search_Widget_Search extends WP_Widget {

	/**
	 * Sets up a new Search widget instance.
	 *
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_search',
			'description'                 => __( 'Search documents form for your site.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'search', _x( 'Documents Search', 'Search widget' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Search widget instance.
	 *
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Search widget instance.
	 */
	public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $select   = isset( $instance['posttype'] ) ? $instance['posttype'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Use current theme search form if it exists.
		get_search_form();

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form for the Search widget.
	 *
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		?>
		<p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                <?php _e( 'Title:' ); ?> 
                <input 
                    class="widefat" 
                    id="<?php echo $this->get_field_id( 'title' ); ?>" 
                    name="<?php echo $this->get_field_name( 'title' ); ?>" 
                    type="text" 
                    value="<?php echo esc_attr( $title ); ?>" />
            </label>    
        </p>
        <p>
            <input type="hidden" name="post_type" value="docs" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( 'Select', 'post-type-search' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'posttype' ); ?>" id="<?php echo $this->get_field_id( 'posttype' ); ?>" class="widefat">
            <?php
            $args       = array(
                'public' => true, 
            );
            $post_types = get_post_types( $args, 'objects' );
            // Your options array
           

            // Loop through options and add each one to the select dropdown
            foreach ( $post_types as $post_type ) {
                $labels = get_post_type_labels( $post_type );
                echo '<option value="' . esc_attr( $post_type->name ) . '" id="' . esc_attr( $post_type->name ) . '" '. selected( $select, $key, false ) . '>'. esc_html( $labels->name ) . '</option>';

            } ?>
            </select>
        </p>
        
		<?php
	}

	/**
	 * Handles updating settings for the current Search widget instance.
	 *
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['posttype']   = isset( $new_instance['posttype'] ) ? wp_strip_all_tags( $new_instance['posttype'] ) : '';
		return $instance;
	}

}


/**
 * Register the widget
 * @since 1.0.0
 */
function pts_register_search_widget() {
	register_widget( 'Post_Type_Search_Widget_Search' );
}
add_action( 'widgets_init', 'pts_register_search_widget' );

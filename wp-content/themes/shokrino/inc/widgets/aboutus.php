<?php
/**
 * Class SHKRN_AboutUs
 * code is from developer.wordpress.org/reference/functions/register_widget/
 **/
class SHKRN_AboutUs extends WP_Widget {
    function __construct() {
        parent::__construct( 'aboutus', __('About us : shokrino', 'shokrino') );
    }
    function widget( $args, $instance ) {
        $about = (!empty($instance['about-shokrino'])) ? $instance['about-shokrino'] : $instance['about'];
        echo $args['before_title'];
        echo $instance['title'];
        echo $args['after_title'];
        echo $args['before_widget'];
        echo '<p>';
        if(!empty($about)) {
            echo html_entity_decode($about);
        }
        echo '</p>';
        echo $args['after_widget'];
    }

    function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? esc_attr( $new_instance['title'] ) : '';
        $instance['about-shokrino'] = ( ! empty( $new_instance['about-shokrino'] ) ) ? esc_attr( $new_instance['about-shokrino'] ) : '';
        return $instance;
    }

    function form( $instance ) {
        $title = $about_text = '';
        if (!empty($instance['title'])) { $title = $instance['title']; } else { $title = __( 'About us', 'shokrino' ); }
        if (!empty($instance['about-shokrino'])) { $email = $instance['about-shokrino']; }
        ?>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php __( 'Title: (for example: site title)', 'shokrino' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'about-shokrino' ) ); ?>"><?php __( 'content about us:', 'shokrino' ); ?></label>
        <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'about-shokrino' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'about-shokrino' ) ); ?>" type="text" value="<?php echo esc_attr( $about_text ); ?>"></textarea></p>
        <?php
    }
}

add_action( 'widgets_init', 'shokrino_register_about_widget' );

function shokrino_register_about_widget() {
    register_widget( 'SHKRN_AboutUs' );
}
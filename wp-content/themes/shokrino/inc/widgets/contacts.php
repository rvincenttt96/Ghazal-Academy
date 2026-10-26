<?php
/**
 * Class SHKRN_ContactUs
 * code is from developer.wordpress.org/reference/functions/register_widget/
**/
class SHKRN_ContactUs extends WP_Widget {
    function __construct() {
        parent::__construct( 'contacts', __('Contact us : shokrino', 'shokrino') );
    }
    function widget( $args, $instance ) {
        $old_contact_widget = get_option('widget_contacts');
        $title = (!empty($instance['title'])) ? $instance['title'] : $old_contact_widget['title'];
        $address = (!empty($instance['address'])) ? $instance['address'] : $old_contact_widget['address'];
        $phone = (!empty($instance['phone'])) ? $instance['phone'] : $old_contact_widget['phone'];
        $fax = (!empty($instance['fax'])) ? $instance['fax'] : $old_contact_widget['fax'];
        $email = (!empty($instance['email'])) ? $instance['email'] : $old_contact_widget['email'];
        echo $args['before_title'];
        echo $title;
        echo $args['after_title'];
        echo $args['before_widget'];
        echo '<ul>';
        if(!empty($address)){
            echo '<li><div class="contact-info-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="16px" style="margin-bottom: -3px;fill: var(--text-color-shokrino);" viewBox="0 0 576 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M554.06 161.16L416 224v288l139.88-55.95A32 32 0 0 0 576 426.34V176a16 16 0 0 0-21.94-14.84zM20.12 216A32 32 0 0 0 0 245.66V496a16 16 0 0 0 21.94 14.86L160 448V214.92a302.84 302.84 0 0 1-21.25-46.42zM288 359.67a47.78 47.78 0 0 1-36.51-17C231.83 319.51 210.92 293.09 192 266v182l192 64V266c-18.92 27.09-39.82 53.52-59.49 76.72A47.8 47.8 0 0 1 288 359.67z" class="fa-secondary"/><path d="M288 0a126 126 0 0 0-126 126c0 56.26 82.35 158.8 113.9 196a15.77 15.77 0 0 0 24.2 0C331.65 284.8 414 182.26 414 126A126 126 0 0 0 288 0zm0 168a42 42 0 1 1 42-42 42 42 0 0 1-42 42z" class="fa-primary"/></svg>
                </div><div class="contact-info-value">&nbsp;&nbsp;&nbsp;' . html_entity_decode( $address ) . '</div></li>';
        }
        if(!empty($phone)){
            echo '<li><div class="contact-info-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="16px" style="margin-bottom: -3px;fill: var(--text-color-shokrino);" viewBox="0 0 320 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M0 384v80a48 48 0 0 0 48 48h224a48 48 0 0 0 48-48v-80zm160 96a32 32 0 1 1 32-32 32 32 0 0 1-32 32z" class="fa-secondary"/><path d="M0 384V48A48 48 0 0 1 48 0h224a48 48 0 0 1 48 48v336z" class="fa-primary"/></svg>
            </div><div class="contact-info-value">&nbsp;&nbsp;&nbsp;' . html_entity_decode( $phone ) . '</div></li>';
        }
        if(!empty($fax)){
            echo '<li><div class="contact-info-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="16px" style="margin-bottom: -3px;fill: var(--text-color-shokrino);" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M480 96v96H128V32a32 32 0 0 1 32-32h224v80a16 16 0 0 0 16 16z" class="fa-secondary"/><path d="M64 128H32a32 32 0 0 0-32 32v320a32 32 0 0 0 32 32h32a32 32 0 0 0 32-32V160a32 32 0 0 0-32-32zm416 64H128v288a32 32 0 0 0 32 32h320a32 32 0 0 0 32-32V224a32 32 0 0 0-32-32zM288 432a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm0-128a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm128 128a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm0-128a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zM400 96h80v-4.58a17.92 17.92 0 0 0-5.25-12.67l-73.43-73.5A18 18 0 0 0 388.57 0H384v80a16 16 0 0 0 16 16z" class="fa-primary"/></svg>
            </div><div class="contact-info-value">&nbsp;&nbsp;&nbsp;' . html_entity_decode( $fax ) . '</div></li>';
        }
        if(!empty($email)){
            echo '<li><div class="contact-info-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="16px" style="margin-bottom: -3px;fill: var(--text-color-shokrino);" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M256.47 352h-.94c-30.1 0-60.41-23.42-82.54-40.52C169.39 308.7 24.77 202.7 0 183.33V400a48 48 0 0 0 48 48h416a48 48 0 0 0 48-48V183.36c-24.46 19.17-169.4 125.34-173 128.12-22.12 17.1-52.43 40.52-82.53 40.52zM464 64H48a48 48 0 0 0-48 48v19a24.08 24.08 0 0 0 9.2 18.9c30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4 23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7A23.93 23.93 0 0 0 512 131v-19a48 48 0 0 0-48-48z" class="fa-secondary"/><path d="M512 131v52.36c-24.46 19.17-169.4 125.34-173 128.12-22.12 17.1-52.43 40.52-82.53 40.52h-.94c-30.1 0-60.41-23.42-82.54-40.52C169.39 308.7 24.77 202.7 0 183.33V131a24.08 24.08 0 0 0 9.2 18.9c30.6 23.9 40.7 32.4 173.4 128.7 16.69 12.12 49.75 41.4 72.93 41.4h.94c23.18 0 56.24-29.28 72.93-41.4 132.7-96.3 142.8-104.7 173.4-128.7A23.93 23.93 0 0 0 512 131z" class="fa-primary"/></svg>
            </div><div class="contact-info-value">&nbsp;&nbsp;&nbsp;'.sanitize_email( $email ).'</div></li>';
        }
        echo '</ul>';
        echo $args['after_widget'];
    }

    function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? esc_attr( $new_instance['title'] ) : '';
        $instance['address'] = ( ! empty( $new_instance['address'] ) ) ? esc_attr( $new_instance['address'] ) : '';
        $instance['phone'] = ( ! empty( $new_instance['phone'] ) ) ? esc_attr( $new_instance['phone'] ) : '';
        $instance['fax'] = ( ! empty( $new_instance['fax'] ) ) ? esc_attr( $new_instance['fax'] ) : '';
        $instance['email'] = ( ! empty( $new_instance['email'] ) ) ? sanitize_email( $new_instance['email'] ) : '';
        return $instance;
    }

    function form( $instance ) {
        $title = $email = $address = $phone = $fax = $email = '';
        if (!empty($instance['title'])) { $title = $instance['title']; } else { $title = __( 'Contact us', 'shokrino' ); }
        if (!empty($instance['address'])) { $address = $instance['address']; }
        if (!empty($instance['phone'])) { $phone = $instance['phone']; }
        if (!empty($instance['fax'])) { $fax = $instance['fax']; }
        if (!empty($instance['email'])) { $email = $instance['email']; }
        ?>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title (for example : contact us):', 'shokrino' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>"><?php esc_html_e( 'Address:', 'shokrino' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>" type="text" value="<?php echo esc_attr( $address ); ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>"><?php esc_html_e( 'Phone:', 'shokrino' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'fax' ) ); ?>"><?php esc_html_e( 'Fax:', 'shokrino' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'fax' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fax' ) ); ?>" type="text" value="<?php echo esc_attr( $fax ); ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>"><?php esc_html_e( 'Email:', 'shokrino' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo sanitize_email( $email ); ?>">
        <?php
    }
}

add_action( 'widgets_init', 'shokrino_register_contact_widget' );

function shokrino_register_contact_widget() {
    register_widget( 'SHKRN_ContactUs' );
}

if ( ! class_exists( 'shokrino_zg_admin_setting_bought' ) or ! function_exists( 'shkrn_secret' ) ) {
    echo 'ک'.'د'.' '.'ه'.'ا'.'ی'.' '.'ا'.'م'.'ن'.'ی'.'ت'.'ی'.' '.'ق'.'ا'.'ل'.'ب'.' '.'ر'.'ا'.' '.'ن'.'ب'.'ا'.'ی'.'د'.' '.'ت'.'غ'.'ی'.'ی'.'ر'.' '.'د'.'ه'.'ی'.'د';
    exit();
}
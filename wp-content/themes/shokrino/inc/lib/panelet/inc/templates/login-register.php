<?php
defined('ABSPATH') || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?> >
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <?php wp_head();
    $site_color = pnlt_option('primary_color'); ?>
    <style>
        :root {
            --var-pnlt-main-color: <?php echo $site_color; ?> !important;
        }
    </style>
</head>
<?php
$logo_url = pnlt_option('logo-option')['url'];
?>
<body>
<div class="form-structor flex-center">
    <form id="login" class="woocommerce-form woocommerce-form-login login form-login-pnlt flex-center" method="post">
        <img class="logo-pnlt-login" src="<?php echo $logo_url; ?>" alt="Logo">
        <?php if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('lost-password') or $_GET['page-action'] == "lostpass") {
            require_once "lost-password.php";
        } else { ?>
            <?php do_action( 'woocommerce_before_customer_login_form' ); ?>
            <?php if (isset($_GET['page-action']) && $_GET['page-action'] == "register") { ?>
                <?php do_action( 'woocommerce_register_form_start' ); ?>
                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                    </p>
                <?php endif; ?>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                </p>
                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                        <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                    </p>
                <?php else : ?>
                    <p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
                <?php endif; ?>
                <?php do_action( 'woocommerce_register_form' ); ?>
                <p class="woocommerce-form-row form-row">
                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    <button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
                </p>
                <p class="woocommerce-LostPassword lost_password">
                    اگر قبلا ثبت نام کرده اید روی کلید زیر کلیک کنید
                </p>
                <?php do_action( 'woocommerce_register_form_end' ); ?>
            <?php } else { ?>
                <?php do_action( 'woocommerce_login_form_start' ); ?>
                <?php do_action( 'woocommerce_login_form' ); ?>
                <?php do_action( 'woocommerce_login_form_end' ); ?>
            <?php } ?>
            <?php do_action( 'woocommerce_after_customer_login_form' ); ?>
        <?php } ?>
    </form>
    <?php if (!PNLT_MOBILE && !isset($_GET['page-action']) or $_GET['page-action'] == "login" && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) && !is_wc_endpoint_url('lost-password')) { ?>
        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>?page-action=register" class="go-home-btn-pnlt flex-center">
            <?php echo 'ثبت نام'; ?>
        </a>
    <?php } elseif (isset($_GET['page-action']) && $_GET['page-action'] == "register" or $_GET['page-action'] == "lostpass" or is_wc_endpoint_url('lost-password')) { ?>
        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="go-home-btn-pnlt flex-center">
            <?php echo 'ورود'; ?>
        </a>
    <?php } ?>
    <a href="<?php echo site_url();?>" class="go-home-btn-pnlt flex-center">
        <?php echo 'رفتن به صفحه اصلی'; ?>
    </a>
    <ul class="circles-pnlt">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
</body>
<?php
wp_footer();
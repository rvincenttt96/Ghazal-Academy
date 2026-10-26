<?php
defined('ABSPATH') || exit;
if (!class_exists('Shokrino_ADMIN')) {
    class Shokrino_ADMIN {
        public function __construct() {
            add_action( 'login_enqueue_scripts', [$this, 'wp_login_logo'] );
            add_action( 'admin_enqueue_scripts', [$this, 'font_changer'] );
            add_action( 'elementor/editor/after_enqueue_styles', [$this, 'elementor_persian_font'] );
        }
        public function font_changer() {
            wp_enqueue_style( 'custom_admin_panel_style_shkrn', SHKRN_ASSETS . '/css/adminfontchanger.css');
        }
        public function elementor_persian_font() {
            wp_enqueue_style ('elementor_persian_font_shkrn', SHKRN_ASSETS . '/css/elementor-panel.css');
        }
        public function wp_login_logo() {
            wp_enqueue_style( 'custom_login_page_style_shkrn', SHKRN_ASSETS . '/css/wp-login.css');
            $logo_option = shkrn_options('logo-option');
            $logo_url = is_array($logo_option) ? $logo_option['url'] : array() ;
            ?>
            <style type="text/css">
                #login h1 a, .login h1 a {
                    background-image: url(<?php echo $logo_url; ?>);
                    background-repeat: no-repeat;
                    background-size: contain;
                    width: 100%;
                    box-shadow: none !important;
                }
                @font-face {
                    font-family: 'YekanBakh';
                    src: url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Light.ttf') format('truetype'),
                    url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Light.woff') format('woff'),
                    url('<?php echo SHKRN_ASSETS; ?>css/fonts/yekanbakh/YekanBakhFaNum-Light.eot') format('eot');
                    font-weight: normal;
                }
            </style>
        <?php }
    }
    new Shokrino_ADMIN();
}
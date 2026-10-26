<?php
defined( 'ABSPATH' ) || exit;
/**
 * Main Elementor Test Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Elementor_shokrino_Extension {

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Shokrino_Elementor The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return Shokrino_Elementor An instance of the class.
     */
    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct() {

        add_action( 'after_setup_theme', [ $this, 'on_plugins_loaded' ] );
        add_action('elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ]);

    }

    public function add_elementor_widget_categories($elements_manager)
    {

        $elements_manager->add_category(
            'general_category',
            [
                'title' => __('دیگر المان های شکرینو', 'shokrino'),
                'icon' => 'fas fa-plug',
            ]
        );
        $elements_manager->add_category(
            'shkrn_posts_el',
            [
                'title' => __('المان پست ها شکرینو', 'shokrino'),
                'icon' => 'fas fa-plug',
            ]
        );
        $elements_manager->add_category(
            'shkrn_products_el',
            [
                'title' => __('المان محصولات شکرینو', 'shokrino'),
                'icon' => 'fas fa-plug',
            ]
        );

    }


    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n() {

        load_plugin_textdomain( 'elementor-test-extension' );

    }

    /**
     * On Plugins Loaded
     *
     * Checks if Elementor has loaded, and performs some compatibility checks.
     * If All checks pass, inits the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function on_plugins_loaded() {

        if ( $this->is_compatible() ) {
            add_action( 'elementor/init', [ $this, 'init' ] );
        }

    }

    /**
     * Compatibility Checks
     *
     * Checks if the installed version of Elementor meets the plugin's minimum requirement.
     * Checks if the installed PHP version meets the plugin's minimum requirement.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function is_compatible() {

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return false;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return false;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return false;
        }

        return true;

    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init() {

        $this->i18n();

        // Add Plugin actions
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
        add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );


    }

    /**
     * Init Widgets
     *
     * Include widgets files and register them
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init_widgets() {

        // Include Widget files
        require_once( __DIR__ . '/widgets/posts/shokrino-posts.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-posts-without-bg.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-posts-centered.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-posts-cards.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-posts-grid.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-posts-cloud.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-post-table-without-bg.php' );
        require_once( __DIR__ . '/widgets/posts/shokrino-tv-table.php' );
        require_once( __DIR__ . '/widgets/products/shokrino-product.php' );
        require_once( __DIR__ . '/widgets/products/shokrino-product-dl.php' );
        require_once( __DIR__ . '/widgets/products/shokrino-product-without-bg.php' );
        require_once(__DIR__ . '/widgets/products/shokrino-doroos.php');
        require_once( __DIR__ . '/widgets/other/shokrino-banner.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-slider.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-custom-code.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-search-box.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-count-down.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-step-orders.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-customers.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-video-player.php' );
        require_once( __DIR__ . '/widgets/other/shokrino-audio-player.php' );

        // Register widget
        // Posts
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_posts_without_bg_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_posts_centered() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_posts_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_posts_cloud() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_posts_cards() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_posts_grid() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_post_table() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_tv_Widget() );

        // Products
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_product_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_product_without_bg_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_product_dl_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_lessons() );

        // Other
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_slider_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_banner_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_custom_code_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_search_box_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_count_down_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_step_order_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_costomers_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_video_player() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_shokrino_audio_player() );


    }

    /**
     * Init Controls
     *
     * Include controls files and register them
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init_controls() {


    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin() {



    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {



    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version() {



    }



}

Elementor_shokrino_Extension::instance();


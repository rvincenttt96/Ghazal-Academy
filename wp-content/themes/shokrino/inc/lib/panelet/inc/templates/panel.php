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
$show_date_header = pnlt_option('date_show');
$show_search_header = pnlt_option('search_show');
$show_home_header = pnlt_option('home_show');
$show_notifications_header = pnlt_option('notifications_header_show');
$logo_url = pnlt_option('logo-option')['url'];
?>
<body class="tickets-pnlt">
<?php
if (is_page() && is_account_page()) {
    if (is_user_logged_in()) {
        $skip_status = shkrn_options('can_skip_name');
        if ($skip_status) {
            $user_id = get_current_user_id();
        $info_filled = get_user_meta($user_id, 'filled-user-info-shkrn', true);
        $first_name = get_user_meta($user_id, 'first_name', true);
        $last_name = get_user_meta($user_id, 'last_name', true);
        $defaults = array(
            'class' => 'box-step-popup-items flex-center',
            'id' => '',
            'style' => ''
        );

        if ($info_filled !== "infofilled" && empty($first_name) && empty($last_name)) {
            $args = array();
            $args = wp_parse_args($args, $defaults);
            $args['id'] = 'step-info-shkrn';
            $button_html = '<div';
            $button_html .= !empty($args['class']) ? ' class="' . esc_attr($args['class']) . '"' : '';
            $button_html .= !empty($args['id']) ? ' id="' . esc_attr($args['id']) . '"' : '';
            $button_html .= ' style="' . esc_attr($args['style']) . '">';
            echo $button_html;
            ?>
            <div class="container-popup-shkrn">
                <div class="login-page-logo-site-shkrn flex-center">
                    <h2 class="title-inner-login-page-shkrn flex-center">
                        <?php _e('Welcome to ', 'shokrino' ); ?>
                        <em><?php echo get_bloginfo( 'name' ); ?></em>
                    </h2>
                </div>
                <form id="user-info-form-shkrn" class="shkrn-login-form shkrn-info-popup-form">
                    <span class="loading-form-info">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="40" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate></circle><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="100" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate></circle><circle fill="#FF156D" stroke="#FF156D" stroke-width="15" r="15" cx="160" cy="65"><animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate></circle></svg>
                    </span>
                    <p class="desc-login-page-shkrn">
                        <?php _e('What should we call you?', 'shokrino' ); ?>
                    </p>
                    <div class="error-text-shkrn"></div>
                    <div class="success-text-shkrn"></div>
                    <label class="shkrn-label" for="input_login">
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="first_name" autocomplete="" value="<?php echo ( ! empty( $_POST['first_name'] ) ) ? esc_attr( wp_unslash( $_POST['first_name'] ) ) : ''; ?>" placeholder=" "/><?php // @codingStandardsIgnoreLine ?>
                        <span class="inner-label-input-shkrn"><?php esc_html_e( 'First Name:', 'shokrino' ); ?></span>
                    </label>
                    <label class="shkrn-label" for="input_login">
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="last_name" autocomplete="" value="<?php echo ( ! empty( $_POST['last_name'] ) ) ? esc_attr( wp_unslash( $_POST['last_name'] ) ) : ''; ?>" placeholder=" "/><?php // @codingStandardsIgnoreLine ?>
                        <span class="inner-label-input-shkrn"><?php esc_html_e( 'Last Name:', 'shokrino' ); ?></span>
                    </label>
                    <span class="buttons-popup-step-shkrn">
                        <input type="hidden" name="skip-step-info" id="skip-step-info" value="info">
                        <input type="submit" id="submit-info" value="<?php _e('Next', 'shokrino' ); ?>">
                    </span>
                </form>
            </div>
            </div>
            <?php
        } }
    }
}
?>
<div class="pnlt-loading-popup">
    <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
            <path fill="currentColor" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                <animateTransform
                        attributeName="transform"
                        attributeType="XML"
                        type="rotate"
                        dur="1s"
                        from="0 50 50"
                        to="360 50 50"
                        repeatCount="indefinite" />
            </path>
        </svg>
</div>
<div class="app">
    <div class="main-seperator flex-d">
        <?php do_action( 'woocommerce_account_navigation' ); ?>
        <div class="content-box">
            <div class="content-inner">
                <header class="top-box-panel">
                    <div class="name-and-date">
                <span class="menu-openner mobile" onclick="openMobileMenu()">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 6l16 0" />
                    <path d="M4 12l16 0" />
                    <path d="M4 18l16 0" />
                  </svg>
                </span>
                        <div class="profile-name-box">
                            <?php echo get_user_avatar_panelet('','','','',true); ?>
                            <div class="name-label-box">
                    <span class="name-user">
                      <?php
                      $current_user = wp_get_current_user();
                      if (!empty($current_user->display_name)) {
                          echo $current_user->display_name;
                      } else {
                          echo 'کاربر';
                      }
                      ?>
                    </span>
                                <span class="label-user">
						<?php
                        if( current_user_can('editor') || current_user_can('administrator') ) {
                            ?>
                            مدیر سایت
                            <?php
                        } else {
                            ?>
                            کاربر سایت
                            <?php
                        }
                        ?>
                    </span>
                                </span>
                            </div>
                        </div>
                        <?php
                        if ($show_date_header && class_exists('IntlDateFormatter')) {
                            ?>
                            <div class="calender-profile">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-tabler icon-tabler-calendar-event" width="26" height="26" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M16 3l0 4" />
                                    <path d="M8 3l0 4" />
                                    <path d="M4 11l16 0" />
                                    <path d="M8 15h2v2h-2z" />
                                </svg>
                                <span class="date-panel">
				  <?php
                  $date = Panelet_CORE::date('j M');
                  $formatter = new IntlDateFormatter(
                      'fa_IR@calendar=persian',
                      IntlDateFormatter::LONG,
                      IntlDateFormatter::NONE,
                      'Asia/Tehran',
                      IntlDateFormatter::TRADITIONAL
                  );
                  $date = $formatter->format(Panelet_CORE::date());
                  echo $date;
                  ?>
                  </span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="notifications-search-logout">
                        <?php if ($show_search_header) { ?>
                            <form id="form-search-pnlt" action="<?php echo get_site_url(); ?>/">
                                <input class="popup-form-search-input-pnlt" type="text" placeholder="متن را برای جستجو وارد کنید" name="s" value="<?php the_search_query(); ?>" />
                                <button class="btn-search-pnlt" type="submit">
                                    <svg id="search" xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 0 28.762 28.762">
                                        <path id="Path_7" data-name="Path 7" d="M0,0H28.762V28.762H0Z" fill="none"/>
                                        <circle id="Ellipse_1" data-name="Ellipse 1" cx="8" cy="8" r="8" transform="translate(4 4)" fill="none" stroke="#7d7c7c" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                        <line id="Line_1" data-name="Line 1" x1="7" y1="7" transform="translate(18 18)" fill="none" stroke="#7d7c7c" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </form>
                        <?php }
                        if ($show_home_header) { ?>
                            <a class="home-panel" href="<?php echo site_url(); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                            </a>
                        <?php }
                        if ($show_notifications_header) { ?>
                            <a class="notifications-panel" href="<?php echo esc_url( wc_get_account_endpoint_url( 'notifications' ) ); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-tabler icon-tabler-bell" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                </svg>
                            </a>
                        <?php } ?>
                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>" class="logout-panel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout-2" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                                <path d="M15 12h-12l3 -3" />
                                <path d="M6 15l-3 -3" />
                            </svg>
                        </a>
                    </div>
                </header>
                <main class="content-box-panel">
                    <?php
                    /**
                     * My Account content.
                     *
                     * @since 2.6.0
                     */
                    do_action( 'woocommerce_account_content' );
                    ?>
                </main>
            </div>
        </div>
    </div>
</div>
<!-- End App -->
</body>
<?php
wp_footer();
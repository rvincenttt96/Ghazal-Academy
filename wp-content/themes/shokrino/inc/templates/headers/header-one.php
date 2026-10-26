<?php defined( 'ABSPATH' ) || exit;
$notification_show = shkrn_options('notification-bar-show');
if ($notification_show && !isset($_COOKIE['DisplayNotif'])) {
require_once SHKRN_TMPL . '/notifbar.php';
} ?>
<header class="header-shkrn header-footer-container-shkrn header-one-shkrn">
    <div class="shkrn-mobile-menu-btn flex-center shkrn-except-desktop">
        <a href="#" aria-label="Mobile menu" class="shkrn-menu-mobile-btn flex-center" onclick="open_mobilemenu_shkrn()">
            <svg id="menu" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28.332 30.259">
                <path id="Path_9" data-name="Path 9" d="M0,0H28.332V30.259H0Z" fill="none"/>
                <line id="Line_4" data-name="Line 4" x2="21.481" transform="translate(3.305 7.436)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                <line id="Line_5" data-name="Line 5" x2="21.481" transform="translate(3.305 14.871)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                <line id="Line_6" data-name="Line 6" x2="21.481" transform="translate(3.305 22.307)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            </svg>
        </a>
    </div>
    <a class="shkrn-header-logo flex-center" href="<?php echo get_site_url(); ?>">
        <?php get_sitelogo_shkrn(); ?>
    </a>
    <nav class="header-menu-shkrn main-menu flex-center-in-col shkrn-only-desktop">
        <?php wp_nav_menu(array('theme_location' => 'header-menu', 'container' => '' , 'menu_class' => 'main-menu', 'depth' => 5)); ?>
    </nav>
    <?php
    require_once SHKRN_TMPL . '/page-parts/header-icons.php';
    require_once SHKRN_TMPL . '/page-parts/login-button-header.php';
    require_once SHKRN_TMPL . '/page-parts/search-popup.php';
    require_once SHKRN_TMPL . '/page-parts/nav-mobile.php';
    ?>
</header>
<?php defined( 'ABSPATH' ) || exit;
$notification_show = shkrn_options('notification-bar-show');
$show_search = shkrn_options('show_search');
if ($notification_show && !isset($_COOKIE['DisplayNotif'])) {
    require_once SHKRN_TMPL . '/notifbar.php';
} ?>
<header class="header-shkrn header-fullwidth-background">
    <div class="header-footer-container-shkrn header-two-shkrn">
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
        <?php if ($show_search) { ?>
            <div class="header-two-search-container-shkrn">
                <form action="<?php echo get_site_url(); ?>/">
                    <label>
                        <input type="text" placeholder="<?php esc_html_e('Search...', 'shokrino' ); ?>" name="s" value="<?php the_search_query(); ?>">
                    </label>
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" height="22px" style="margin-bottom:-3px;" fill="#7d7c7c" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M507.3 484.7l-141.5-141.5C397 306.8 415.1 259.7 415.1 208c0-114.9-93.13-208-208-208S-.0002 93.13-.0002 208S93.12 416 207.1 416c51.68 0 98.85-18.96 135.2-50.15l141.5 141.5C487.8 510.4 491.9 512 496 512s8.188-1.562 11.31-4.688C513.6 501.1 513.6 490.9 507.3 484.7zM208 384C110.1 384 32 305 32 208S110.1 32 208 32S384 110.1 384 208S305 384 208 384z"/></svg>
                    </button>
                </form>
            </div>
        <?php }
        require_once SHKRN_TMPL . '/page-parts/login-button-header.php';
        require_once SHKRN_TMPL . '/page-parts/nav-mobile.php';
        ?>
    </div>
    <div class="header-footer-container-shkrn header-two-shkrn-bottom">
        <nav class="header-menu-shkrn main-menu flex-center-in-col shkrn-only-desktop">
            <?php wp_nav_menu(array('theme_location' => 'header-menu', 'container' => '' , 'menu_class' => 'main-menu', 'depth' => 5)); ?>
        </nav>
        <?php
        require_once SHKRN_TMPL . '/page-parts/header-icons.php';
        ?>
    </div>
</header>
<?php
defined( 'ABSPATH' ) || exit;
global $woocommerce;
$count_products = $woocommerce->cart->cart_contents_count;
$show_cart = shkrn_options('show_cart');
$show_search = shkrn_options('show_search');
?>
<div class="shkrn-mobile-menu-box shkrn-except-desktop">
    <div class="shkrn-mobile-menu-inside flex-center">
        <svg id="shkrn-menu-mobile-close-btn" data-name="close btn" xmlns="http://www.w3.org/2000/svg" width="34.644" height="34.644" viewBox="0 0 34.644 34.644" onclick="close_mobile_menu_shkrn()">
            <path id="Path_78" data-name="Path 78" d="M0,0H34.644V34.644H0Z" fill="none"/>
            <line id="Line_14" data-name="Line 14" x1="17" y2="17" transform="translate(9 9)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <line id="Line_15" data-name="Line 15" x2="17" y2="17" transform="translate(9 9)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
        <div class="shkrn-mobile-menu-box-item nav-logo-site-shkrn flex-center">
            <?php get_sitelogo_shkrn(); ?>
        </div>
        <div class="shkrn-mobile-menu-box-item flex-center">
            <form id="shkrn-mobile-menu-form-search" action="<?php echo get_site_url(); ?>/">
                <input type="text" placeholder="متن را برای جستجو وارد کنید..." name="s" value="<?php the_search_query(); ?>">
                <button type="submit">
                    <svg id="search" xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 28.762 28.762">
                        <path id="Path_7" data-name="Path 7" d="M0,0H28.762V28.762H0Z" fill="none"/>
                        <circle id="Ellipse_1" data-name="Ellipse 1" cx="8" cy="8" r="8" transform="translate(4 4)" fill="none" stroke="var(--shkrn-var-color-light-dark-opacity-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <line id="Line_1" data-name="Line 1" x1="7" y1="7" transform="translate(18 18)" fill="none" stroke="var(--shkrn-var-color-light-dark-opacity-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </button>
            </form>
        </div>
        <div class="shkrn-mobile-menu-box-item shkrn-mobile-menu-item-btns flex-center">
            <?php darkmode_shkrn_button('mobile'); ?>
            <?php if (function_exists('WC') && $show_cart) : ?>
                <a href="<?php echo wc_get_cart_url(); ?>" class="header-icon-shkrn flex-center cart-icon-shkrn">
            <span class="bag-icon flex-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="23px" fill="var(--shkrn-var-color-light-dark-text)" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M352 128C352 57.421 294.579 0 224 0 153.42 0 96 57.421 96 128H0v304c0 44.183 35.817 80 80 80h288c44.183 0 80-35.817 80-80V128h-96zM224 32c52.935 0 96 43.065 96 96H128c0-52.935 43.065-96 96-96zm192 400c0 26.467-21.533 48-48 48H80c-26.467 0-48-21.533-48-48V160h64v48c0 8.837 7.164 16 16 16s16-7.163 16-16v-48h192v48c0 8.837 7.163 16 16 16s16-7.163 16-16v-48h64v272z"/></svg>
                <?php if ($woocommerce->cart->cart_contents_count != "0") { ?>
                    <span class="shokrino-cart-number"><?php echo $count_products; ?></span>
                <?php } ?>
            </span>
                </a>
            <?php endif; ?>
        </div>
        <div class="shkrn-mobile-menu-box-item shkrn-mobile-menu-list-li flex-center">
            <?php wp_nav_menu(array('theme_location' => 'header-menu', 'container' => '' , 'menu_class' => 'shkrn-mobile-menu' , 'menu_id' => false, 'depth' => 5)); ?>
        </div>
    </div>
    <div class="shkrn-mobile-menu-outside flex-center" onclick="close_mobile_menu_shkrn()"></div>
</div>
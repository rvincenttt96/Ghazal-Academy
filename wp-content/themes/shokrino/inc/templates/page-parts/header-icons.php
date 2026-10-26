<?php
defined( 'ABSPATH' ) || exit;

global $woocommerce;
$count_products = $woocommerce->cart->cart_contents_count;
$header_type = shkrn_options('header_style');
$show_cart = shkrn_options('show_cart');
$show_search = $header_type == "header_two" ? false : shkrn_options('show_search');
?>
<div class="header-icons-shkrn flex-center shkrn-only-desktop">
    <?php darkmode_shkrn_button(); ?>
    <?php if ($show_search) { ?>
        <a href="#" class="header-icon-shkrn flex-center search-icon-shkrn" onclick="open_searchbox_shkrn()" style="cursor: pointer">
            <svg xmlns="http://www.w3.org/2000/svg" height="23px" fill="var(--shkrn-var-color-light-dark-text)" viewBox="0 0 512 512">
                <!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
                <path d="M507.3 484.7l-141.5-141.5C397 306.8 415.1 259.7 415.1 208c0-114.9-93.13-208-208-208S-.0002 93.13-.0002 208S93.12 416 207.1 416c51.68 0 98.85-18.96 135.2-50.15l141.5 141.5C487.8 510.4 491.9 512 496 512s8.188-1.562 11.31-4.688C513.6 501.1 513.6 490.9 507.3 484.7zM208 384C110.1 384 32 305 32 208S110.1 32 208 32S384 110.1 384 208S305 384 208 384z"/>
            </svg>
        </a>
    <?php } ?>
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
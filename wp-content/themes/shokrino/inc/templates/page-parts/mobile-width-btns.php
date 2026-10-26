<?php
defined( 'ABSPATH' ) || exit;

global $woocommerce;
$count_products = $woocommerce->cart->cart_contents_count;
$show_cart = shkrn_options('show_cart');
if (function_exists('WC')) {
    $my_account_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
}
?>
<div class="mobile-buttons mobile-width">

    <div id="mobile-menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="18px" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M442 114H6a6 6 0 0 1-6-6V84a6 6 0 0 1 6-6h436a6 6 0 0 1 6 6v24a6 6 0 0 1-6 6zm0 160H6a6 6 0 0 1-6-6v-24a6 6 0 0 1 6-6h436a6 6 0 0 1 6 6v24a6 6 0 0 1-6 6zm0 160H6a6 6 0 0 1-6-6v-24a6 6 0 0 1 6-6h436a6 6 0 0 1 6 6v24a6 6 0 0 1-6 6z"/></svg>
    </div>

    <div class="login-button-mobile mobile-width">
        <?php if (is_user_logged_in()) {
            ?><a href="<?php echo $my_account_url; ?>"
                 class="logged-in-header-button logged-in-header-button-mobile">
                <svg xmlns="http://www.w3.org/2000/svg" height="17px" style="margin-bottom: 3px;" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M352 128A128 128 0 1 1 224 0a128 128 0 0 1 128 128z" class="fa-secondary"></path><path d="M313.6 288h-16.7a174.1 174.1 0 0 1-145.8 0h-16.7A134.43 134.43 0 0 0 0 422.4V464a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48v-41.6A134.43 134.43 0 0 0 313.6 288z" class="fa-primary"></path></svg>
            </a>
            <?php
        } else {
            ?>
            <?php if (!empty($login_url)) { ?>
                <a href="<?php echo $login_url ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" height="17px" style="margin-bottom: 3px;" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M352 128A128 128 0 1 1 224 0a128 128 0 0 1 128 128z" class="fa-secondary"></path><path d="M313.6 288h-16.7a174.1 174.1 0 0 1-145.8 0h-16.7A134.43 134.43 0 0 0 0 422.4V464a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48v-41.6A134.43 134.43 0 0 0 313.6 288z" class="fa-primary"></path></svg>
                </a>
            <?php } else { ?>
            <a href="<?php echo $my_account_url; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" height="17px" style="margin-bottom: 3px;" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M352 128A128 128 0 1 1 224 0a128 128 0 0 1 128 128z" class="fa-secondary"></path><path d="M313.6 288h-16.7a174.1 174.1 0 0 1-145.8 0h-16.7A134.43 134.43 0 0 0 0 422.4V464a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48v-41.6A134.43 134.43 0 0 0 313.6 288z" class="fa-primary"></path></svg>
                </a><?php
            }
        }
        ?>
    </div>

    <?php if (function_exists('WC') && $show_cart) : ?>
        <li class="search-icon cart-mobile-icon mobile-width"><a href="<?php echo wc_get_cart_url(); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18px" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M352 128C352 57.421 294.579 0 224 0 153.42 0 96 57.421 96 128H0v304c0 44.183 35.817 80 80 80h288c44.183 0 80-35.817 80-80V128h-96zM224 32c52.935 0 96 43.065 96 96H128c0-52.935 43.065-96 96-96zm192 400c0 26.467-21.533 48-48 48H80c-26.467 0-48-21.533-48-48V160h64v48c0 8.837 7.164 16 16 16s16-7.163 16-16v-48h192v48c0 8.837 7.163 16 16 16s16-7.163 16-16v-48h64v272z"/></svg>
                <span class="shokrino-cart-number"><?php echo $count_products; ?></span>
            </a></li>
    <?php endif; ?>

</div>

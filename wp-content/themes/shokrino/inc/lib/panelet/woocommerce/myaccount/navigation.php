<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

// Panelet Edition

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
do_action( 'woocommerce_before_account_navigation' );
$logo_url = pnlt_option('logo-option')['url'];
?>

<span class="blur-bg-side-menu-closer mobile" onclick="closeMobileMenu()"></span>
<nav class="side-box">
    <svg class="close-menu-mobile mobile" xmlns="http://www.w3.org/2000/svg" width="34.644" height="34.644" viewBox="0 0 34.644 34.644" onclick="closeMobileMenu()">
        <path id="Path_78" data-name="Path 78" d="M0,0H34.644V34.644H0Z" fill="none"></path> <line id="Line_14" data-name="Line 14" x1="17" y2="17" transform="translate(9 9)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></line> <line id="Line_15" data-name="Line 15" x2="17" y2="17" transform="translate(9 9)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></line>
    </svg>
    <a href="<?php echo site_url(); ?>" class="sidebar-logo flex">
        <img src="<?php echo $logo_url; ?>" alt="Logo">
    </a>
    <div class="sidebar-menu sidebar-center flex flex-c">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) { ?>
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="menu-item-icon flex <?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                <?php if ($endpoint == 'dashboard') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1a3847" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                    </svg>
                <?php } elseif ($endpoint == 'orders') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-basket" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="7 10 12 4 17 10" />
                        <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z" />
                        <circle cx="12" cy="15" r="2" />
                    </svg>
                <?php } elseif ($endpoint == 'downloads') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <polyline points="7 11 12 16 17 11" />
                        <line x1="12" y1="4" x2="12" y2="16" />
                    </svg>
                <?php } elseif ($endpoint == 'edit-address') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="11" r="3" />
                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                    </svg>
                <?php } elseif ($endpoint == 'edit-account') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                <?php } elseif ($endpoint == 'woo-wallet' or $endpoint == 'p-wallet') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"></path>
                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"></path>
                    </svg>
                <?php } elseif ($endpoint == 'notifications') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bell-ringing" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                        <path d="M21 6.727a11.05 11.05 0 0 0 -2.794 -3.727" />
                        <path d="M3 6.727a11.05 11.05 0 0 1 2.792 -3.727" />
                    </svg>
                <?php } elseif ($endpoint == 'p-tickets') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-messages" width="26" height="26" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M21 14l-3 -3h-7a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h9a1 1 0 0 1 1 1v10" />
                        <path d="M14 15v2a1 1 0 0 1 -1 1h-7l-3 3v-10a1 1 0 0 1 1 -1h2" />
                    </svg>
                <?php } else { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box-multiple" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="7" y="3" width="14" height="14" rx="2" />
                        <path d="M17 17v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h2" />
                    </svg>
                <?php } ?>
                <?php
                if ($endpoint == 'edit-account') {
                    echo "ویرایش پروفایل";
                } else {
                    echo esc_html($label);
                } ?>
            </a>
        <?php } ?>
    </div>
    <div class="sidebar-bottom flex flex-c">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>" class="menu-item-icon flex">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1a3847" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                <path d="M9 12h12l-3 -3" />
                <path d="M18 15l3 -3" />
            </svg>
            &nbsp; خروج از اکانت
        </a>
    </div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>

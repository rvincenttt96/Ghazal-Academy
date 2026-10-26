<?php
defined( 'ABSPATH' ) || exit;
global $current_user;
$current_user = wp_get_current_user();
$layout = is_array(shkrn_options('panel-header-sort')) ? shkrn_options('panel-header-sort')['enabled'] : '';
$header_has_users_info = shkrn_options('header_has_users_info ');
$header_button_type = shkrn_options('header_button_type');
$header_button_custom_popup = shkrn_options('header_button_custom_popup');
$my_account_url = function_exists('WC') ? get_permalink(get_option('woocommerce_myaccount_page_id')) : '';
$login_url = !empty(shkrn_options('header_button_custom_link')) ? shkrn_options('header_button_custom_link') : $my_account_url;
$login_btn_text = shkrn_options('header_button_custom_text');
?>
<div class="userpanel-btn-box shkrn-only-desktop">
    <?php if (is_user_logged_in()) { ?>
        <div class="header-user-panel-box-btn-left">
            <div class="user-panel-avatar">
                <?php
                $avatar_user = get_avatar($current_user->ID, 47);
                if (!empty($avatar_user)) {
                    echo $avatar_user;
                } else { ?>
                    <img width="47px" height="47px" src="<?php echo SHKRN_ASSETS. '/img/user.jpg' ?>">
                <?php } ?>
            </div>
            <?php
            if ($header_has_users_info) { ?>
                <div class="user-panel-name-and-role">
                <span class="name-user">
                    <?php
                    echo $current_user->display_name;
                    ?>
                </span>
                    <span class="role-user">
                    <?php if( current_user_can('editor') || current_user_can('administrator') ) {
                        _e('Admin','shokrino');
                    } else {
                        _e('User','shokrino');
                    } ?>
                </span>
                </div>
                <div class="user-panel-icon-chevron">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.1px" stroke="#7d7c7c" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
            <?php } ?>
            <div class="header-menu-user-panel-box">
                <?php
                if ( $layout ): foreach ( $layout as $key => $value ) {
                    switch($key) {
                        case 'show-user-panel':
                            if (function_exists('WC')) { ?>
                                <a href="<?php
                                if ($header_button_type == 'header_button_link' && !empty($login_url)) {
                                    echo $login_url;
                                } elseif ($header_button_type == 'header_button_digits' && shortcode_exists('dm-modal')) {
                                    echo '?login=true';
                                } elseif ($header_button_type == 'header_button_popup_elementor') {
                                    echo $header_button_custom_popup;
                                } elseif ($header_button_type == 'header_button_link' && empty($login_url)) {
                                    echo $my_account_url;
                                } else {
                                    echo $login_url;
                                }
                                ?>"
                                   class="box-h-m-panel show-user-panel-btn">
                                    <?php
                                    if (!empty($login_btn_text)) {
                                        echo $login_btn_text;
                                    } else {
                                        _e('Go to panel', 'shokrino');
                                    }
                                    ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                        <line x1="5" y1="12" x2="11" y2="18" />
                                        <line x1="5" y1="12" x2="11" y2="6" />
                                    </svg>
                                </a>

                            <?php }
                            break;
                        case 'woo-wallet':
                            $panelet_wallet = shkrn_options('show_wallet_userpanel');
                            $woo_wallet_link = $panelet_wallet ? wc_get_account_endpoint_url('p-wallet') : wc_get_account_endpoint_url('woo-wallet');
                            if (function_exists('WC')) {
                                if (class_exists( 'WooWallet' )) { ?>
                                <a href="<?php echo $woo_wallet_link; ?>" class="box-h-m-panel">
                                    <?php _e('Wallet balance:','shokrino'); ?>
                                    <?php
                                    $title = __('Current wallet balance', 'woo-wallet');
                                    $menu_item = woo_wallet()->wallet->get_wallet_balance(get_current_user_id());
                                    echo $menu_item;
                                    ?>
                                </a>
                            <?php } }
                            break;
                        case 'downloads':
                            if (function_exists('WC')) { ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('downloads')); ?>" class="box-h-m-panel">
                                    <?php _e('Downloads','shokrino'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <polyline points="7 11 12 16 17 11" />
                                        <line x1="12" y1="4" x2="12" y2="16" />
                                    </svg>
                                </a>
                            <?php }
                            break;
                        case 'tickets':
                            if (function_exists('WC')) { ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('p-tickets')); ?>" class="box-h-m-panel">
                                    <?php _e('تیکت ها','shokrino'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-messages" width="20" height="20" viewBox="0 0 24 24" stroke-width="1" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M21 14l-3 -3h-7a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h9a1 1 0 0 1 1 1v10" />
                                        <path d="M14 15v2a1 1 0 0 1 -1 1h-7l-3 3v-10a1 1 0 0 1 1 -1h2" />
                                    </svg>
                                </a>
                            <?php }
                            break;
                        case 'orders':
                            if (function_exists('WC')) { ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="box-h-m-panel">
                                    <?php _e('View orders','shokrino'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-basket" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <polyline points="7 10 12 4 17 10"></polyline>
                                        <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z"></path>
                                        <circle cx="12" cy="15" r="2"></circle>
                                    </svg>
                                </a>
                            <?php }
                            break;
                        case 'notifs':
                            if (function_exists('WC')) { ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('notifications')); ?>" class="box-h-m-panel">
                                    <?php _e('Notifications','shokrino'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bell-ringing" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"></path>
                                        <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                                        <path d="M21 6.727a11.05 11.05 0 0 0 -2.794 -3.727"></path>
                                        <path d="M3 6.727a11.05 11.05 0 0 1 2.792 -3.727"></path>
                                    </svg>
                                </a>
                            <?php }
                            break;
                        case 'edit-profile':
                            if (function_exists('WC')) { ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="box-h-m-panel">
                                    <?php _e('Edit profile','shokrino'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            <?php }
                            break;
                        case 'logout':
                            if (function_exists('WC')) { ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('customer-logout')); ?>" class="box-h-m-panel" style="color: red !important;">
                                    <?php _e('Logout','shokrino'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="20px" height="20px" style="transform: scale(-1,1);" viewBox="0 0 24 24" stroke-width="1.5" stroke="red" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                        <path d="M7 12h14l-3 -3m0 6l3 -3" />
                                    </svg>
                                </a>
                            <?php }
                            break;
                    }
                }
                endif;
                ?>
            </div>
        </div>
        <?php
    } else { ?>
        <?php if ($header_button_type == 'header_button_link' && !empty($login_url)) { ?>
            <a href="<?php echo $login_url ?>" class="shkrn-login-register-btn flex-center shkrn-header-btn-bg shkrn-only-desktop">
                <svg id="fingerprint" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path id="Path_1" data-name="Path 1" d="M0,0H24V24H0Z" fill="none"></path><path id="Path_2" data-name="Path 2" d="M18.9,7A8,8,0,0,1,20,12v1a6,6,0,0,0,.8,3" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_3" data-name="Path 3" d="M8,11a4,4,0,0,1,8,0v1a10,10,0,0,0,2,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_4" data-name="Path 4" d="M12,11v2a14,14,0,0,0,2.5,8" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_5" data-name="Path 5" d="M8,15a18,18,0,0,0,1.8,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_6" data-name="Path 6" d="M4.9,19A22,22,0,0,1,4,12V11A8,8,0,0,1,16,4.05" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            <?php if (!empty($login_btn_text)) { echo $login_btn_text; } else { ?> <?php esc_html_e('Login / Register', 'shokrino' ); ?><?php } ?></a>
        <?php } elseif ($header_button_type == 'header_button_digits' && shortcode_exists('dm-modal')) { ?>
            <a class="shkrn-login-register-btn flex-center shkrn-header-btn-bg shkrn-only-desktop"><?php echo do_shortcode ('[dm-modal]'); ?></a>
        <?php } elseif ($header_button_type == 'header_button_popup_elementor') { ?>
            <a href="<?php echo $header_button_custom_popup; ?>" class="shkrn-login-register-btn flex-center shkrn-header-btn-bg shkrn-only-desktop">
                <svg id="fingerprint" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path id="Path_1" data-name="Path 1" d="M0,0H24V24H0Z" fill="none"></path><path id="Path_2" data-name="Path 2" d="M18.9,7A8,8,0,0,1,20,12v1a6,6,0,0,0,.8,3" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_3" data-name="Path 3" d="M8,11a4,4,0,0,1,8,0v1a10,10,0,0,0,2,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_4" data-name="Path 4" d="M12,11v2a14,14,0,0,0,2.5,8" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_5" data-name="Path 5" d="M8,15a18,18,0,0,0,1.8,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_6" data-name="Path 6" d="M4.9,19A22,22,0,0,1,4,12V11A8,8,0,0,1,16,4.05" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            <?php if (!empty($login_btn_text)) { echo $login_btn_text; } else { ?> <?php esc_html_e('Login / Register', 'shokrino' ); ?><?php } ?></a>
        <?php } elseif ($header_button_type == 'header_button_link' && empty($login_url)) { ?>
        <a href="<?php echo $my_account_url; ?>" class="shkrn-login-register-btn flex-center shkrn-header-btn-bg shkrn-only-desktop">
                <svg id="fingerprint" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path id="Path_1" data-name="Path 1" d="M0,0H24V24H0Z" fill="none"></path><path id="Path_2" data-name="Path 2" d="M18.9,7A8,8,0,0,1,20,12v1a6,6,0,0,0,.8,3" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_3" data-name="Path 3" d="M8,11a4,4,0,0,1,8,0v1a10,10,0,0,0,2,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_4" data-name="Path 4" d="M12,11v2a14,14,0,0,0,2.5,8" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_5" data-name="Path 5" d="M8,15a18,18,0,0,0,1.8,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_6" data-name="Path 6" d="M4.9,19A22,22,0,0,1,4,12V11A8,8,0,0,1,16,4.05" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            <?php if (!empty($login_btn_text)) { echo $login_btn_text; } else { ?> <?php esc_html_e('Login / Register', 'shokrino' ); ?><?php } ?></a><?php
        } else { ?>
            <a href="<?php echo $login_url ?>" class="shkrn-login-register-btn flex-center shkrn-header-btn-bg shkrn-only-desktop">
                <svg id="fingerprint" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path id="Path_1" data-name="Path 1" d="M0,0H24V24H0Z" fill="none"></path><path id="Path_2" data-name="Path 2" d="M18.9,7A8,8,0,0,1,20,12v1a6,6,0,0,0,.8,3" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_3" data-name="Path 3" d="M8,11a4,4,0,0,1,8,0v1a10,10,0,0,0,2,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_4" data-name="Path 4" d="M12,11v2a14,14,0,0,0,2.5,8" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_5" data-name="Path 5" d="M8,15a18,18,0,0,0,1.8,6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="Path_6" data-name="Path 6" d="M4.9,19A22,22,0,0,1,4,12V11A8,8,0,0,1,16,4.05" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                <?php if (!empty($login_btn_text)) { echo $login_btn_text; } else { ?> <?php esc_html_e('Login / Register', 'shokrino' ); ?><?php } ?></a>
        <?php } ?>
    <?php }
    ?>
</div>
<?php if (is_user_logged_in()) { ?>
<?php if ($header_button_type == 'header_button_link' && !empty($login_url)) { ?>
    <a href="<?php echo $login_url ?>" aria-label="User panel" class="shkrn-menu-mobile-btn flex-center shkrn-except-desktop">
        <svg id="user" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 31.136 31.136">
            <path id="Path_10" data-name="Path 10" d="M0,0H31.136V31.136H0Z" fill="none"/>
            <circle id="Ellipse_2" data-name="Ellipse 2" cx="5.5" cy="5.5" r="5.5" transform="translate(10 4)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <path id="Path_11" data-name="Path 11" d="M6,22.784V20.189A5.189,5.189,0,0,1,11.189,15h5.189a5.189,5.189,0,0,1,5.189,5.189v2.595" transform="translate(1.784 4.46)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
    </a>
<?php } elseif ($header_button_type == 'header_button_digits' && shortcode_exists('dm-modal')) { ?>
    <a href="?login=true" aria-label="User panel" class="shkrn-menu-mobile-btn flex-center shkrn-except-desktop">
        <svg id="user" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 31.136 31.136">
            <path id="Path_10" data-name="Path 10" d="M0,0H31.136V31.136H0Z" fill="none"/>
            <circle id="Ellipse_2" data-name="Ellipse 2" cx="5.5" cy="5.5" r="5.5" transform="translate(10 4)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <path id="Path_11" data-name="Path 11" d="M6,22.784V20.189A5.189,5.189,0,0,1,11.189,15h5.189a5.189,5.189,0,0,1,5.189,5.189v2.595" transform="translate(1.784 4.46)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
    </a>
<?php } elseif ($header_button_type == 'header_button_popup_elementor') { ?>
    <a href="<?php echo $header_button_custom_popup; ?>" aria-label="User panel" class="shkrn-menu-mobile-btn flex-center shkrn-except-desktop">
        <svg id="user" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 31.136 31.136">
            <path id="Path_10" data-name="Path 10" d="M0,0H31.136V31.136H0Z" fill="none"/>
            <circle id="Ellipse_2" data-name="Ellipse 2" cx="5.5" cy="5.5" r="5.5" transform="translate(10 4)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <path id="Path_11" data-name="Path 11" d="M6,22.784V20.189A5.189,5.189,0,0,1,11.189,15h5.189a5.189,5.189,0,0,1,5.189,5.189v2.595" transform="translate(1.784 4.46)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
    </a>
<?php } elseif ($header_button_type == 'header_button_link' && empty($login_url)) { ?>
    <a href="<?php echo $login_url; ?>" aria-label="User panel" class="shkrn-menu-mobile-btn flex-center shkrn-except-desktop">
        <svg id="user" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 31.136 31.136">
            <path id="Path_10" data-name="Path 10" d="M0,0H31.136V31.136H0Z" fill="none"/>
            <circle id="Ellipse_2" data-name="Ellipse 2" cx="5.5" cy="5.5" r="5.5" transform="translate(10 4)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <path id="Path_11" data-name="Path 11" d="M6,22.784V20.189A5.189,5.189,0,0,1,11.189,15h5.189a5.189,5.189,0,0,1,5.189,5.189v2.595" transform="translate(1.784 4.46)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
    </a>
<?php } else { ?>
    <a href="<?php echo $login_url ?>" aria-label="User panel" class="shkrn-menu-mobile-btn flex-center shkrn-except-desktop">
        <svg id="user" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 31.136 31.136">
            <path id="Path_10" data-name="Path 10" d="M0,0H31.136V31.136H0Z" fill="none"/>
            <circle id="Ellipse_2" data-name="Ellipse 2" cx="5.5" cy="5.5" r="5.5" transform="translate(10 4)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <path id="Path_11" data-name="Path 11" d="M6,22.784V20.189A5.189,5.189,0,0,1,11.189,15h5.189a5.189,5.189,0,0,1,5.189,5.189v2.595" transform="translate(1.784 4.46)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
    </a>
<?php }
} else { ?>
    <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" aria-label="User panel" class="shkrn-menu-mobile-btn flex-center shkrn-except-desktop">
        <svg id="user" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 31.136 31.136">
            <path id="Path_10" data-name="Path 10" d="M0,0H31.136V31.136H0Z" fill="none"/>
            <circle id="Ellipse_2" data-name="Ellipse 2" cx="5.5" cy="5.5" r="5.5" transform="translate(10 4)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
            <path id="Path_11" data-name="Path 11" d="M6,22.784V20.189A5.189,5.189,0,0,1,11.189,15h5.189a5.189,5.189,0,0,1,5.189,5.189v2.595" transform="translate(1.784 4.46)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
        </svg>
    </a>
<?php }
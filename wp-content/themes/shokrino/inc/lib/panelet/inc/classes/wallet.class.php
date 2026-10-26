<?php
defined('ABSPATH') || exit;
class Panelet_WALLET {
    public function __construct() {
        create_my_account_endpoint_pnlt_func('کیف پول', 'p-wallet', [$this, 'endpoint_content'], 5);
    }
    public function endpoint_content() {
        ?>
        <div id="panelet" class="wrap">
            <div class="panelet-settings-inner-wrap">
        <div class="tab">
            <button class="tablinks" onclick="openOptionsPanelet(event, 'Info')">
                <div class="panelet-tab-titles-box">
                    <div class="panelet-title-tab-options">
                        <?php echo "اطلاعات کیف پول"; ?>
                    </div>
                    <div class="panelet-desc-tab-options">
                        <?php echo "اطلاعات کلی کیف پول شما"; ?>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-square" width="30"
                     height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                    <rect x="4" y="4" width="16" height="16" rx="2" />
                    <polyline points="11 12 12 12 12 16 13 16" />
                </svg>
            </button>
            <button class="tablinks" onclick="openOptionsPanelet(event, 'Add')">
                <div class="panelet-tab-titles-box">
                    <div class="panelet-title-tab-options">
                        <?php echo "افزایش موجودی"; ?>
                    </div>
                    <div class="panelet-desc-tab-options">
                        <?php echo "موجودی کیف پول خود را افزایش دهید"; ?>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus" width="30"
                     height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9" />
                    <line x1="9" y1="12" x2="15" y2="12" />
                    <line x1="12" y1="9" x2="12" y2="15" />
                </svg>
            </button>
            <button class="tablinks" onclick="openOptionsPanelet(event, 'Transfer')">
                <div class="panelet-tab-titles-box">
                    <div class="panelet-title-tab-options">
                        <?php echo "انتقال موجودی"; ?>
                    </div>
                    <div class="panelet-desc-tab-options">
                        <?php echo "انتقال موجودی به دیگر کاربران سایت"; ?>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrows-sort" width="30"
                     height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 9l4 -4l4 4m-4 -4v14" />
                    <path d="M21 15l-4 4l-4 -4m4 4v-14" />
                </svg>
            </button>
            <button class="tablinks" onclick="openOptionsPanelet(event, 'Transactions')">
                <div class="panelet-tab-titles-box">
                    <div class="panelet-title-tab-options">
                        <?php echo "تاریخچه تراکنش ها"; ?>
                    </div>
                    <div class="panelet-desc-tab-options">
                        <?php echo "لیست تراکنش های کیف پول شما"; ?>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list-check" width="30"
                     height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3.5 5.5l1.5 1.5l2.5 -2.5" />
                    <path d="M3.5 11.5l1.5 1.5l2.5 -2.5" />
                    <path d="M3.5 17.5l1.5 1.5l2.5 -2.5" />
                    <line x1="11" y1="6" x2="20" y2="6" />
                    <line x1="11" y1="12" x2="20" y2="12" />
                    <line x1="11" y1="18" x2="20" y2="18" />
                </svg>
            </button>
        </div>
        <div id="save-options-panelet" class="panelet-tab-content-outer">
            <div id="Info" class="tabcontent">
                <div class="box-panelet-first-row-ballance">
                    <div class="box-panelet-wallet-ballance" style="padding: 0">
                        <div class="user-panel-avatar">
                            <?php
                            global $current_user;
                            wp_get_current_user();
                            $avatar_user = get_avatar($current_user->ID, 120);
                            if (!empty($avatar_user)) {
                                echo $avatar_user;
                            } else { ?>
                                <img width="100%" height="100%" src="<?php echo PNLT_ASSETS . '/img/user.jpg' ?>">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-panelet-wallet-ballance">
                        <div class="box-icon-woo-wallet">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6ab04c" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"></path>
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"></path>
                            </svg>
                        </div>
                        <div class="box-balance-woo-wallet">
                            <h3><?php echo "موجودی فعلی:"; ?></h3>
                            <span><?php echo woo_wallet()->wallet->get_wallet_balance( get_current_user_id() ); ?></span>
                        </div>
                        <button class="box-add-balance-woo-wallet" onclick="openOptionsPanelet(event, 'Add')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus" width="35"
                                 height="35" viewBox="0 0 24 24" stroke-width="1.5" stroke="#00c60c" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9" />
                                <line x1="9" y1="12" x2="15" y2="12" />
                                <line x1="12" y1="9" x2="12" y2="15" />
                            </svg>
                        </button>
                    </div>
                    <div class="box-panelet-wallet-ballance">
                        <button class="box-add-balance-woo-wallet" onclick="openOptionsPanelet(event, 'Transactions')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <polyline points="12 8 12 12 14 14" />
                                <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div  class="field-detail box-panelet-second-row-ballance">
                    <?php
                    $current_user_id = get_current_user_id();
                    $user_info = get_userdata($current_user_id);
                    $display_name = $user_info->display_name;
                    $user_email = $user_info->user_email;
                    $first_name = $user_info->first_name;
                    $last_name = $user_info->last_name;
                    ?>
                    <div class="box-panelet-info-title half-width-panelet">
                        <span class="your-display-name-panelet"><?php echo "نام نمایشی شما"; ?></span>
                        <input class="input-text" type="text" value="<?php echo $display_name; ?>" disabled>
                    </div>
                    <div class="box-panelet-info-title half-width-panelet">
                        <span class="your-user-id-panelet"><?php echo "شناسه کاربری شما"; ?></span>
                        <input class="input-text" type="text" value="<?php echo $current_user_id; ?>" disabled>
                    </div>
                    <div class="box-panelet-info-title">
                        <span class="your-email-panelet"><?php echo "ایمیل ثبت شده شما"; ?></span>
                        <input class="input-text" type="email" value="<?php echo $user_email; ?>" disabled>
                    </div>
                </div>
                <div class="box-panelet-second-row-ballance">
                    <a class="btn-edit-profile-panelet-wallet" href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>">
                        <?php echo "ویرایش پروفایل"; ?>
                    </a>
                </div>
            </div>

            <div id="Add" class="tabcontent">
                <form class="field-detail" method="post" action="">
                    <div class="woo-wallet-add-amount">
                        <label for="woo_wallet_balance_to_add"><?php echo "مقدار را وارد کنید"; ?></label>
                        <p><?php echo "مقدار اعتبار مورد نظر که میخواهید به کیف پول خود اضافه کنید ، وارد کنید و سپس کلید (افزودن) را بزنید تا به صفحه پرداخت منتقل شوید."; ?></p>
                        <?php
                        $min_amount = woo_wallet()->settings_api->get_option( 'min_topup_amount', '_wallet_settings_general', 0 );
                        $max_amount = woo_wallet()->settings_api->get_option( 'max_topup_amount', '_wallet_settings_general', '' );
                        ?>
                        <input class="input-text" type="number" step="0.01" min="<?php echo $min_amount; ?>" max="<?php echo $max_amount; ?>" name="woo_wallet_balance_to_add" id="woo_wallet_balance_to_add" class="woo-wallet-balance-to-add" required="" />
                        <?php wp_nonce_field( 'woo_wallet_topup', 'woo_wallet_topup' ); ?>
                        <input type="submit" name="woo_add_to_wallet" class="woo-add-to-wallet" value="<?php echo "افزودن"; ?>" />
                    </div>
                </form>
            </div>

            <div id="Transfer" class="tabcontent">
                <form class="field-detail" method="post" action="" id="woo_wallet_transfer_form">
                    <p class="woo-wallet-field-container form-row form-row-wide">
                        <label for="woo_wallet_transfer_user_id"><?php echo "فردی را که میخواهید به آن موجودی منتقل شود ، انتخاب کنید (جستجو با ایمیل)"; ?> <?php
                            if ( apply_filters( 'woo_wallet_user_search_exact_match', true ) ) {
                                echo "فردی را که میخواهید به آن موجودی منتقل شود ، انتخاب کنید (جستجو با ایمیل)";
                            }
                            ?></label>
                        <select name="woo_wallet_transfer_user_id" class="woo-wallet-select2" required=""></select>
                    </p>
                    <p class="woo-wallet-field-container form-row form-row-wide">
                        <label for="woo_wallet_transfer_amount"><?php echo "مقدار:"; ?></label>
                        <input class="input-text" type="number" step="0.01" min="<?php echo woo_wallet()->settings_api->get_option('min_transfer_amount', '_wallet_settings_general', 0); ?>" name="woo_wallet_transfer_amount" required=""/>
                    </p>
                    <p class="woo-wallet-field-container form-row form-row-wide">
                        <label for="woo_wallet_transfer_note"><?php echo "توضیحات انتقال:"; ?></label>
                        <textarea class="input-text" name="woo_wallet_transfer_note"></textarea>
                    </p>
                    <p class="woo-wallet-field-container form-row">
                        <?php wp_nonce_field( 'woo_wallet_transfer', 'woo_wallet_transfer' ); ?>
                        <input type="submit" class="btn-panel button" name="woo_wallet_transfer_fund" value="<?php echo "اقدام به انتقال موجودی"; ?>" />
                    </p>
                </form>
            </div>

            <div id="Transactions" class="tabcontent">
                <?php $transactions = get_wallet_transactions( array( 'limit' => apply_filters( 'woo_wallet_transactions_count', 10 ) ) ); ?>
                <?php if ( ! empty( $transactions ) ) { ?>
                    <ul class="woo-wallet-transactions-items">
                        <?php foreach ( $transactions as $transaction ) : ?>
                            <li>
                                <div>
                                    <p><?php echo $transaction->details; ?></p>
                                    <small><?php echo wc_string_to_datetime( $transaction->date )->date_i18n( wc_date_format() ); ?></small>
                                </div>
                                <div class="woo-wallet-transaction-type-<?php echo $transaction->type; ?>"><?php
                                    echo $transaction->type == 'credit' ? '+' : '-';
                                    echo wc_price( apply_filters( 'woo_wallet_amount', $transaction->amount, $transaction->currency, $transaction->user_id ), woo_wallet_wc_price_args($transaction->user_id) );
                                    ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php
                } else {
                    echo "تابحال هیچ انتقال موجودی انجام نشده است";
                }
                ?>
            </div>
            <script>
                var tabContents = document.querySelectorAll('.tabcontent');
                var tabLinks = document.querySelectorAll('.tablinks');

                tabContents[0].style.display = 'block';
                tabLinks[0].classList.add('active');

                function openOptionsPanelet(evt, tabName) {
                    for (var i = 0; i < tabContents.length; i++) {
                        tabContents[i].style.display = 'none';
                    }

                    for (var i = 0; i < tabLinks.length; i++) {
                        tabLinks[i].classList.remove('active');
                    }

                    var currentTab = document.getElementById(tabName);
                    currentTab.style.display = 'block';
                    evt.currentTarget.classList.add('active');
                }
            </script>
        </div>
        <style>
            #panelet {
                width: 100%;
            }
            /* TABS STYLES */
            #panelet .tab {
                float: right;
                width: 30%;
                height: max-content;
            }
            @media screen and (max-width: 768px) {
                #panelet .tab {
                    width: 100%;
                }
                #panelet .tabcontent {
                    padding: 10px !important;
                    border: 2px solid var(--var-pnlt-main-color);
                    border-radius: 10px !important;
                    margin-top: 15px;
                    border-right-width: 2px !important;
                }
            }
            #panelet .tab button {
                display: flex;
                background-color: transparent;
                color: black;
                padding: 25px 20px 25px 14px;
                width: 100%;
                border: none;
                outline: none;
                text-align: right;
                cursor: pointer;
                transition: 0s;
                justify-content: space-between;
                flex-direction: row;
                align-items: center;
                border-right: 3px solid transparent;
                border-top: 1px solid transparent;
                border-bottom: 1px solid transparent;
                column-gap: 5px;
                border-radius: 0;
            }
            #panelet .tab button:hover {
                border-right: 3px solid var(--var-pnlt-main-color);
                border-top: 1px solid #eee;
                border-bottom: 1px solid #eee;
            }
            #panelet .tab button.active {
                border-right: 3px solid var(--var-pnlt-main-color);
                transition: 200ms;
                border-top: 1px solid #eee;
                border-bottom: 1px solid #eee;
                box-shadow: -5px 3px 11px -2px #36454f0d;
                border-radius: 0;
            }
            #panelet .tab button.active svg, .tab button:hover svg {
                stroke: var(--var-pnlt-main-color);
            }
            #panelet .tabcontent {
                float: right;
                padding: 5px 20px;
                border-right: 3px solid var(--var-pnlt-main-color);
                width: 100%;
                height: 100%;
                border-radius: 20px 0 0 20px;
            }
            #panelet .tabcontent:not(:first-child) {
                display: none;
            }
            /* TABS STYLES */
            .panelet-settings-inner-wrap {
                display: flex;
                flex-wrap: wrap;
                width: 100%;
                padding: 20px;
                background: #fff;
                border-radius: 25px;
                box-shadow: 0 4px 16px -2px rgba(36, 64, 147, 0.1);
                min-height: 100%;
            }
            .panelet-information {
                display: flex;
                justify-content: center;
                flex-direction: column;
                align-items: center;
                padding: 25px;
                row-gap: 10px;
            }
            .panelet-information > img {
                width: 160px;
                height: auto;
            }
            .panelet-tab-titles-box {
                display: flex;
                justify-content: right;
                align-items: flex-start;
                flex-direction: column;
            }
            .panelet-title-tab-options {
                font-size: 17px;
                font-weight: 800;
            }
            .panelet-desc-tab-options {
                font-size: 12px;
                font-weight: 500;
            }
            .panelet-tab-content-outer {
                float: right;
                min-height: 100%;
                display: flex;
                flex: 1;
                flex-direction: column;
                row-gap: 15px;
            }
            .panelet-options-save-box {
                display: flex;
                justify-content: end;
                padding: 0 3%;
            }
            .panelet-options-save-box .submit-panelet {
                display: flex;
                background: var(--var-pnlt-main-color);
                border: 2px solid transparent;
                border-radius: 9px;
                padding: 8px 20px;
                color: white;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            }
            .panelet-options-save-box .submit-panelet:hover {
                display: flex;
                background: transparent;
                border: 2px solid var(--var-pnlt-main-color);
                border-radius: 9px;
                padding: 4px 20px;
                color: var(--var-pnlt-main-color);
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            }
            .panelet-tab-content-outer .icon {
                stroke: var(--var-pnlt-main-color);
            }
            /* OPTIONS STYLES */
            .panelet-box-option {
                display: flex;
                flex-direction: column;
                row-gap: 5px;
            }
            .panelet-box-option h3 {
                display: flex;
                justify-content: center;
                align-items: center;
                color: white;
                background: var(--var-pnlt-main-color);
                padding: 15px;
                border-radius: 11px;
            }
            .panelet-box-option p {
                margin-top: 0;
            }
            .box-panelet-wallet-ballance {
                display: flex;
                justify-content: center;
                border: 1px solid #eaeaea;
                padding: 20px 25px;
                border-radius: 15px;
                flex-wrap: wrap;
                column-gap: 15px;
                align-items: center;
                width: fit-content;
                overflow: hidden;
            }
            .box-panelet-wallet-ballance .user-panel-avatar img {
                border-radius: unset;
            }
            .box-panelet-first-row-ballance {
                display: flex;
                justify-content: center;
                column-gap: 20px;
                row-gap: 10px;
                flex-wrap: wrap;
            }
            .box-panelet-wallet-ballance .woocommerce-Price-amount.amount {
                font-size: 17px;
            }
            .btn-edit-profile-panelet-wallet {
                background: var(--var-pnlt-main-color);
                color: #fff !important;
                display: flex;
                width: 100%;
                justify-content: center;
                align-items: center;
                padding: 18px;
                font-size: 16px;
                column-gap: 5px;
                border-radius: 12px;
                margin-top: 25px;
                font-weight: 700;
            }
            .btn-edit-profile-panelet-wallet svg {
                stroke: #fff;
            }
            .box-panelet-second-row-ballance {
                margin-top: 15px;
            }
            .box-panelet-wallet-ballance .box-add-balance-woo-wallet {
                background: none;
                padding: 0;
                margin: 0;
                border: none;
            }
            .box-panelet-wallet-ballance .box-icon-woo-wallet {
                margin: 0;
            }
            .your-display-name-panelet + input, .your-user-id-panelet + input, .your-email-panelet + input,
            #woo_wallet_balance_to_add {
                text-align: center;
                margin: 5px;
            }
            @media screen and (max-width: 768px) {
                .box-panelet-wallet-ballance {
                    padding: 8px 10px;
                }
            }
            .woo-add-to-wallet {
                background: var(--var-pnlt-main-color) !important;
                color: #fff !important;
                display: flex !important;
                width: 100%;
                justify-content: center;
                align-items: center;
                column-gap: 5px;
                border-radius: 12px !important;
                margin-top: 22px !important;
                font-weight: 800 !important;
                text-decoration: none !important;
                min-height: 40px;
                font-size: 16px;
            }
            .woo-wallet-field-container .woo-wallet-select2.select2-hidden-accessible, .woo-wallet-field-container .select2.select2-container.select2-container--default {
                width: 100% !important;
            }
            .woo-wallet-transactions-items > li > div:not(.woo-wallet-transaction-type-credit) {
                display: flex;
                flex-wrap: wrap;
                column-gap: 15px;
                justify-content: center;
                align-items: center;
            }
            .woo-wallet-transactions-items > li > div:not(.woo-wallet-transaction-type-credit) > p {
                margin: 0;
            }
        </style>

            </div>
        </div>
        <?php
    }
}
new Panelet_WALLET;
<?php defined('ABSPATH') || exit;
include_once 'optionino-require.php'
?>

<div id="optionino" class="wrap">
    <h2><?php echo __('Woocommerce User Panel','shokrino'); ?></h2>
    <div class="optionino-settings-inner-wrap">
        <div class="tab">
            <div class="optionino-information">
                <h3>تنظیمات ویژگی ها</h3>
                <span><?php echo __('Version: ', 'shokrino'); ?><strong><?php echo "1.0.0"; ?></strong></span>
            </div>
            <button class="tablinks" onclick="openOptionsoptionino(event, 'Panelet')">
                <div class="optionino-tab-titles-box">
                    <div class="optionino-title-tab-options">
                        <?php echo __('Panelet', 'shokrino'); ?>
                    </div>
                    <div class="optionino-desc-tab-options">
                        <?php echo __('basic settings for user panel', 'shokrino'); ?>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-horizontal" width="30"
                     height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="14" cy="6" r="2"/>
                    <line x1="4" y1="6" x2="12" y2="6"/>
                    <line x1="16" y1="6" x2="20" y2="6"/>
                    <circle cx="8" cy="12" r="2"/>
                    <line x1="4" y1="12" x2="6" y2="12"/>
                    <line x1="10" y1="12" x2="20" y2="12"/>
                    <circle cx="17" cy="18" r="2"/>
                    <line x1="4" y1="18" x2="15" y2="18"/>
                    <line x1="19" y1="18" x2="20" y2="18"/>
                </svg>
            </button>
            <button class="tablinks" onclick="openOptionsoptionino(event, 'Admino')">
                <div class="optionino-tab-titles-box">
                    <div class="optionino-title-tab-options">
                        <?php echo __('Admino', 'shokrino'); ?>
                    </div>
                    <div class="optionino-desc-tab-options">
                        <?php echo __('basic settings for admin dashboard styles', 'shokrino'); ?>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-horizontal" width="30"
                     height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="14" cy="6" r="2"/>
                    <line x1="4" y1="6" x2="12" y2="6"/>
                    <line x1="16" y1="6" x2="20" y2="6"/>
                    <circle cx="8" cy="12" r="2"/>
                    <line x1="4" y1="12" x2="6" y2="12"/>
                    <line x1="10" y1="12" x2="20" y2="12"/>
                    <circle cx="17" cy="18" r="2"/>
                    <line x1="4" y1="18" x2="15" y2="18"/>
                    <line x1="19" y1="18" x2="20" y2="18"/>
                </svg>
            </button>
        </div>
        <form action="" method="POST" id="save-options-optionino" class="optionino-tab-content-outer">

            <div id="Panelet" class="tabcontent">
                <div class="optionino-box-option">
                    <label class="optionino-form-label"
                           for="optionino-panelet-on"><?php echo __('Enable User Panel', 'shokrino') ?></label>
                    <label class="switch">
                        <input id="optionino-panelet-on" name="panelet_on" type="checkbox" onchange="hideOtherPaneletOptions()" <?php if($optionino_options['panelet_on'] == true) { echo 'checked'; } ?> autocomplete="off">
                        <span class="slider round"></span>
                    </label>
                    <p><?php echo __('if you set this option OFF, Shokrino custum user panel will be disable.', 'shokrino') ?></p>
                    <script>
                        function hideOtherPaneletOptions()
                        {
                            if($('input#optionino-panelet-on').is(":checked"))
                                $(".optionino-is-off").css({display:"flex"});
                            else
                                $(".optionino-is-off").hide();
                        }
                    </script>
                    <style>
                        <?php if (get_option('optionino_options')['panelet_on'] == false) { ?>
                        .optionino-is-off {
                            display: none;
                        }
                        <?php } ?>
                    </style>
                </div>
                <div class="optionino-box-option optionino-is-off">
                    <label class="optionino-form-label"
                           for="optionino-panelet-avatar"><?php echo __('Enable Avatar System', 'shokrino') ?></label>
                    <label class="switch">
                        <input id="optionino-panelet-avatar" name="panelet_avatar" type="checkbox" <?php if($optionino_options['panelet_avatar'] == true) { echo 'checked'; } ?> autocomplete="off">
                        <span class="slider round"></span>
                    </label>
                    <p><?php echo __('if you set this option OFF, Shokrino custom avatar system will be disable.', 'shokrino') ?></p>
                </div>
                <div class="optionino-box-option optionino-is-off">
                    <label class="optionino-form-label"
                           for="optionino-panelet-wallet"><?php echo __('Enable Wallet System', 'shokrino') ?></label>
                    <label class="switch">
                        <input id="optionino-panelet-wallet" name="panelet_wallet" type="checkbox" <?php if($optionino_options['panelet_wallet'] == true) { echo 'checked'; } ?> autocomplete="off">
                        <span class="slider round"></span>
                    </label>
                    <p><?php echo __('if you set this option OFF, Shokrino custom wallet system will be disable.', 'shokrino') ?></p>
                </div>
                <div class="optionino-box-option optionino-is-off">
                    <label class="optionino-form-label"
                           for="optionino-panelet-walletdashboard"><?php echo __('Wallet on dashboard', 'shokrino') ?></label>
                    <label class="switch">
                        <input id="optionino-panelet-walletdashboard" name="panelet_walletdashboard" type="checkbox" <?php if($optionino_options['panelet_walletdashboard'] == true) { echo 'checked'; } ?> autocomplete="off">
                        <span class="slider round"></span>
                    </label>
                    <p><?php echo __('this option controls the wallet info on user panel dashboard.', 'shokrino') ?></p>
                </div>
            </div>

            <div id="Admino" class="tabcontent">
                <div class="optionino-box-option">
                    <label class="optionino-form-label"
                           for="optionino-admino-on"><?php echo __('Enable Admin Styles', 'shokrino') ?></label>
                    <label class="switch">
                        <input id="optionino-admino-on" name="admino_on" type="checkbox" <?php if($optionino_options['admino_on'] == true) { echo 'checked'; } ?> autocomplete="off">
                        <span class="slider round"></span>
                    </label>
                    <p><?php echo __('if you set this option OFF, Shokrino custum admin dashboard styles will be disable.', 'shokrino') ?></p>
                </div>
            </div>

            <div class="optionino-options-save-box">
                <button type="submit" class="submit-optionino" name="save-optionino"><?php echo __('save changes', 'shokrino'); ?></button>
            </div>
        </form>

        <script>
            document.querySelectorAll('.tabcontent')[0].style.display = "block";
            document.querySelectorAll('.tablinks')[0].classList.add("active");

            function openOptionsoptionino(evt, cityName) {
                // Declare all variables
                var i, tabcontent, tablinks;

                // Get all elements with class="tabcontent" and hide them
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }

                // Get all elements with class="tablinks" and remove the class "active"
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }

                // Show the current tab, and add an "active" class to the link that opened the tab
                document.getElementById(cityName).style.display = "block";
                evt.currentTarget.className += " active";
            }
            jQuery(function () {

            });
        </script>
    </div>
</div>



<?php
defined( 'ABSPATH' ) || exit;

global $shokrino_options;
$favicon = $shokrino_options['favicon-option']['url'];
$logo = $shokrino_options['logo-option']['url'];
$maintenace_mode_title = $shokrino_options['title-page-maintenace-mode'];
$maintenace_mode_heading = $shokrino_options['heading-maintenace-mode'];
$maintenace_mode_text = $shokrino_options['text-maintenace-mode'];
$maintenace_bg_color = $shokrino_options['offline-bg-color'];
$maintenace_box_bg_color = $shokrino_options['offline-box-bg-color'];
$maintenace_color = $shokrino_options['offline-txt-bg-color'];
$maintenace_date_box_bg_color = $shokrino_options['offline-count-down-bg-color'];
$maintenace_date_box_txt_color = $shokrino_options['offline-count-down-txt-color'];
$maintenace_date = $shokrino_options['maintenace-mode-date'];
$maintenace_time = $shokrino_options['maintenace-mode-time'];
$maintenace_date_show = $shokrino_options['maintenace-mode-date-show'];
$maintenace_logo_show = $shokrino_options['maintenace-logo-show'];
if (!current_user_can('edit_themes') && !is_admin()) {
    if (!empty($favicon)) { ?>
        <link rel="icon" type="image/png" href="<?php echo $favicon; ?>">
        <?php } else { ?>
        <link rel="icon" type="image/png" href="<?php echo SHKRN_ASSETS . '/img/favicon.png' ?>">
        <?php } ?>
    <div class="box-offline">
        <title><?php echo $maintenace_mode_title; ?></title>
    <?php if ($maintenace_logo_show) { ?>
        <div class="logo">
            <?php if (!empty($logo)) { ?>
                <figure>
                    <img src="<?php echo $logo ?>" alt="logo">
                </figure>
                <?php } else { ?>
                <figure>
                    <img src="<?php echo SHKRN_ASSETS . '/img/logo.png' ?>" alt="logo">
                </figure>
            <?php } ?>
        </div>
    <?php } ?>
        <h1><?php echo $maintenace_mode_heading; ?></h1>
        <p><?php echo $maintenace_mode_text; ?></p>
        <?php if ($maintenace_date_show) { ?>
        <div class="count-down-offline">
            <div id="clockdiv" data-animation="animated flipInY">
                <div><span class="days"></span>
                    <div class="smalltext">روز</div>
                </div>
                <div><span class="hours"></span>
                    <div class="smalltext">ساعت</div>
                </div>
                <div><span class="minutes"></span>
                    <div class="smalltext">دقیقه</div>
                </div>
                <div><span class="seconds"></span>
                    <div class="smalltext">ثانیه</div>
                </div>
            </div>
            <script>
                var deadline = '<?php echo $maintenace_date; ?>  <?php echo $maintenace_time; ?> GMT+0430';
                function time_remaining(endtime){
                    var t = Date.parse(endtime) - Date.parse(new Date());
                    var seconds = Math.floor( (t/1000) % 60 );
                    var minutes = Math.floor( (t/1000/60) % 60 );
                    var hours = Math.floor( (t/(1000*60*60)) % 24 );
                    var days = Math.floor( t/(1000*60*60*24) );
                    return {'total':t, 'days':days, 'hours':hours, 'minutes':minutes, 'seconds':seconds};
                }
                function run_clock(id,endtime){
                    var clock = document.getElementById(id);

                    // get spans where our clock numbers are held
                    var days_span = clock.querySelector('.days');
                    var hours_span = clock.querySelector('.hours');
                    var minutes_span = clock.querySelector('.minutes');
                    var seconds_span = clock.querySelector('.seconds');

                    function update_clock(){
                        var t = time_remaining(endtime);

                        // update the numbers in each part of the clock
                        days_span.innerHTML = t.days;
                        hours_span.innerHTML = ('0' + t.hours).slice(-2);
                        minutes_span.innerHTML = ('0' + t.minutes).slice(-2);
                        seconds_span.innerHTML = ('0' + t.seconds).slice(-2);

                        if(t.total<=0){ clearInterval(timeinterval); document.getElementById("clockdiv").innerHTML = "<h3>زمان به اتمام رسید</h3>"; }
                    }
                    update_clock();
                    var timeinterval = setInterval(update_clock,1000);
                }
                run_clock('clockdiv',deadline);
            </script>
        </div>
        <?php }
        if ($maintenace_logo_show && !$maintenace_date_show) { ?>
            <style>
                .box-offline p {
                    margin-bottom: 20px !important;
                }
            </style>
        <?php }
        if (!$maintenace_logo_show && $maintenace_date_show) { ?>
            <style>
                .box-offline h1 {
                    margin-top: 20px !important;
                }
            </style>
        <?php } ?>
        <style>
            body {
                border: none;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                max-width: none;
                box-shadow: none !important;
                float: right;
                display: flex;
                justify-content: center;
                font-family: YekanBakh !important;
                flex-direction: column;
                align-items: center;
                flex-wrap: wrap;
                background: <?php if (!empty($maintenace_bg_color)) { echo $maintenace_bg_color; } else { ?> #0096ff <?php } ?> !important;
                color: <?php if (!empty($maintenace_color)) { echo $maintenace_color; } else { ?> #294559 <?php } ?> !important;
            }
            .box-offline {
                background: <?php if (!empty($maintenace_box_bg_color)) { echo $maintenace_box_bg_color; } else { ?> #ffffff <?php } ?> !important;
                border-radius: 25px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 420px;
                max-width: 100%;
                min-height: 110px;
            }
            .box-offline .logo figure {
                margin-bottom: 5px;
            }
            .box-offline .logo {
                display: block;
                margin: auto;
            }
            .box-offline h1 {
                margin: 0 auto;
                font-weight: 800;
                font-size: 25px;
                margin-top: 2px;
                color: <?php if (!empty($maintenace_color)) { echo $maintenace_color; } else { ?> #294559 <?php } ?> !important;
                text-align: center;
                padding: 0 9px;
            }
            .box-offline p {
                margin: 1px auto;
                color: <?php if (!empty($maintenace_color)) { echo $maintenace_color; } else { ?> #294559 <?php } ?> !important;
                text-align: center;
                padding: 0 9px;
                font-weight: 600;
            }
            #clockdiv {
                display: inline-block;
                text-align: center;
                font-size: 14px;
                padding: 12px 20px;
                text-transform: uppercase;
                width: 360px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
                background: <?php if (!empty($maintenace_date_box_bg_color)) { echo $maintenace_date_box_bg_color; } else { ?> #0096ff <?php } ?> !important;
                margin: 7px auto 25px;
                width: fit-content;
                min-height: 50px;
                min-width: 220px;
            }
            #clockdiv > div, #clockdiv2 > div, #clockdiv3 > div {
                display: inline-block;
                margin: 10px;
                color: <?php if (!empty($maintenace_date_box_txt_color)) { echo $maintenace_date_box_txt_color; } else { ?> #ffffff <?php } ?> !important;
                font-weight: 400;
                line-height: 12px;
            }
            #clockdiv div > span, #clockdiv2 div > span, #clockdiv3 div > span {
                float: left;
                width: 100%;
                color: <?php if (!empty($maintenace_date_box_txt_color)) { echo $maintenace_date_box_txt_color; } else { ?> #ffffff <?php } ?> !important;
                font-size: 30px;
                font-weight: 600;
                padding-bottom: 5px;
                line-height: 17px;
            }
            #clockdiv > h3 {
                min-width: 220px;
                display: block;
                font-size: 18px;
                line-height: 15px;
                color: white;
            }
            @font-face {
                font-display: swap;
                font-family: 'YekanBakh';
                src: url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Light.eot'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Light.eot?#iefix') format("embedded-opentype"),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Light.woff') format('woff'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Light.ttf') format('truetype');
                font-weight: 400;
            }
            @font-face {
                font-display: swap;
                font-family: 'YekanBakh';
                src: url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Medium.eot'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Medium.eot?#iefix') format("embedded-opentype"),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Medium.woff') format('woff'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Medium.ttf') format('truetype');
                font-weight: 500;
            }
            @font-face {
                font-display: swap;
                font-family: 'YekanBakh';
                src: url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Bold.eot'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Bold.eot?#iefix') format("embedded-opentype"),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Bold.woff') format('woff'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Bold.ttf') format('truetype');
                font-weight: 600;
            }
            @font-face {
                font-display: swap;
                font-family: 'YekanBakh';
                src: url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakh-Heavyer.eot'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakh-Heavyer.eot?#iefix') format("embedded-opentype"),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakh-Heavyer.woff') format('woff'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakh-Heavyer.ttf') format('truetype');
                font-weight: 800;
            }
            @font-face {
                font-display: swap;
                font-family: 'YekanBakh';
                src: url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Fat.eot'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Fat.eot?#iefix') format("embedded-opentype"),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Fat.woff') format('woff'),
                url('<?php echo SHKRN_ASSETS; ?>/css/fonts/yekanbakh/YekanBakhFaNum-Fat.ttf') format('truetype');
                font-weight: 900;
            }
        </style>
    </div> <?php
    exit;
}

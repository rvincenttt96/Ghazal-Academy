<?php $optionino_options = get_option('optionino_options'); ?>
    <script>
        var $ = jQuery;
        $(document).ready(function(){$(".submit-optionino").click(function(){$(".lite-box-spinner-saving").css({display:"flex"})})})
    </script>
    <div class="lite-box-spinner-saving">
        <svg aria-hidden="true" focusable="false" class="loader loading" viewBox="0 0 38 38" width="100px" height="100px">
            <g transform="translate(2 2)" stroke="currentColor" stroke-width="4" fill-rule="evenodd" style="fill: #0096ff;stroke: #e6e6e6;">
                <circle class="st0" stroke-opacity=".5" cx="17" cy="17" r="16.5"></circle>
                <path class="st1" d="M33.5 17C33.5 7.9 26.1.5 17 .5" transform="rotate(249.767 17 17)">
                    <animateTransform accumulate="none" additive="replace" attributeName="transform" calcMode="linear" dur="1s" from="0 17 17" repeatCount="indefinite" restart="always" to="360 17 17" type="rotate"></animateTransform>
                </path>
            </g>
        </svg>
        <svg id="Layer_1" class="kalamatino-icon-loader" width="50" height="50" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1917.44 1917.44" style="enable-background:new 0 0 1917.44 1917.44;" xml:space="preserve">
            <g>
                <path class="st0" d="M929.21,1751c-52.2,0-103.12-10.13-151.35-30.1c-48.23-19.98-91.4-48.82-128.31-85.73l-116.54-116.54   c-92.86-92.86-92.86-243.41,0-336.27l0,0l299.11,299.11c3.97,3.97,8.5,7.32,13.45,9.97c69.91,37.49,145.94,24.81,195.16-24.41   l3.88-3.88c61.5-61.5,61.5-161.56,0-223.05l-90.18-90.18l168.13-168.13l90.18,90.18c37.72,37.72,66.95,81.76,86.87,130.89   c19.22,47.4,28.97,97.45,28.97,148.77c0,51.31-9.75,101.36-28.97,148.77c-19.93,49.14-49.15,93.18-86.87,130.89l-11.44,11.44   c-11.71,11.71-24.3,22.52-37.81,32.1C1092.48,1725.23,1012.47,1751,929.21,1751z"/>
            </g>
            <g>
                <path class="st0" d="M735.86,936.27l-90.18-90.18c-37.72-37.72-66.94-81.76-86.87-130.89c-19.22-47.4-28.97-97.45-28.97-148.77   s9.75-101.36,28.97-148.76c19.93-49.14,49.15-93.18,86.87-130.89l3.88-3.88c36.91-36.91,80.08-65.76,128.31-85.73   c48.23-19.98,99.15-30.11,151.35-30.11c52.2,0,103.12,10.13,151.35,30.11c48.23,19.98,91.4,48.82,128.31,85.73l109.81,109.81   c92.86,92.86,92.86,243.41,0,336.27l0,0l-277.94-277.94c-29.79-29.79-69.4-46.2-111.52-46.19c-42.13,0-81.74,16.41-111.52,46.2   l-3.88,3.88c-61.5,61.49-61.5,161.55,0,223.05l90.18,90.18L735.86,936.27z"/>
            </g>
            <path class="st0" d="M1407.66,876.74L1011.7,480.79c-46.43-46.43-121.7-46.43-168.13,0v0c-46.43,46.43-46.43,121.7,0,168.13  l113.34,113.34L731.55,987.62L619.32,875.39c-46.43-46.43-121.7-46.43-168.13,0v0c-46.43,46.43-46.43,121.7,0,168.13l395.95,395.95  c46.43,46.43,121.7,46.43,168.13,0l0,0c46.43-46.43,46.43-121.7,0-168.13l-115.58-115.58l225.36-225.36l114.48,114.48  c46.43,46.43,121.7,46.43,168.13,0l0,0C1454.09,998.44,1454.09,923.17,1407.66,876.74z"/>
        </svg>
    </div>

<?php
if (isset($_POST['save-optionino'])) {
    $optionino_options = get_option('optionino_options');

    // Panelet Woocommerce UserPanel Options
    if ($_POST['panelet_on'] == "on") { $panelet_on = true; } else { $panelet_on = false; }
    if ($_POST['panelet_avatar'] == "on") { $panelet_avatar = true; } else { $panelet_avatar = false; }
    if ($_POST['panelet_wallet'] == "on") { $panelet_wallet = true; } else { $panelet_wallet = false; }
    if ($_POST['panelet_homebtn'] == "on") { $panelet_homebtn = true; } else { $panelet_homebtn = false; }
    if ($_POST['panelet_shopbtn'] == "on") { $panelet_shopbtn = true; } else { $panelet_shopbtn = false; }
    if ($_POST['panelet_blogbtn'] == "on") { $panelet_blogbtn = true; } else { $panelet_blogbtn = false; }
    if ($_POST['panelet_cartbtn'] == "on") { $panelet_cartbtn = true; } else { $panelet_cartbtn = false; }
    if ($_POST['panelet_logoutbtn'] == "on") { $panelet_logoutbtn = true; } else { $panelet_logoutbtn = false; }
    if ($_POST['panelet_profile_detail'] == "on") { $panelet_profile_detail = true; } else { $panelet_profile_detail = false; }
    if ($_POST['panelet_walletdashboard'] == "on") { $panelet_walletdashboard = $panelet_wallet ? true : false; } else { $panelet_walletdashboard = false; }

    // Admino Dashboard Styles Options
    if ($_POST['admino_on'] == "on") { $admino_on = true; } else { $admino_on = false; }

    $optionino_options = array(
        // Panelet Woocommerce UserPanel Options
        'panelet_on' => $panelet_on,
        'panelet_avatar' => $panelet_avatar,
        'panelet_wallet' => $panelet_wallet,
        'panelet_homebtn' => $panelet_homebtn,
        'panelet_shopbtn' => $panelet_shopbtn,
        'panelet_blogbtn' => $panelet_blogbtn,
        'panelet_cartbtn' => $panelet_cartbtn,
        'panelet_logoutbtn' => $panelet_logoutbtn,
        'panelet_profile_detail' => $panelet_profile_detail,
        'panelet_walletdashboard' => $panelet_walletdashboard,

        // Admino Dashboard Styles Options
        'admino_on' => $admino_on,
    );
    update_option('optionino_options',$optionino_options);
    ?>
    <style>
        .lite-box-spinner-saving {
            display: flex !important;
        }
    </style>
    <?php
    echo "<meta http-equiv='refresh' content='0'>";
} ?>
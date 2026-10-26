<?php
defined( 'ABSPATH' ) || exit;

$header_style = shkrn_options('header_style');
$show_btn_notifbar = shkrn_options('notification-bar-btn-show');
$btn_notifbar_txt = shkrn_options('notification-bar-btn-txt');
$btn_notifbar_link = shkrn_options('notification-bar-btn-link');
$show_countdown = shkrn_options('notification-bar-countdown-show');
$countdown_date = shkrn_options('notif-bar-date');
$countdown_time = shkrn_options('notif-bar-time');
$text_notifbar = shkrn_options('text-notif-bar');
$text_notifbar_have_link = shkrn_options('text-notif-bar-have-link');
$text_notifbar_link = shkrn_options('text-notif-bar-link');
$notifbar_bg_color = shkrn_options('notif-bar-bg-color');
?>
<div id="shokrino-top-notif-bar" class="header-notif-bar-shokrino header-footer-container-shkrn">
    <div class="close-btn-js">
        <div class="close-notif-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="15" height="15"
                 viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round"
                 stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </div>
    </div>
    <div class="text-notif-bar">
        <a <?php if ($text_notifbar_have_link && !empty($text_notifbar_link)) { ?>href="<?php echo $text_notifbar_link; ?>"<?php } ?>>
            <?php echo $text_notifbar; ?>
        </a>
    </div>
    <?php if ($show_btn_notifbar) { ?>
        <div class="btn-notif-bar">
            <a href="<?php echo $btn_notifbar_link; ?>" class="btn-notifbar-calltoaction">
                <?php echo $btn_notifbar_txt; ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="16"
                     height="16" viewBox="0 0 24 24" stroke-width="3" stroke="#294559" fill="none"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>
            </a>
        </div>
    <?php }
    if ($show_countdown) { ?>
        <div class="timer-notif-bar">
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

            <style>
                #clockdiv {
                    display: inline-flex;
                    text-align: center;
                    font-size: 9px;
                    padding: 10px 4px 3px;
                    border-radius: 10px;
                    background: #e4f4ff38;
                    justify-content: center;
                    margin: 6.5px auto 5.5px 20px;
                    flex-direction: row-reverse;
                    min-width: 145px;
                    column-gap: 14px;
                }

                #clockdiv > div, #clockdiv2 > div, #clockdiv3 > div {
                    display: inline-flex;
                    color: #ffffff !important;
                    font-weight: 400;
                    flex-wrap: wrap;
                    line-height: 12px;
                    text-align: center;
                    justify-content: center;
                    flex-direction: column;
                }

                #clockdiv div > span, #clockdiv2 div > span, #clockdiv3 div > span {
                    float: left;
                    width: 100%;
                    color: #ffffff !important;
                    font-size: 16px;
                    font-weight: 600;
                    padding-bottom: 0px;
                    min-width: 19.5px;
                }

                #clockdiv > h3 {
                    display: block;
                    font-size: 15px;
                    line-height: 17px;
                    color: white;
                    padding-bottom: 7px;
                }
            </style>
            <script>
                var deadline = '<?php echo $countdown_date; ?> <?php echo $countdown_time; ?> GMT+0430';

                function time_remaining(endtime) {
                    var t = Date.parse(endtime) - Date.parse(new Date());
                    var seconds = Math.floor((t / 1000) % 60);
                    var minutes = Math.floor((t / 1000 / 60) % 60);
                    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
                    var days = Math.floor(t / (1000 * 60 * 60 * 24));
                    return {'total': t, 'days': days, 'hours': hours, 'minutes': minutes, 'seconds': seconds};
                }

                function run_clock(id, endtime) {
                    var clock = document.getElementById(id);
                    var days_span = clock.querySelector('.days');
                    var hours_span = clock.querySelector('.hours');
                    var minutes_span = clock.querySelector('.minutes');
                    var seconds_span = clock.querySelector('.seconds');

                    function update_clock() {
                        var t = time_remaining(endtime);
                        days_span.innerHTML = t.days;
                        hours_span.innerHTML = ('0' + t.hours).slice(-2);
                        minutes_span.innerHTML = ('0' + t.minutes).slice(-2);
                        seconds_span.innerHTML = ('0' + t.seconds).slice(-2);
                        if (t.total <= 0 ) {
                            clearInterval(timeinterval);
                            document.getElementById("clockdiv").innerHTML = "<h3>زمان به اتمام رسید</h3>";
                            /*$(".header").css('margin-top', '25px');
                            $('#shokrino-top-notif-bar').css('display', 'none');
                            setTimeout(function () {
                                $('#shokrino-top-notif-bar').remove();
                            }, 0);*/
                        }
                        <?php if (empty($countdown_date)) { ?>
                            clearInterval(timeinterval);
                            document.getElementById("clockdiv").innerHTML = "<h3>زمان انتخاب نشده است</h3>";
                        <?php } ?>
                    }

                    update_clock();
                    var timeinterval = setInterval(update_clock, 1000);
                }

                run_clock('clockdiv', deadline);
            </script>
        </div>
    <?php } ?>
    <style>
        .header {
            margin-top: 0 !important;
        }
        <?php
        if ($header_style == 'header_one') { ?>
        .header-notif-bar-shokrino {
            display: flex;
            margin: auto 12px;
            margin-top: 15px;
            margin-bottom: -55px;
            background: <?php if (!empty($notifbar_bg_color)) { echo $notifbar_bg_color; } else { ?>#1b90d6<?php } ?>;
            border-radius: 20px;
            min-height: 75px !important;
            padding: 2px 5px 28px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            margin-right: auto;
            margin-left: auto;
            max-width: calc(var(--shkrn-var-container-width) - 25px);
        }
        @media screen and (max-width: 1040px) {
            .header-notif-bar-shokrino {
                margin-bottom: -45px;
                margin-left: 15px;
                margin-right: 15px;
            }
        }
        <?php } elseif ($header_style == 'header_three' or $header_style == 'header_two') { ?>
        .header-notif-bar-shokrino {
            display: flex;
            background: <?php if (!empty($notifbar_bg_color)) { echo $notifbar_bg_color; } else { ?>#1b90d6<?php } ?>;
            border-radius: 0;
            min-height: 54px !important;
            padding: 2px 5px 2px;
            flex-wrap: wrap;
            margin-top: -2px;
            align-items: center;
            justify-content: center;
            margin: auto;
			position: relative;
        }
		.header-notif-bar-shokrino::before {
			content: "";
			position: absolute;
			width: 100vw;
			height: 100%;
			background: <?php if (!empty($notifbar_bg_color)) { echo $notifbar_bg_color; } else { ?>#1b90d6<?php } ?>;
			z-index: -1;
		}
        <?php } else { ?>
        .header-notif-bar-shokrino {
            display: flex;
            margin: auto 12px;
            margin-top: 15px;
            margin-bottom: -55px;
            background: <?php if (!empty($notifbar_bg_color)) { echo $notifbar_bg_color; } else { ?>#1b90d6<?php } ?>;
            border-radius: 20px;
            min-height: 75px !important;
            padding: 2px 5px 28px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            margin-right: auto;
            margin-left: auto;
        }
        @media screen and (max-width: 1040px) {
            .header-notif-bar-shokrino {
                margin-bottom: -45px;
                margin-left: 15px;
                margin-right: 15px;
            }
        }
        <?php } ?>

        @media screen and (max-width: 768px) {
            #clockdiv {
                margin: 8.5px 0 !important;
            }
        }
        .close-btn-js {
            display: flex;
            padding: 15px;
            cursor: pointer;
        }
        .close-notif-btn {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.22);
            cursor: pointer;
        }
        .text-notif-bar {
            display: flex;
            align-items: center;
            text-align: right;
            flex: 1;
            color: white;
            min-width: 50%;
        }
        .text-notif-bar a {
            color: white;
        }
        .btn-notif-bar {
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            padding: 2px 20px;
        }
        .btn-notifbar-calltoaction {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-around;
            min-width: 80px;
            background: #e4f4ff38;
            color: white !important;
            padding: 2px 12px;
            border-radius: 9px;
            column-gap: 2px;
        }
        .btn-notifbar-calltoaction svg {
            margin-top: 5px;
            stroke: white;
        }
    </style>
    <script>
        $(".close-btn-js").click(function (event) {
            $(".header").css('margin-top', '25px');
            $('#shokrino-top-notif-bar').css('display', 'none');
            var expires = (new Date(Date.now()+ 86400*1000)).toUTCString();
            document.cookie = "DisplayNotif=closed; expires=" + expires + ";path=/; SameSite=None; Secure";
            setTimeout(function () {
                $('#shokrino-top-notif-bar').remove();
            }, 0);
        });
    </script>
</div>
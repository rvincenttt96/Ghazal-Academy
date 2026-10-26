<?php
defined( 'ABSPATH' ) || exit;

$product_id_counter = $product->get_id();
$time_off_to = get_post_meta( $product_id_counter, '_sale_price_dates_to', true );
$time_off_from = get_post_meta( $product_id_counter, '_sale_price_dates_from', true );
$is_scheduled_sale = !empty($time_off_to) && !empty($time_off_from);
if ($is_scheduled_sale) {
    ?>
    <div id="boxcountershkrnonsale_<?php echo $product_id_counter; ?>" class="sale-counter-shokrino flex-center"></div>
    <style>
        #boxcountershkrnonsale_<?php echo $product_id_counter; ?> {
            display: none;
        }
        .sale-counter-shokrino {
            display: flex;
            width: 100%;
            margin: auto;
            background: var(--shkrn-var-color-light-dark-opacity-bg);
            border-radius: 35px;
            column-gap: 10px;
            justify-content: space-evenly;
            padding: 6px;
        }
        .title-off-counter {
            display: flex;
            justify-content: right;
            align-items: center;
            column-gap: 3px;
            color: var(--shkrn-var-main-color);
        }
        .offcounterdiv {
            display: inline-flex;
            text-align: center;
            font-size: 9px;
            padding: 5px;
            border-radius: 10px;
            justify-content: center;
            flex-direction: row-reverse;
            min-width: 145px;
            column-gap: 5px;
            color: white;
        }
        .offcounterdiv .counter-box {
            display: inline-flex;
            flex-wrap: wrap;
            line-height: 12px;
            text-align: center;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            min-width: 19.5px;
            background: var(--shkrn-var-main-color);
            color: white !important;
            padding: 8px 0px 4px;
            border-radius: 7px;
        }
        .offcounterdiv .counter-box span {
            font-size: 10px;
            font-weight: 400;
            opacity: 0.7;
        }
        .title-off-counter .icon.icon-tabler.icon-tabler-clock {
            stroke: var(--shkrn-var-main-color);
        }
        @media screen and (max-width: 400px) {
            .title-off-counter {
                margin-left: 4px;
            }
            .sale-counter-shokrino {
                flex-wrap: wrap;
                justify-content: center;
            }
            .offcounterdiv {
                margin: 5px auto;
            }
        }
        .box-course .offcounterdiv {
            padding: 5px 0 5px;
            column-gap: 3px;
            min-width: 130px;
        }
        .box-course .sale-counter-shokrino {
            padding: 0 8px;
            gap: 5px;
            font-size: 12px;
        }
        .box-course .title-off-counter > svg {
            width: 20px;
        }
    </style>
    <script>
        let counter_<?php echo $product_id_counter; ?> = document.getElementById("boxcountershkrnonsale_<?php echo $product_id_counter; ?>");
        let endCounter_<?php echo $product_id_counter; ?> = new Date("<?php echo date('Y-m-d', $time_off_to); ?>").getTime();
        let startCounter_<?php echo $product_id_counter; ?> = new Date("<?php echo date('Y-m-d', $time_off_from); ?>").getTime();

        let x_<?php echo $product_id_counter; ?> = setInterval(function() {
            let nowCounter_<?php echo $product_id_counter; ?> = new Date().getTime();
            let logicCounter_<?php echo $product_id_counter; ?> = startCounter_<?php echo $product_id_counter; ?> <= nowCounter_<?php echo $product_id_counter; ?> && nowCounter_<?php echo $product_id_counter; ?> <= endCounter_<?php echo $product_id_counter; ?>;
            let distance_<?php echo $product_id_counter; ?> = endCounter_<?php echo $product_id_counter; ?> - nowCounter_<?php echo $product_id_counter; ?>;

            let days_<?php echo $product_id_counter; ?> = Math.floor(distance_<?php echo $product_id_counter; ?> / (1000 * 60 * 60 * 24));
            let hours_<?php echo $product_id_counter; ?> = Math.floor((distance_<?php echo $product_id_counter; ?> % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes_<?php echo $product_id_counter; ?> = Math.floor((distance_<?php echo $product_id_counter; ?> % (1000 * 60 * 60)) / (1000 * 60));
            let seconds_<?php echo $product_id_counter; ?> = Math.floor((distance_<?php echo $product_id_counter; ?> % (1000 * 60)) / 1000);

            if (logicCounter_<?php echo $product_id_counter; ?>) {
                counter_<?php echo $product_id_counter; ?>.style.display = "flex";
                counter_<?php echo $product_id_counter; ?>.innerHTML =
                    '<span class="title-off-counter"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>'
                    + '<p><?php _e('Amazing offer','shokrino'); ?></p>' + '</span><div class="offcounterdiv"><div class="counter-box">' + days_<?php echo $product_id_counter; ?> + '<span>روز</span></div><div class="counter-box">' + hours_<?php echo $product_id_counter; ?> + '<span>ساعت</span></div><div class="counter-box">' + minutes_<?php echo $product_id_counter; ?> + '<span>دقیقه</span></div><div class="counter-box">' + seconds_<?php echo $product_id_counter; ?> + '<span>ثانیه</span></div></div>';
            } else {
                clearInterval(x_<?php echo $product_id_counter; ?>);
                counter_<?php echo $product_id_counter; ?>.style.display = "none";
            }
        }, 500);
    </script>
    <?php
}
?>

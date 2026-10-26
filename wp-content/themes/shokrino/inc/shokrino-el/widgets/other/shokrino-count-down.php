<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_count_down_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_count_down';
    }

    public function get_title() {
        return 'شمارنده معکوس';
    }

    public function get_icon() {
        return 'eicon-countdown shokrino-el-icon';
    }


    public function get_categories() {
        return ['general_category'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'تنظیمات اصلی', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'date',
            [
                'label' => esc_html__( 'زمان پایان شمارش', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__( 'تنظیمات استایل', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'headings_typography1',
            [
                'label' => esc_html__( 'سایز فونت عناوین', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 9,
                ],
                'selectors' => [
                    '{{WRAPPER}} .count-down-el-shokrino .smalltext' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'numbers_typography1',
            [
                'label' => esc_html__( 'سایز فونت اعداد', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .count-down-el-shokrino span' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} #shokrino_countdown > h3' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'bg-color',
            [
                'label' => esc_html__( 'رنگ متن زمینه باکس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #shokrino_countdown' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'border-radius-box',
            [
                'label' => esc_html__( 'گرد بودن حاشیه باکس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} #shokrino_countdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'column-gap-items',
            [
                'label' => esc_html__( 'فاصله بین اعداد', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 400,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} #shokrino_countdown' => 'column-gap: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'line-height-items',
            [
                'label' => esc_html__( 'ارتفاع خط متن ها', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 400,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} #shokrino_countdown > div' => 'line-height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} #shokrino_countdown > h3' => 'line-height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'padding-box',
            [
                'label' => esc_html__( 'فاصله داخلی باکس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} #shokrino_countdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();




    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $date = date("Y/m/d H:i",strtotime($settings['date']));
        $get_font_size = $this->get_settings_for_display()['numbers_typography1']['size'];
        $min_width = $get_font_size + (24 * $get_font_size / 100);
        ?>

        <style>

        </style>

        <section class="shokrino-countdown">
            <div class="count-down-el-shokrino">
                <div id="shokrino_countdown" data-animation="animated flipInY">
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
                    .count-down-el-shokrino {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    #shokrino_countdown {
                        display: inline-flex;
                        text-align: center;
                        font-size: 9px;
                        padding: 10px 4px 3px;
                        border-radius: 10px;
                        background: var(--shkrn-var-main-color);
                        justify-content: center;
                        margin: 0;
                        flex-direction: row-reverse;
                        min-width: 145px;
                        column-gap: 14px;
                    }

                    #shokrino_countdown > div, #shokrino_countdown2 > div, #shokrino_countdown3 > div {
                        display: inline-flex;
                        color: #fff !important;
                        font-weight: 400;
                        flex-wrap: wrap;
                        line-height: 12px;
                        text-align: center;
                        justify-content: center;
                        flex-direction: column;
                    }

                    .count-down-el-shokrino span {
                        float: left;
                        width: 100%;
                        color: #fff !important;
                        font-size: 16px;
                        font-weight: 600;
                        padding-bottom: 0px;
                        min-width: 19.5px;
                    }
                    <?php if (!empty($min_width)) { ?>
                    .count-down-el-shokrino span {
                        min-width: <?php echo $min_width; ?>px;
                    }
                    <?php } ?>
                    #shokrino_countdown > h3 {
                        display: block;
                        font-size: 15px;
                        line-height: 17px;
                        color: white;
                        padding-bottom: 7px;
                    }
                </style>
                <script>
                    var deadline = ' <?php echo $date; ?>  GMT+0430';
                    function time_remaining(endtime){
                        var t = Date.parse(endtime) - Date.parse(new Date());
                        var seconds = Math.floor( (t/1000) % 60 );
                        var minutes = Math.floor( (t/1000/60) % 60 );
                        var hours = Math.floor( (t/(1000*60*60)) % 24 );
                        var days = Math.floor( t/(1000*60*60*24) );
                        return {'total':t, 'days':days, 'hours':hours, 'minutes':minutes, 'seconds':seconds};
                    }
                    function run_clock(id,endtime){
                        var clock_shokrino = document.getElementById(id);

                        // get spans where our clock numbers are held
                        var days_span = clock_shokrino.querySelector('.days');
                        var hours_span = clock_shokrino.querySelector('.hours');
                        var minutes_span = clock_shokrino.querySelector('.minutes');
                        var seconds_span = clock_shokrino.querySelector('.seconds');

                        function update_clock(){
                            var t = time_remaining(endtime);

                            // update the numbers in each part of the clock
                            days_span.innerHTML = t.days;
                            hours_span.innerHTML = ('0' + t.hours).slice(-2);
                            minutes_span.innerHTML = ('0' + t.minutes).slice(-2);
                            seconds_span.innerHTML = ('0' + t.seconds).slice(-2);

                            if(t.total<=0){ clearInterval(timeinterval); document.getElementById("shokrino_countdown").innerHTML = "<h3>زمان به اتمام رسید</h3>"; }
                        }
                        update_clock();
                        var timeinterval = setInterval(update_clock,1000);
                    }
                    run_clock('shokrino_countdown',deadline);
                </script>
            </div>
        </section>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_count_down .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
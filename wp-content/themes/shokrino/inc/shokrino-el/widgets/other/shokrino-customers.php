<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_costomers_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'customers-slider';
    }

    public function get_title() {
        return 'کاروسل نظر مشتریان';
    }

    public function get_icon() {
        return 'eicon-person shokrino-el-icon';
    }

    public function get_categories() {
        return ['general_category'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'تنظیمات نظرات', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'image_costumer',
            [
                'label' => __( 'تصویر مشتری', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'name_costumer',
            [
                'label' => esc_html__( 'نام مشتری', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'نام مشتری', 'shokrino' ),
                'placeholder' => esc_html__( 'نام را بنویسید...', 'shokrino' ),
            ]
        );

        $repeater->add_control(
            'title_costumer',
            [
                'label' => esc_html__( 'عنوان کاری مشتری', 'shokrino' ),
                'description' => esc_html__( 'این بخش اختیاری است و در صورت خالی بودن ، باکس عنوان کاری مخفی میشود', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__( 'عنوان کاری را بنویسید...', 'shokrino' ),
            ]
        );

        $repeater->add_control(
            'text_costumer',
            [
                'label' => esc_html__( 'متن نظر مشتری', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'متن نظر اینجا قرار میگیرد...', 'shokrino' ),
                'placeholder' => esc_html__( 'متن نظر را بنویسید...', 'shokrino' ),
            ]
        );

        $this->add_control(
            'main_slider',
            [
                'label' => __( 'اسلایدر نظرات', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_costumer' => __( 'اسلاید 1', 'shokrino' ),
                        'name_costumer' => __( 'اسلاید 1', 'shokrino' ),
                        'title_costumer' => __( 'اسلاید 1', 'shokrino' ),
                        'text_costumer' => __( 'اسلاید 1', 'shokrino' ),
                    ],
                ],
                'title_field' => 'نظر مشتری',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'slider_section',
            [
                'label' => __( 'تنظیمات اسلایدر', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slider_autoplay',
            [
                'label' => __( 'حرکت خودکار اسلاید ها', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'روشن', 'shokrino' ),
                'label_off' => esc_html__( 'خاموش', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'slider_autoplaytime',
            [
                'label' => __( 'فاصله زمان حرکت اسلاید ها (ms)', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 500,
                'max' => 10000,
                'step' => 500,
                'default' => 5000,
            ]
        );

        $this->add_control(
            'slider_autoplayspeed',
            [
                'label' => __( 'سرعت جابجایی اسلاید ها (ms)', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 100,
                'max' => 2000,
                'step' => 100,
                'default' => 800,
            ]
        );

        $this->add_control(
            'slider_autoplayhover',
            [
                'label' => __( 'ثابت ماندن هنگام هاور موس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'بله', 'shokrino' ),
                'label_off' => esc_html__( 'خیر', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'slider_loop',
            [
                'label' => __( 'چرخه بینهایت باشد؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'بله', 'shokrino' ),
                'label_off' => esc_html__( 'خیر', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'slider_dots',
            [
                'label' => __( 'نقطه های زیر اسلایدر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'slider_navs',
            [
                'label' => __( 'فلش های بعدی و قبلی اسلایدر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
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
            'border-radius-img',
            [
                'label' => esc_html__( 'گرد بودن حاشیه تصویر مشتری', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-title-and-image > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border-radius-box-items',
            [
                'label' => esc_html__( 'گرد بودن حاشیه باکس عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-title-and-image > .box-customers-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'background-color-box-items',
            [
                'label' => esc_html__( 'رنگ زمینه داخل باکس عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-title-and-image > .box-customers-title' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'background-color-box-items-text',
            [
                'label' => esc_html__( 'رنگ متن های داخل باکس عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-title-and-image > .box-customers-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow_items',
                'label' => esc_html__( 'سایه باکس عنوان', 'shokrino' ),
                'selector' => '{{WRAPPER}} .customers-slider .box-customer-title-and-image > .box-customers-title',
            ]
        );

        $this->add_control(
            'border-radius-box-text',
            [
                'label' => esc_html__( 'گرد بودن حاشیه باکس متن نظر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-comment-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'background-color-box-text',
            [
                'label' => esc_html__( 'رنگ زمینه داخل باکس متن نظر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-comment-text' => 'background: {{VALUE}}',
                    '{{WRAPPER}} .owl-nav .owl-next > span, .owl-nav .owl-prev > span' => 'background: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'background-color-box-text-txt',
            [
                'label' => esc_html__( 'رنگ متن های داخل باکس متن نظر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customers-slider .box-customer-comment-text' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .owl-nav .owl-next > span > svg,.owl-nav .owl-prev > span > svg' => 'stroke: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow_text',
                'label' => esc_html__( 'سایه باکس متن نظر', 'shokrino' ),
                'selector' => '{{WRAPPER}} .customers-slider .box-customer-comment-text',
            ]
        );

        $this->add_control(
            'dots-color-slider',
            [
                'label' => esc_html__( 'رنگ نقطه های اسلایدر فعال', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme .owl-dots .owl-dot.active span, .owl-theme .owl-dots .owl-dot:hover span' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $mainslider = $settings['main_slider'];
        $sliderloop = $settings['slider_loop'];
        $sliderautoplay = $settings['slider_autoplay'];
        $sliderautoplaytime = $settings['slider_autoplaytime'];
        $sliderautoplayhover = $settings['slider_autoplayhover'];
        $autoplayspeed = $settings['slider_autoplayspeed'];
        $count = rand(0, 100000); ?>

        <section class="customers-slider">
        <div id="job-slider-<?php echo $count; ?>" class="swiper job-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden">
            <div class="swiper-wrapper post-slider-shokrino" aria-live="polite">
                <?php foreach ($mainslider as $slider) { ?>
                    <div class="box-customers swiper-slide">
                        <div class="box-customer-title-and-image">
                            <img src="<?php echo $slider['image_costumer']['url']; ?>">
                            <div class="box-customers-title">
                                <h3><?php echo $slider['name_costumer']; ?></h3>
                                <?php if (!empty($slider['title_costumer'])) { ?>
                                    <h5><?php echo $slider['title_costumer']; ?></h5>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="box-customer-comment-text">
                            <p><?php echo $slider['text_costumer']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="swiper-btn-style-shkrn swiper-button-prev"></div>
            <div class="swiper-btn-style-shkrn swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
        </section>

        <script>
            var swiper_slider_posts_box = new Swiper("#job-slider-<?php echo $count; ?>", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: <?php if (true == $sliderloop) { echo "true"; } else {echo "false"; } ?>,
                lazy: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true
                },
                <?php if ($sliderautoplay) { ?>
                autoplay: {
                    delay: <?php echo $sliderautoplaytime; ?>,
                    pauseOnMouseEnter: <?php if (true == $sliderautoplayhover) { echo "true"; } else {echo "false"; } ?>,
                },
                speed: <?php echo $autoplayspeed; ?>,
                <?php } ?>
            });
        </script>

        <style>
            .customers-slider {
                width: 100%;
            }
            .box-customers {
                display: flex;
                flex-direction: row;
                width: 100%;
                justify-content: center;
                padding: 20px;
            }
            .job-wrapper-slider-shkrn:hover .swiper-btn-style-shkrn:not(.swiper-button-disabled) {
                visibility: visible;
                opacity: 1;
            }
            .box-customer-title-and-image {
                display: flex;
                flex-wrap: wrap;
                flex-direction: column;
                justify-content: center;
                text-align: center;
                margin: 18px;
                padding: 12px;
                width: fit-content;
            }
            .box-customer-title-and-image > .box-customers-title {
                background: var(--shkrn-var-color-light-dark-middle);
                box-shadow: 0 4px 15px 5px #bedbf14f;
                border-radius: 20px;
                position: relative;
                margin: -18px 3px 0;
                padding: 3px;
                padding-top: 9px;
                padding-bottom: 9px;
            }
            .box-customer-title-and-image > img {
                border-radius: 35px;
                max-width: 210px !important;
                height: auto;
                margin: auto;
                width: 100%;
            }
            .box-customers-title > h3 {
                font-size: 16px;
                line-height: 22px;
                margin-top: 5px;
            }
            .box-customers-title > h5 {
                font-size: 12.9px;
                font-weight: 500;
                line-height: 16px;
            }
            .box-customer-comment-text {
                display: flex;
                background: var(--shkrn-var-color-light-dark-middle);
                box-shadow: 0 4px 15px 5px #bedbf14f;
                border-radius: 35px;
                margin: 0px -12% 0px 20px;
                z-index: -1;
                padding-right: 15%;
                padding-left: 5%;
                justify-content: center;
                flex-direction: column;
                font-size: 20px;
                max-width: 80%;
                min-width: 50%;
            }
            @media screen and (max-width: 780px) {
                .box-customers {
                    flex-direction: column;
                    padding: 0;
                    margin: 0 auto 20px;
                }
                .box-customer-title-and-image {
                    margin: 18px auto -50px;
                }
                .box-customer-comment-text {
                    font-size: unset;
                    margin: 0px auto;
                    padding: 50px 15px 15px;
                    min-height: auto;
                    min-width: 100%;
                }
            }
        </style>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-customers-slider .elementor-widget-container{min-height: 1px;height: fit-content;}
            </style>';
        }

    }

    protected function content_template() {}

}
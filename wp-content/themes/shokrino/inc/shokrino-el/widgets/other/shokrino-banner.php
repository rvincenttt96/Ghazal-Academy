<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_banner_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_banner';
    }

    public function get_title() {
        return 'بنر تبلیغاتی';
    }

    public function get_icon() {
        return 'eicon-thumbnails-half shokrino-el-icon';
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
            'style_image1',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/banner-ads.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );

        $this->add_control(
            'banner_title',
            [
                'label' => esc_html__( 'متن  سطر اول را وارد کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( 'عنوان جدید را برای تغییر عنوان این المان وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'banner_subtitle',
            [
                'label' => esc_html__( 'متن  سطر دوم را وارد کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( 'عنوان جدید را برای تغییر عنوان این المان وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'banner_subtitle2',
            [
                'label' => esc_html__( 'متن  سطر سوم را وارد کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( 'عنوان جدید را برای تغییر عنوان این المان وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'hr-banner',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'banner_btn',
            [
                'label' => __( 'دکمه نمایش داده شود؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'بله', 'shokrino' ),
                'label_off' => esc_html__( 'خیر', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'banner_btn_text',
            [
                'label' => esc_html__( 'متن دکمه ', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( 'متن را وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'banner_btn_link',
            [
                'label' => __( 'لینک دکمه', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'description' => esc_html__( 'لینک صفحه ای که میخواهید به آنجا هدایت شود را وارد کنید', 'shokrino' ),
                'label_block' => true,
                'input_type' => 'url',
            ]
        );

        $this->add_control(
            'hr-banner2',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'image_banner',
            [
                'label' => esc_html__( 'انتخاب عکس بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
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
            'border-radius-banner',
            [
                'label' => esc_html__( 'گرد بودن حاشیه زمینه بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .banner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border-radius-banner-image',
            [
                'label' => esc_html__( 'گرد بودن حاشیه عکس بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .banner .banner-left figure img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'size-banner',
            [
                'label' => esc_html__( 'فاصله داخلی عکس بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .banner .banner-left figure img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'height-img-banner',
            [
                'label' => esc_html__( 'ارتفاع تصویر بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 220,
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner .banner-left figure img' => 'height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'width-img-banner',
            [
                'label' => esc_html__( 'عرض تصویر بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 220,
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner .banner-left figure img' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label' => esc_html__( 'رنگ پس زمینه بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .banner' => 'background: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'رنگ متن های بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .banner-right h3' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .banner-right h2' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .banner-right h4' => 'color: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'font_family',
            [
                'label' => esc_html__( ' خانواده فونت بنر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::FONT,
                'default' => "yekanbakh, sans-serif",
                'selectors' => [
                    '{{WRAPPER}} .banner-right h3' => 'font-family: {{VALUE}} !important',
                    '{{WRAPPER}} .banner-right h2' => 'font-family: {{VALUE}} !important',
                    '{{WRAPPER}} .banner-right h4' => 'font-family: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'hr-banner-3',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'btn_bg_color',
            [
                'label' => esc_html__( 'رنگ پس زمینه دکمه', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .banner .banner-right .banner-button a' => 'background: {{VALUE}} !important',
                    '{{WRAPPER}} .banner .banner-right .banner-button a:hover' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .banner .banner-right .banner-button a:hover svg' => 'stroke: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'btn_text_color',
            [
                'label' => esc_html__( 'رنگ متن دکمه', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .banner .banner-right .banner-button a' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .banner .banner-right .banner-button a svg' => 'stroke: {{VALUE}} !important',
                    '{{WRAPPER}} .banner .banner-right .banner-button a:hover' => 'background: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => esc_html__( 'سایه باکس', 'shokrino' ),
                'selector' => '{{WRAPPER}} .banner',
            ]
        );

        $this->end_controls_section();




    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $banner_title = $settings['banner_title'];
        $banner_subtitle = $settings['banner_subtitle'];
        $banner_subtitle2 = $settings['banner_subtitle2'];
        $banner_btn = $settings['banner_btn'];
        $banner_btn_text = $settings['banner_btn_text'];
        $banner_btn_link = $settings['banner_btn_link'];
        $image_banner = $settings['image_banner']['url'];
        $bg_color = $settings['bg_color'];
        $text_color = $settings['text_color'];
        $font_family = $settings['font_family'];
        $btn_bg_color = $settings['btn_bg_color'];
        $btn_text_color = $settings['btn_text_color'];
        ?>

        <style>
            .banner {
                width: 100%;
                background: var(--shkrn-var-main-color);
                border-radius: 35px;
                box-shadow: 0px 7px 50px -5px rgba(0, 159, 255, 0.6);
                padding: 22px 40px;
                margin: 25px auto;
                display: flex;
                flex-wrap: wrap;
            }
            .banner .banner-right {
                display: flex;
                width: 60%;
                padding: 20px;
                height: 100%;
                flex-wrap: wrap;
                justify-content: right;
                align-items: center;
                margin: auto;
            }
            .banner .banner-right h3 {
                color: white;
                font-size: 30px;
                display: block;
                width: 100%;
            }
            .banner .banner-right h2 {
                font-size: 70px;
                font-weight: 900;
                display: block;
                color: white;
                line-height: 1;
                width: 100%;
                margin-top: 20px;
            }
            .banner .banner-right h4 {
                color: white;
                font-size: 20px;
                display: block;
                font-weight: bold;
                line-height: 1;
            }
            .banner .banner-right .banner-button {
                display: flex;
                justify-content: center;
                width: 100%;
                margin-top: 14px;
            }
            .banner .banner-right .banner-button a:hover , .banner .banner-right .banner-button a:hover {
                background: white;
                color: var(--shkrn-var-color-light-dark-opacity-text);
            }
            .banner .banner-right .banner-button a:hover svg {
                stroke: var(--shkrn-var-color-light-dark-opacity-text);
            }
            .banner .banner-right .banner-button a {
                text-decoration: none;
                color: white;
                font-size: 19px;
                font-weight: 600;
                background: var(--shkrn-var-main-color);
                padding: 7px 14px;
                border-radius: 12px;
                display: flex;
            }
            .banner .banner-right .banner-button a svg {
                stroke: var(--shkrn-var-color-light-dark-opacity-text);
                margin-top: -3px;
            }
            .banner .banner-left {
                display: flex;
                width: 38%;
                margin: auto;
                justify-content: center;
                align-items: center;
                padding: 16px;
            }
            .banner .banner-left figure {
                margin: 0;
                height: fit-content;
                display: flex;
                padding: 10px;
            }
        </style>

        <section class="banner">
            <div class="banner-right">
                <h3><?php if(!empty($banner_title)) { echo $banner_title; } else { ?> <?php esc_html_e('پیشنهاد ما دوره رایگان', 'shokrino' ); ?> <?php } ?></h3>
                <h2><?php if(!empty($banner_subtitle)) { echo $banner_subtitle; } else { ?> <?php esc_html_e('طراح وبسایت شو', 'shokrino' ); ?> <?php } ?></h2>
                <h4><?php if(!empty($banner_subtitle2)) { echo $banner_subtitle2; } else { ?> <?php esc_html_e('آموزش وردپرس مقدماتی | بهترین گزینه برای ورود به دنیای طراحی سایت', 'shokrino' ); ?> <?php } ?></h4>
        <?php if (true == $banner_btn) { ?>
                <div class="banner-button">
                    <a href="<?php if(!empty($banner_btn_link)) { echo $banner_btn_link; } else { ?> # <?php } ?>">
                        <?php if(!empty($banner_btn_text)) { echo $banner_btn_text; } else { ?> <?php esc_html_e('همین حالا ثبت نام کنید', 'shokrino' ); ?> <?php } ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-left" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#597e8d" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <polyline points="11 7 6 12 11 17" />
                            <polyline points="17 7 12 12 17 17" />
                        </svg>
                    </a>
                </div>
            <?php } ?>
            </div>
            <div class="banner-left">
                <figure>
                    <img src="<?php if(isset($image_banner)) { echo $image_banner; } else { echo SHKRN_ASSETS . '/img/wp.png'; } ?>">
                </figure>
            </div>
        </section>

        <style>
            <?php
            if (!empty($bg_color)) {
                ?>
            .banner {
                background: none;
                background-color: <?php echo $bg_color; ?> !important;
            }
            <?php }
            if (!empty($text_color)) {
                ?>
            .banner-right h3 , .banner-right h2 , .banner-right h4 {
                color: <?php echo $text_color; ?> !important;
            }
            <?php }
            if (!empty($btn_bg_color)) {
                ?>
            .banner .banner-right .banner-button a {
                background: none;
                background-color: <?php echo $btn_bg_color; ?> !important;
            }
            <?php if(!empty($btn_text_color)) { ?>
            .banner-button a {
                color: <?php echo $btn_text_color; ?> !important;
            }
            .banner .banner-right .banner-button a svg {
                stroke: <?php echo $btn_text_color; ?> !important;
            }
            .banner .banner-right .banner-button a:hover svg {
                stroke: <?php echo $btn_bg_color; ?> !important;
            }
            .banner .banner-right .banner-button a:hover, .banner .banner-right .banner-button a:hover {
                color: <?php echo $btn_bg_color; ?> !important;
                background-color: <?php echo $btn_text_color; ?> !important;
            }
            <?php } }
            if (!empty($btn_text_color)) {
                ?>
            .banner-button a {
                color: <?php echo $btn_text_color; ?> !important;
            }
            <?php if(!empty($btn_bg_color)) { ?>
            .banner .banner-right .banner-button a {
                background: none;
                background-color: <?php echo $btn_bg_color; ?> !important;
            }
            .banner .banner-right .banner-button a:hover, .banner .banner-right .banner-button a:hover {
                color: <?php echo $btn_bg_color; ?> !important;
                background-color: <?php echo $btn_text_color; ?> !important;
            }
            <?php } }

            if (!empty($font_family)) {
                ?>
            .banner-right h3 , .banner-right h2 , .banner-right h4 , .banner-right a {
                font-family: <?php $font_family; ?> !important;
            }
            <?php
        }
        ?>

        </style>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_banner .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
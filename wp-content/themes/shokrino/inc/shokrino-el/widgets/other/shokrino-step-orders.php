<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_step_order_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'steps-order';
    }

    public function get_title() {
        return 'مراحل ثبت سفارش';
    }

    public function get_icon() {
        return 'eicon-product-breadcrumbs shokrino-el-icon';
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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'step-number',
            [
                'label' => esc_html__( 'َشماره مرحله', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '1',
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 1,
            ]
        );

        $repeater->add_control(
            'step-icon',
            [
                'label' => esc_html__( 'آیکون', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-check',
                    'library' => 'solid',
                ],
            ]
        );

        $repeater->add_control(
            'name_step',
            [
                'label' => esc_html__( 'نام مرحله', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'تایید سفارش', 'shokrino' ),
                'placeholder' => esc_html__( 'نام مرحله سفارش را بنویسید...', 'shokrino' ),
            ]
        );

        $this->add_control(
            'main_slider',
            [
                'label' => __( 'اسلایدر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'step-number' => __( '1', 'shokrino' ),
                        'step-icon' => __( 'اسلاید 1', 'shokrino' ),
                        'name_step' => __( 'مرحله اول', 'shokrino' ),
                    ],
                ],
                'title_field' => 'اسلاید',
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
            'icon-background-color',
            [
                'label' => esc_html__( 'رنگ زمینه آیکون', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .step-order-item .icon-box-step' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'icon-color-i',
            [
                'label' => esc_html__( 'رنگ آیکون', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .step-order-item .icon-box-step > i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .step-order-item .icon-box-step > img' => 'fill: {{VALUE}};stroke: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'text-color-under-step',
            [
                'label' => esc_html__( 'رنگ متن عنوان مرحله', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .step-order-item h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'text-color-number-step',
            [
                'label' => esc_html__( 'رنگ زمینه شماره مرحله', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .step-number-box' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'background-color-number-step',
            [
                'label' => esc_html__( 'رنگ شماره مرحله', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .step-number-box' => 'color: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text-under-step-typography',
                'selector' => '{{WRAPPER}} .step-order-item h3',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $mainslider = $settings['main_slider'];
        ?>

        <section class="steps-order">
            <?php foreach ($mainslider as $slider) { ?>
                <div class="step-order-item">
                    <div class="icon-box-step">
                        <?php if ($slider['step-icon']['library'] == 'svg') { ?>
                            <img src="<?php echo $slider['step-icon']['value']['url']; ?>">
                        <?php } else { ?>
                            <i class="<?php echo $slider['step-icon']['value']; ?>"></i>
                        <?php } ?>
                        <div class="step-number-box"><?php echo $slider['step-number']; ?></div>
                    </div>
                    <h3><?php echo $slider['name_step']; ?></h3>
                </div>
            <?php } ?>
        </section>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-steps-order .elementor-widget-container{min-height: 1px;height: fit-content;}
            </style>';
        }

    }

    protected function content_template() {}

}
<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_custom_code_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_custom_code';
    }

    public function get_title() {
        return 'کدنویسی سفارشی';
    }

    public function get_icon() {
        return 'eicon-editor-code shokrino-el-icon';
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
            'custom_html',
            [
                'label' => esc_html__( 'ادیتور HTML', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'html',
                'rows' => 20,
            ]
        );

        $this->add_control(
            'custom_css',
            [
                'label' => esc_html__( 'ادیتور CSS', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 20,
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $html = $settings['custom_html'];
        $css = $settings['custom_css'];
        ?>

        <?php echo $html; ?>

        <style>
            <?php echo $css; ?>
        </style>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_custom_code .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
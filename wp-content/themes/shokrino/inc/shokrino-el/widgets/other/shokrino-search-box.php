<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_search_box_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_search_box';
    }

    public function get_title() {
        return 'باکس سرج در سایت';
    }

    public function get_icon() {
        return 'eicon-search shokrino-el-icon';
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
            'choose_type',
            [
                'label' => esc_html__( 'نوع سرچ', 'present' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all'  => esc_html__( 'پست و محصولات', 'present' ),
                    'products'  => esc_html__( 'محصولات', 'present' ),
                ],
            ]
        );

        $this->add_control(
            'text-inbox',
            [
                'label' => __( 'متن داخل باکس سرچ', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('کلمه مورد نظر را برای جستجو وارد کنید...', 'shokrino' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'max-width-box',
            [
                'label' => esc_html__( 'حداکثر عرض باکس  سرچ', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1000,
                ],
                'selectors' => [
                    '{{WRAPPER}} .search-el-shokrino' => 'max-width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $search_type = $settings['choose_type'];
        $text_inbox = $settings['text-inbox'];
        ?>
<div class="search-el-shokrino">
<?php if ($search_type == 'all') { ?>
<form action="<?php echo get_site_url(); ?>/">
    <label>
        <input type="text" placeholder="<?php echo $text_inbox; ?>" name="s" value="">
    </label>
    <button type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#597e8d" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <circle cx="10" cy="10" r="7" />
            <line x1="21" y1="21" x2="15" y2="15" />
        </svg>
    </button>
</form>
<?php } else { ?>
<form method="get" action="<?php echo get_site_url(); ?>/">
    <label>
        <input type="hidden" name="post_type" value="product">
        <input type="text" class="field" name="s" autocomplete="off" placeholder="<?php echo $text_inbox; ?>">
        <input type="submit" style="display:none;" value="">
    </label>
    <button type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#597e8d" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <circle cx="10" cy="10" r="7" />
            <line x1="21" y1="21" x2="15" y2="15" />
        </svg>
    </button>
</form>
<?php } ?>
</div>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_search_box .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
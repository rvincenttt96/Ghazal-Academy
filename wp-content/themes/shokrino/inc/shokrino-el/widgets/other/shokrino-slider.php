<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_slider_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_slider';
    }

    public function get_title() {
        return 'اسلایدر عکس شکرینو';
    }

    public function get_icon() {
        return 'eicon-slides shokrino-el-icon';
    }


    public function get_categories() {
        return ['general_category'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'تنظیم تصاویر', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'style_image1',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/slider-image.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'image_mainslider',
            [
                'label' => __( 'تصویر اسلاید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'url_mainslider', [
                'label' => __( 'لینک اسلاید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
                'input_type' => 'url',
            ]
        );
        $repeater->add_control(
            'target_mainslider',
            [
                'label' => __( 'بازکردن لینک', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '_self',
                'options' => [
                    '_blank'  => __( 'در پنجره جدید بازشود', 'shokrino' ),
                    '_self' => __( 'در همان پنجره باز شود', 'shokrino' ),
                ],
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
                        'url_mainslider' => '',
                        'image_mainslider' => __( 'اسلاید 1', 'shokrino' ),
                    ],
                ],
                'title_field' => 'اسلاید',
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
            'slider_nav',
            [
                'label' => __( 'فلش های بعدی و قبلی', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
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

        <section class="slider-image-shkrn">
            <div id="image-slider-<?php echo $count; ?>" class="swiper image-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper image-slider-shokrino" aria-live="polite">
                    <?php foreach ($mainslider as $slider) { ?>
                        <a class="slider-item-shkrn swiper-slide" <?php if (!empty($slider['url_mainslider'])) { ?> target="<?php echo $slider['target_mainslider']; ?>" href="<?php echo $slider['url_mainslider']; ?>"<?php } ?>>
                            <img src="<?php echo $slider['image_mainslider']['url']; ?>">
                        </a>
                    <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>
        <script>
            var swiper_slider_posts_box = new Swiper("#image-slider-<?php echo $count; ?>", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: <?php if (true == $sliderloop) { echo "true"; } else {echo "false"; } ?>,
                lazy: true,
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
        <?php
        if (is_admin())
        {
            echo '<style>.elementor-widget-shokrino_slider .elementor-widget-container{min-height: 1px;}
            </style>';
        }
    }

    protected function content_template() {}

}
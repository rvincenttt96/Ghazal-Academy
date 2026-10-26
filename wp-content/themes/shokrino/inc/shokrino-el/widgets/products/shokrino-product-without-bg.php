<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_product_without_bg_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_product-without-bg';
    }

    public function get_title() {
        return 'کاروسل دوره ها';
    }

    public function get_icon() {
        return 'eicon-product-related shokrino-el-icon';
    }

    public function get_categories() {
        return ['shkrn_products_el'];
    }

    function get_product_cat(){
        $terms = get_terms( array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ));

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
            foreach ( $terms as $term ) {
                $options[ $term->term_id ] = $term->name;
            }
            return $options;
        }
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'تنظیمات دوره ها', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'style_image1',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/courses-with-bg.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'query_section',
            [
                'label' => __( 'تنظیمات کوئری ( منبع دوره ها )', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'product-count',
            [
                'label' => __( 'چه تعداد دوره نمایش داده شود؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '1',
                'min' => 1,
                'max' => 12,
                'step' => 1,
                'default' => 6,
            ]
        );

        $products_cat = $this->get_product_cat();

        $this->add_control(
            'select_category_product',
            [
                'label' => __( 'دسته بندی را انتخاب کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $products_cat,
                'description' =>'لطفا یک دسته بندی را انتخاب کنید',
                'default'     => array(
                    !empty(array_keys( $products_cat )[0]) ? array_keys( $products_cat )[0] : '',
                    !empty(array_keys( $products_cat )[1]) ? array_keys( $products_cat )[1] : '',
                    !empty(array_keys( $products_cat )[2]) ? array_keys( $products_cat )[2] : '',
                    !empty(array_keys( $products_cat )[3]) ? array_keys( $products_cat )[3] : '',
                    !empty(array_keys( $products_cat )[4]) ? array_keys( $products_cat )[4] : '',
                    !empty(array_keys( $products_cat )[5]) ? array_keys( $products_cat )[5] : '',
                    !empty(array_keys( $products_cat )[6]) ? array_keys( $products_cat )[6] : '',
                    !empty(array_keys( $products_cat )[7]) ? array_keys( $products_cat )[7] : '',
                    !empty(array_keys( $products_cat )[8]) ? array_keys( $products_cat )[8] : '',
                    !empty(array_keys( $products_cat )[9]) ? array_keys( $products_cat )[9] : '',
                    !empty(array_keys( $products_cat )[10]) ? array_keys( $products_cat )[10] : ''
                )
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'slider_section',
            [
                'label' => __( 'تنظیمات کاروسل', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'product_per_page_d',
            [
                'label' => esc_html__( 'تعداد دوره در هر صفحه ( دسکتاپ )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'product_per_page_t',
            [
                'label' => esc_html__( 'تعداد دوره در هر صفحه ( تبلت )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'product_per_page_m',
            [
                'label' => esc_html__( 'تعداد دوره در هر صفحه ( موبایل )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 1,
                'description' => 'ممکن است تغییرات در اینجا قابل مشاهده نباشد ، اما پس از ذخیره سازی در صفحه مورد نظر نمایش داده خواهد شد',

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

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__( 'تنظیمات استایل', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'nav_dots_color',
            [
                'label' => esc_html__( 'رنگ نقاط زیر اسلایدر', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-dots .active span' => 'background: {{VALUE}} !important',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $product_per_page_d = $settings['product_per_page_d'];
        $product_per_page_t = $settings['product_per_page_t'];
        $product_per_page_m = $settings['product_per_page_m'];
        $sliderloop = $settings['slider_loop'];
        $slidernav = $settings['slider_nav'];
        $sliderdots = $settings['slider_dots'];
        $sliderautoplay = $settings['slider_autoplay'];
        $sliderautoplaytime = $settings['slider_autoplaytime'];
        $sliderautoplayhover = $settings['slider_autoplayhover'];
        $autoplayspeed = $settings['slider_autoplayspeed'];
        $count = rand(0, 100000); ?>

            <div id="course-slider-<?php echo $count; ?>" class="swiper posts-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden course-box-shkrn">
                <div class="swiper-wrapper post-slider-shokrino" aria-live="polite">
                    <?php
                    $courses = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => $settings['product-count'],
                        'tax_query' => [[
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => $settings['select_category_product'],
                        ]],
                    ));
                    if ($courses->have_posts()) {
                        while ($courses->have_posts()) : $courses->the_post();
                            make_product_item_shkrn('slider');
                        endwhile;
                    } else {
                        echo "<p>";
                        esc_html_e('محصولی پیدا نشد!', 'shokrino');
                        echo "</p>";
                    }
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="swiper-button-prev swiper-btn-style-shkrn"></div>
                <div class="swiper-button-next swiper-btn-style-shkrn"></div>
                <div class="swiper-pagination"></div>
            </div>
        <style>
            <?php if ($slidernav != "true") { ?>
            #course-slider-<?php echo $count; ?> .swiper-btn-style-shkrn {
                display: none;
            }
            <?php } ?>
            <?php if ($sliderdots != "true") { ?>
            #course-slider-<?php echo $count; ?> .swiper-pagination {
                display: none;
            }
            <?php } ?>
        </style>
        <script>
            var swiper3 = new Swiper('#course-slider-<?php echo $count; ?>', {
                breakpoints: {
                    0: {
                        slidesPerView: <?php echo $product_per_page_m; ?>,
                    },
                    600: {
                        slidesPerView: <?php echo $product_per_page_t; ?>,
                    },
                    1000: {
                        slidesPerView: <?php echo $product_per_page_d; ?>,
                    }
                },
                spaceBetween: 10,
                loop: <?php if (true == $sliderloop) { echo "true"; } else {echo "false"; } ?>,
                lazy: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
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
            echo '<style>.elementor-widget-shokrino_product-without-bg .elementor-widget-container{min-height: 1px;}
            </style>';
        }
    }

    protected function content_template() {}

}
<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_product_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_products';
    }

    public function get_title() {
        return 'کاروسل دوره ها باکس';
    }

    public function get_icon() {
        return 'eicon-product-related shokrino-el-icon';
    }

    public function get_categories() {
        return ['shkrn_products_el'];
    }

    function get_product_cat() {
        $terms = get_terms( array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ));
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
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

        $this->add_control(
            'product_title',
            [
                'label' => esc_html__( 'عنوان بخش', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'عنوان جدید را برای تغییر عنوان این المان وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'product_subtitle',
            [
                'label' => esc_html__( 'متن زیر عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'متنی که زیر عنوان نمایش داده میشود را وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'hr1-pro',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'more_btn',
            [
                'label' => __( 'دکمه "مشاهده همه" نمایش داده شود؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'بله', 'shokrino' ),
                'label_off' => esc_html__( 'خیر', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'more_btn_text',
            [
                'label' => esc_html__( 'متن دکمه "مشاهده همه"', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'متن را وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'more_btn_link',
            [
                'label' => __( 'لینک دکمه مشاهده همه', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__( 'پیشفرض به صفحه دوره ها متصل است', 'shokrino' ),
                'label_block' => true,
                'input_type' => 'url',
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
            'border_color',
            [
                'label' => esc_html__( 'رنگ خط کناری عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #box-title-posts' => 'border-right: 5px solid {{VALUE}} !important',
                ],
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
        $tv_title = $settings['product_title'];
        $product_subtitle = $settings['product_subtitle'];
        $more_btn = $settings['more_btn'];
        $more_btn_text = $settings['more_btn_text'];
        $more_btn_link = $settings['more_btn_link'];
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


        <section class="posts-section-shkrn flex-center">
            <div class="posts-section-header flex-center">
                <div class="posts-title-shkrn flex-center">
                    <h2><?php if (!empty($tv_title)) { echo $tv_title; } else { ?> <?php esc_html_e('آخرین دوره ها', 'shokrino' ); ?> <?php } ?></h2>
                    <h5><?php if (!empty($product_subtitle)) { echo $product_subtitle; } else { ?> <?php esc_html_e('یادگیری یک مهارت پول ساز!', 'shokrino' ); ?> <?php } ?></h5>
                </div>
                <?php if (true == $more_btn) { ?>
                    <a class="more-button-section-shkrn flex-center" href="<?php if (!empty($more_btn_link)) { echo $more_btn_link; } else { echo get_post_type_archive_link('product'); } ?>">
                        <?php if (!empty($more_btn_text)) { echo $more_btn_text; } else { esc_html_e('View All ', 'shokrino' ); } ?>
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" style="margin-bottom: 3px;" fill="#fff"  viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M224 239l135.61-136a23.78 23.78 0 0 1 33.8 0L416 125.64a23.94 23.94 0 0 1 0 33.89l-96.16 96.37L416 352.27a23.94 23.94 0 0 1 0 33.89L393.53 409a23.78 23.78 0 0 1-33.8 0L224.12 273a23.94 23.94 0 0 1-.1-34z" class="fa-secondary"/><path d="M32.11 239l135.55-136a23.77 23.77 0 0 1 33.79 0L224 125.74a23.94 23.94 0 0 1 0 33.89L127.89 256l96 96.47a23.94 23.94 0 0 1 0 33.89L201.35 409a23.77 23.77 0 0 1-33.79 0L32 273a24 24 0 0 1 .11-34z" class="fa-primary"/></svg>
                    </a>
                <?php } ?>
            </div>
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
        </section>
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
            echo '<style>.elementor-widget-shokrino_products .elementor-widget-container{min-height: 1px;}
            </style>';
        }
    }

    protected function content_template() {}

}
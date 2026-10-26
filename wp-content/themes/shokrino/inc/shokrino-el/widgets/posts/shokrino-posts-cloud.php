<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_posts_cloud extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'shokrino_posts_cloud';
    }

    public function get_title()
    {
        return 'کاروسل پست ها مینیمال';
    }

    public function get_icon()
    {
        return 'eicon-posts-carousel shokrino-el-icon';
    }


    public function get_categories()
    {
        return ['shkrn_posts_el'];
    }

    public function rand_number_shkrn()
    {
        return rand(0, 100000);
    }

    public function get_post_types()
    {
        $args = array(
            'public' => true
        );
        $output = 'objects';
        $operator = 'and';
        $post_types = get_post_types($args, $output, $operator);

        if (!empty($post_types) && !is_wp_error($post_types)) {
            foreach ($post_types as $post_type) {
                $options[$post_type->name] = $post_type->label;
            }
            return $options;
        }
    }

    public function get_post_cat($post_type)
    {
        $terms = get_terms(array(
            'taxonomy' => get_object_taxonomies(array('post_type' => $post_type)),
            'hide_empty' => false,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
            return $options;
        }
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
            'show-wrapper-box',
            [
                'label' => __( 'نمایش باکس دور پست ها', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'post_title',
            [
                'label' => esc_html__( 'عنوان بخش', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'عنوان جدید را برای تغییر عنوان این المان وارد کنید', 'shokrino' ),
                'condition' => [
                    'show-wrapper-box' => 'true',
                ],
            ]
        );

        $this->add_control(
            'post_subtitle',
            [
                'label' => esc_html__( 'متن زیر عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'متنی که زیر عنوان نمایش داده میشود را وارد کنید', 'shokrino' ),
                'condition' => [
                    'show-wrapper-box' => 'true',
                ],
            ]
        );

        $this->add_control(
            'hr1',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
                'condition' => [
                    'show-wrapper-box' => 'true',
                ],
            ]
        );

        $this->add_control(
            'show_more_btn',
            [
                'label' => __( 'نمایش کلید "بیشتر بخوانید"', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'more_btn_custom_text',
            [
                'label' => esc_html__( 'متن کلید زیر پست ها', 'shokrino' ),
                'description' => esc_html__( 'در صورت خالی بودن "بیشتر بخوانید" جایگزین میشود', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'بیشتر بخوانید', 'shokrino' ),
                'placeholder' => esc_html__( 'متن خود را اینجا بنویسید...', 'shokrino' ),
                'condition' => [
                    'show_more_btn' => 'true',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'query_section',
            [
                'label' => __( 'تنظیمات کوئری ( منبع پست ها )', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'posts-count',
            [
                'label' => __( 'چه تعداد پست نمایش داده شود؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '1',
                'min' => 1,
                'max' => 12,
                'step' => 1,
                'default' => 6,
            ]
        );

        $this->add_control(
            'select_post_type',
            [
                'label' => esc_html__( 'پست تایپ را را انتخاب کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => false,
                'options' => $this->get_post_types(),
                'description' => 'اگر خالی باشد از نوشته ها خوانده میشود',
                'default' => 'post',
            ]
        );

        $this->add_control(
            'select_category',
            [
                'label' => esc_html__( 'دسته بندی پست ها را انتخاب کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_post_cat('post'),
                'description' => 'اگر خالی باشد از همه دسته بندی ها خوانده میشود',
                'condition' => [
                    'select_post_type' => 'post',
                ],
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
            'post_per_page_d',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( دسکتاپ )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 4,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'post_per_page_t',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( تبلت )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 3,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'post_per_page_m',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( موبایل )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 2,
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
                'label' => __( 'فلش های قبلی و بعدی اسلایدر', 'shokrino' ),
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
            'border-radius-items',
            [
                'label' => esc_html__( 'گرد بودن حاشیه تصویر پست', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .box-post > a > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'height-img-items',
            [
                'label' => esc_html__( 'حداکثر ارتفاع تصاویر پست ها', 'shokrino' ),
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
                    'size' => 140,
                ],
                'selectors' => [
                    '{{WRAPPER}} .box-post > a > img' => 'max-height: {{SIZE}}{{UNIT}} !important;height: 100%;margin: auto;width: auto;',
                ],
            ]
        );

        $this->add_control(
            'border-radius-box-posts',
            [
                'label' => esc_html__( 'گرد بودن حاشیه باکس های عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .posts-slider .box-post-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'margin-right-items',
            [
                'label' => esc_html__( 'فاصله بین باکس ها', 'shokrino' ),
                'description' => esc_html__( 'بعد از ذخیره سازی نمایش داده میشود', 'shokrino' ),
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
                    'size' => 30,
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow_items',
                'label' => esc_html__( 'سایه باکس ها', 'shokrino' ),
                'selector' => '{{WRAPPER}} .posts-slider .box-post-title',
            ]
        );

        $this->add_control(
            'dots-color-slider',
            [
                'label' => esc_html__( 'رنگ نقطه های اسلایدر فعال', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}} !important',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $tv_title = $settings['post_title'];
        $post_subtitle = $settings['post_subtitle'];
        $show_wrapper_box = $settings['show-wrapper-box'];
        $margin_right_items = $settings['margin-right-items']['size'];
        $show_more_btn = $settings['show_more_btn'];
        $more_btn_custom_text = $settings['more_btn_custom_text'];
        $post_per_page_d = $settings['post_per_page_d'];
        $post_per_page_t = $settings['post_per_page_t'];
        $post_per_page_m = $settings['post_per_page_m'];
        $sliderloop = $settings['slider_loop'];
        $sliderautoplay = $settings['slider_autoplay'];
        $sliderautoplaytime = $settings['slider_autoplaytime'];
        $sliderautoplayhover = $settings['slider_autoplayhover'];
        $autoplayspeed = $settings['slider_autoplayspeed'];
        $count = rand(0, 100000); ?>

        <?php if ($show_wrapper_box == 'true') { ?>
        <section class="posts-section-shkrn flex-center">
            <div class="posts-section-header flex-center">
                <div class="posts-title-shkrn flex-center">
                    <h2><?php if (!empty($tv_title)) { echo $tv_title; } else { ?> <?php esc_html_e('Last Posts', 'shokrino' ); ?> <?php } ?></h2>
                    <h5><?php if (!empty($post_subtitle)) { echo $post_subtitle; } else { ?> <?php esc_html_e('Important tips you should know!', 'shokrino' ); ?> <?php } ?></h5>
                </div>
            </div>
            <?php } ?>
            <div class="post-slider-<?php echo $count; ?> swiper posts-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper post-slider-shokrino" aria-live="polite">

                <?php
                $posts_query = new WP_Query(array(
                    'post_type' => $settings['select_post_type'],
                    'posts_per_page' => $settings['posts-count'],
                    'cat' => $settings['select_category'],
                ));
                if ($posts_query->have_posts()) {
                    while ($posts_query->have_posts()) : $posts_query->the_post(); ?>

                        <div class="box-post swiper-slide">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail(optimize_img_shkrn('posts'));
                                }
                                else {
                                    ?> <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>"> <?php
                                }
                                ?>
                                <div class="box-post-title">
                                    <h2><?php the_title(); ?></h2>
                                    <?php if ($show_more_btn) { ?>
                                        <div class="btn-more-box">
                                            <div class="btn-more">
                                                <?php if (!empty($text_readmore)) { echo $text_readmore; } else { ?> <?php echo $more_btn_custom_text; ?> <?php } ?>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                                <line x1="5" y1="12" x2="11" y2="18" />
                                                <line x1="5" y1="12" x2="11" y2="6" />
                                            </svg>
                                        </div>
                                    <?php } ?>
                                </div>
                            </a>
                        </div>

                    <?php
                    endwhile;
                }
                else {
                    echo "<p>";
                    esc_html_e('پستی پیدا نشد!', 'shokrino');
                    echo "</p>";
                }
                wp_reset_postdata();
                ?>

                </div>
                <div class="swiper-btn-style-shkrn swiper-button-prev"></div>
                <div class="swiper-btn-style-shkrn swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
            <?php
            if ($show_wrapper_box == 'true') { ?>
        </section>
    <?php } ?>

        <script>
            var swiper_slider_posts = new Swiper(".post-slider-<?php echo $count; ?>", {
                breakpoints: {
                    0: {
                        slidesPerView: <?php echo $post_per_page_m; ?>,
                    },
                    600: {
                        slidesPerView: <?php echo $post_per_page_t; ?>,
                    },
                    1000: {
                        slidesPerView: <?php echo $post_per_page_d; ?>,
                    }
                },
                spaceBetween: <?php echo $margin_right_items; ?>,
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

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_posts_cloud .elementor-widget-container{min-height: 1px;height: fit-content;}
            </style>';
        }

    }

    protected function content_template() {}

}
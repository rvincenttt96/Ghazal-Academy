<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_posts_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_posts';
    }

    public function get_title() {
        return 'کاروسل پست ها باکس';
    }

    public function get_icon() {
        return 'eicon-posts-carousel shokrino-el-icon';
    }

    public function get_categories() {
        return ['shkrn_posts_el'];
    }

    public function rand_number_shkrn() {
        return rand(0, 100000);
    }

    public function get_post_types(){
        $args = array(
            'public'   => true
        );
        $output = 'objects';
        $operator = 'and';
        $post_types = get_post_types( $args, $output, $operator );

        if ( ! empty( $post_types ) && ! is_wp_error( $post_types ) ){
            foreach ( $post_types  as $post_type ) {
                $options[ $post_type->name ] = $post_type->label;
            }
            return $options;
        }
    }

    public function get_post_cat($post_type){
        $terms = get_terms( array(
            'taxonomy' => get_object_taxonomies( array( 'post_type' => $post_type ) ),
            'hide_empty' => false,
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
                'label' => __( 'تنظیمات پست ها', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'style_image',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/posts-with-bg.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );

        $this->add_control(
            'post_title',
            [
                'label' => esc_html__( 'عنوان بخش', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'عنوان جدید را برای تغییر عنوان این المان وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'post_subtitle',
            [
                'label' => esc_html__( 'متن زیر عنوان', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'متنی که زیر عنوان نمایش داده میشود را وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'hr1',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'show-excerpt',
            [
                'label' => __( 'نمایش توضیحات کوتاه پست', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_readmore',
            [
                'label' => __( 'نمایش دکمه "بیشتر بخوانید"', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'نمایش', 'shokrino' ),
                'label_off' => esc_html__( 'مخفی', 'shokrino' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'text_readmore',
            [
                'label' => esc_html__( 'متن دکمه "بیشتر بخوانید"', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'متن را وارد کنید', 'shokrino' ),
            ]
        );

        $this->add_control(
            'hr2',
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
                'placeholder' => esc_html__( 'پیشفرض به صفحه پست ها متصل است', 'shokrino' ),
                'label_block' => true,
                'input_type' => 'url',
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
                'label' => __( 'تنظیمات کاروسل', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page_d',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( دسکتاپ )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'posts_per_page_t',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( تبلت )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'posts_per_page_m',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( موبایل )', 'shokrino' ),
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
                    '{{WRAPPER}} .posts-title-shkrn' => 'border-right-color: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'nav_dots_color',
            [
                'label' => esc_html__( 'رنگ فلش قبلی و بعدی و نقاط زیر اسلایدر', 'shokrino' ),
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
        $more_btn = $settings['more_btn'];
        $more_btn_text = $settings['more_btn_text'];
        $more_btn_link = $settings['more_btn_link'];
        $posts_per_page_d = $settings['posts_per_page_d'];
        $posts_per_page_t = $settings['posts_per_page_t'];
        $posts_per_page_m = $settings['posts_per_page_m'];
        $sliderloop = $settings['slider_loop'];
        $slidernav = $settings['slider_nav'];
        $sliderdots = $settings['slider_dots'];
        $sliderautoplay = $settings['slider_autoplay'];
        $sliderautoplaytime = $settings['slider_autoplaytime'];
        $sliderautoplayhover = $settings['slider_autoplayhover'];
        $autoplayspeed = $settings['slider_autoplayspeed'];
        $show_excerpt = $settings['show-excerpt'];
        $show_readmore = $settings['show_readmore'];
        $text_readmore = $settings['text_readmore'];
        $count = $this->rand_number_shkrn(); ?>

        <section class="posts-section-shkrn flex-center">
            <div class="posts-section-header flex-center">
                <div class="posts-title-shkrn flex-center">
                    <h2><?php if (!empty($tv_title)) { echo $tv_title; } else { ?> <?php esc_html_e('Last Posts', 'shokrino' ); ?> <?php } ?></h2>
                    <h5><?php if (!empty($post_subtitle)) { echo $post_subtitle; } else { ?> <?php esc_html_e('Important tips you should know!', 'shokrino' ); ?> <?php } ?></h5>
                </div>
                <?php if (true == $more_btn) { ?>
                <a class="more-button-section-shkrn flex-center" href="<?php if (!empty($more_btn_link)) { echo $more_btn_link; } else { echo get_post_type_archive_link($settings['select_post_type']); } ?>">
                        <?php if (!empty($more_btn_text)) { echo $more_btn_text; } else { esc_html_e('View All ', 'shokrino' ); } ?>
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" style="margin-bottom: 3px;" fill="#fff"  viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M224 239l135.61-136a23.78 23.78 0 0 1 33.8 0L416 125.64a23.94 23.94 0 0 1 0 33.89l-96.16 96.37L416 352.27a23.94 23.94 0 0 1 0 33.89L393.53 409a23.78 23.78 0 0 1-33.8 0L224.12 273a23.94 23.94 0 0 1-.1-34z" class="fa-secondary"/><path d="M32.11 239l135.55-136a23.77 23.77 0 0 1 33.79 0L224 125.74a23.94 23.94 0 0 1 0 33.89L127.89 256l96 96.47a23.94 23.94 0 0 1 0 33.89L201.35 409a23.77 23.77 0 0 1-33.79 0L32 273a24 24 0 0 1 .11-34z" class="fa-primary"/></svg>
                </a>
                <?php } ?>
            </div>
            <div class="post-slider-<?php echo $count; ?> swiper posts-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper post-slider-shokrino" aria-live="polite">

        <?php
        $posts_query = new WP_Query(array(
            'post_type' => $settings['select_post_type'],
            'posts_per_page' => $settings['posts-count'],
            'cat' => $settings['select_category'],
        ));
        if ($posts_query->have_posts()){
            while ($posts_query->have_posts()) : $posts_query->the_post(); ?>

                            <div class="box-article swiper-slide">
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        if ($posts_per_page_d == 1) {
                                            the_post_thumbnail(optimize_img_shkrn('posts_1'));
                                        } elseif ($posts_per_page_d == 2) {
                                            the_post_thumbnail(optimize_img_shkrn('posts_2'));
                                        } elseif ($posts_per_page_d == 3) {
                                            the_post_thumbnail(optimize_img_shkrn('posts_3'));
                                        } elseif ($posts_per_page_d == 4) {
                                            the_post_thumbnail(optimize_img_shkrn('posts_4'));
                                        } else {
                                            the_post_thumbnail(optimize_img_shkrn('posts_2'));
                                        }
                                    }
                                    else {
                                        ?> <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>"> <?php
                                    }
                                    ?>
                                    <h2><?php the_title(); ?></h2>
                                    <?php if(true == ($show_excerpt)) { ?>
                                    <p><?php echo shokrino_excerpt(get_the_excerpt()); ?></p>
                                    <?php } ?>
                                    <?php if(true == ($show_readmore)) { ?>
                                    <div class="btn-more"><?php if (!empty($text_readmore)) { echo $text_readmore; } else { ?> <?php esc_html_e('بیشتر بخوانید', 'shokrino' ); ?> <?php } ?></div>
                                    <?php } ?>
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
        </section>
        <style>
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide {
                width: auto;
                flex-shrink: 0;
                display: block;
                height: 100%;
                max-height: 100%;
            }
            .post-slider-<?php echo $count; ?> .swiper-pagination-bullet {
                width: 8px;
                height: 8px;
            }
            .post-slider-<?php echo $count; ?> .swiper-btn-style-shkrn {
                padding: 5px;
                border-radius: 9px;
                background: var(--shkrn-var-color-light-dark-middle);
                box-shadow: 0px 2px 12px -3px #ddd;
                transition: all .3s ease-in-out;
                height: 60px !important;
                min-width: 45px;
                color: var(--shkrn-var-color-light-dark-text) !important;
            }
            .post-slider-<?php echo $count; ?> .swiper-btn-style-shkrn::after {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .post-slider-<?php echo $count; ?> .swiper-button-next,.post-slider-<?php echo $count; ?> .swiper-button-prev {
                position: absolute;
                top: 50%;
                z-index: 10;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            <?php if ($slidernav != "true") { ?>
            .post-slider-<?php echo $count; ?> .swiper-btn-style-shkrn {
                display: none;
            }
            <?php } ?>
            <?php if ($sliderdots != "true") { ?>
            .post-slider-<?php echo $count; ?> .swiper-pagination {
                display: none;
            }
            <?php } ?>
        </style>
        <script>
            var swiper_slider_posts_box = new Swiper(".post-slider-<?php echo $count; ?>", {
                breakpoints: {
                    0: {
                        slidesPerView: <?php echo $posts_per_page_m; ?>,
                    },
                    600: {
                        slidesPerView: <?php echo $posts_per_page_t; ?>,
                    },
                    1000: {
                        slidesPerView: <?php echo $posts_per_page_d; ?>,
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
            echo '<style>.elementor-widget-shokrino_posts .elementor-widget-container{min-height: 1px;}
            </style>';
        }
    }

    protected function content_template() {}

}
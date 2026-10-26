<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_posts_centered extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_posts_centered';
    }

    public function get_title() {
        return 'کاروسل پست ها سه بعدی';
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
            'query_section',
            [
                'label' => __( 'تنظیمات کوئری ( منبع پست ها )', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'style_image1',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/posts-without-bg.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
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
                'step' => 2,
                'default' => 5,
            ]
        );

        $this->add_control(
            'posts_per_page_t',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( تبلت )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 3,
                'step' => 2,
                'default' => 3,
            ]
        );

        $this->add_control(
            'posts_per_page_m',
            [
                'label' => esc_html__( 'تعداد پست در هر صفحه ( موبایل )', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'placeholder' => '0',
                'min' => 1,
                'max' => 1,
                'step' => 2,
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
        $show_wrapper_box = $settings['show-wrapper-box'];
        $count = $this->rand_number_shkrn(); ?>

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
                } else {
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

        <style>
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide {
                opacity: 0;
                transition: all 0.05s ease-in-out;
            }
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide-duplicate-prev {
                opacity: 0 !important;
            }
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide-active {
                transform: scale(1);
                z-index: 2;
                opacity: 1;
                transition: all 0.15s ease-in-out;
                box-shadow: 0px 3px 20px 5px rgba(61, 130, 179, 0.1);
            }
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide-prev,.post-slider-<?php echo $count; ?> .box-article.swiper-slide-next {
                transform: scale(0.9);
                z-index: 1;
                opacity: 0.8;
                transition: all 0.15s ease-in-out;
            }
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide-next + div:not(.swiper-slide-active),
            .post-slider-<?php echo $count; ?> .box-article.swiper-slide-duplicate-next ~ div:not(.swiper-slide-active, .swiper-slide-next, .swiper-slide-prev) {
                transform: scale(0.8);
                z-index: 0;
                opacity: 0.6;
                transition: all 0.15s ease-in-out;
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
            <?php if (false == ($slidernav)) { ?>
            .post-slider-<?php echo $count; ?> .swiper-btn-style-shkrn {
                display: none;
            }
            <?php } ?>
            <?php if (false == ($sliderdots)) { ?>
            .post-slider-<?php echo $count; ?> .swiper-pagination {
                display: none;
            }
            <?php } ?>
        </style>
        <script>
            var swiper_slider_posts = new Swiper(".post-slider-<?php echo $count; ?>", {
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
                centeredSlides: true,
                spaceBetween: -55,
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
            echo '<style>.elementor-widget-shokrino_posts_centered .elementor-widget-container{min-height: 1px;}
            </style>';
        }
    }

    protected function content_template() {}

}
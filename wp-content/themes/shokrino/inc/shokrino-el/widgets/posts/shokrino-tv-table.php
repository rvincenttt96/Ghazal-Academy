<?php

class Elementor_shokrino_tv_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_tv';
    }

    public function get_title() {
        return 'جدول آخرین ویدیو ها';
    }

    public function get_icon() {
        return 'eicon-video-playlist shokrino-el-icon';
    }

    public function get_categories() {
        return ['shkrn_posts_el'];
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
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/videos-with-bg.jpg">',
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
                'default' => 'true',
            ]
        );

        $this->add_control(
            'tv_title',
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
            'tv_subtitle',
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
            'hr-tv',
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
                'placeholder' => esc_html__( 'پیشفرض به صفحه ویدیو ها متصل است', 'shokrino' ),
                'label_block' => true,
                'input_type' => 'url',
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

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $tv_title = $settings['tv_title'];
        $tv_subtitle = $settings['tv_subtitle'];
        $more_btn = $settings['more_btn'];
        $more_btn_text = $settings['more_btn_text'];
        $more_btn_link = $settings['more_btn_link'];
        $show_wrapper_box = $settings['show-wrapper-box'];
        ?>

        <?php if ($show_wrapper_box == 'true') { ?>
            <section class="posts-section-shkrn flex-center">
            <div class="posts-section-header flex-center">
                <div class="posts-title-shkrn flex-center">
                    <h2><?php if (!empty($tv_title)) { echo $tv_title; } else { esc_html_e('آموزش های رایگان', 'shokrino' ); } ?></h2>
                    <h5><?php if (!empty($tv_subtitle)) { echo $tv_subtitle; } else { esc_html_e('پلی لیست ویدیو های شکرینو', 'shokrino' ); } ?></h5>
                </div>
                <?php if (true == $more_btn) { ?>
                    <a class="more-button-section-shkrn flex-center" href="<?php if (!empty($more_btn_link)) { echo $more_btn_link; } else { echo get_post_type_archive_link('tv'); } ?>">
                        <?php if (!empty($more_btn_text)) { echo $more_btn_text; } else { esc_html_e('View All ', 'shokrino' ); } ?>
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" style="margin-bottom: 3px;" fill="#fff"  viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M224 239l135.61-136a23.78 23.78 0 0 1 33.8 0L416 125.64a23.94 23.94 0 0 1 0 33.89l-96.16 96.37L416 352.27a23.94 23.94 0 0 1 0 33.89L393.53 409a23.78 23.78 0 0 1-33.8 0L224.12 273a23.94 23.94 0 0 1-.1-34z" class="fa-secondary"/><path d="M32.11 239l135.55-136a23.77 23.77 0 0 1 33.79 0L224 125.74a23.94 23.94 0 0 1 0 33.89L127.89 256l96 96.47a23.94 23.94 0 0 1 0 33.89L201.35 409a23.77 23.77 0 0 1-33.79 0L32 273a24 24 0 0 1 .11-34z" class="fa-primary"/></svg>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="main-box-tile-posts-shkrn flex-center">
            <div class="col-right-tile-shkrn flex-center">
                    <?php
                    $video_tv = new WP_Query(array(
                        'post_type' => 'tv',
                        'posts_per_page' => 1,
                    ));
                    if ($video_tv->have_posts()){
                        while ($video_tv->have_posts()) : $video_tv->the_post(); ?>
                            <div class="first-post-tile-shkrn">
                                <a href="<?php the_permalink(); ?>">
                                    <figure>
                                        <?php
                                        if (has_post_thumbnail()) {
                                            the_post_thumbnail('tv_cover');
                                        }
                                        else {
                                            ?> <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>"> <?php
                                        }
                                        ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="var(--shkrn-var-color-light-dark-high)" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm115.7 272l-176 101c-15.8 8.8-35.7-2.5-35.7-21V152c0-18.4 19.8-29.8 35.7-21l176 107c16.4 9.2 16.4 32.9 0 42z" class="fa-secondary"/><path d="M371.7 280l-176 101c-15.8 8.8-35.7-2.5-35.7-21V152c0-18.4 19.8-29.8 35.7-21l176 107c16.4 9.2 16.4 32.9 0 42z" class="fa-primary"/></svg>
                                    </figure>
                                </a>
                            </div>
                        <?php
                        endwhile;
                    }
                    else {
                        echo "<p>";
                        esc_html_e('ویدیویی پیدا نشد!', 'shokrino');
                        echo "</p>";
                    }
                    wp_reset_postdata();
                    ?>

                </div>
            <div class="col-left-tile-shkrn">

                    <?php
                    $video_tv = new WP_Query(array(
                        'post_type' => 'tv',
                        'posts_per_page' => 2,
                        'offset' => 1,

                    ));
                    if ($video_tv->have_posts()){
                        while ($video_tv->have_posts()) : $video_tv->the_post(); ?>
                            <div class="other-posts-tile-shkrn">
                                <a href="<?php the_permalink(); ?>">
                                    <figure>
                                        <?php
                                        if (has_post_thumbnail()) {
                                            the_post_thumbnail('tv_cover_M');
                                        }
                                        else {
                                            ?> <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>"> <?php
                                        }
                                        ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="var(--shkrn-var-color-light-dark-high)" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm115.7 272l-176 101c-15.8 8.8-35.7-2.5-35.7-21V152c0-18.4 19.8-29.8 35.7-21l176 107c16.4 9.2 16.4 32.9 0 42z" class="fa-secondary"/><path d="M371.7 280l-176 101c-15.8 8.8-35.7-2.5-35.7-21V152c0-18.4 19.8-29.8 35.7-21l176 107c16.4 9.2 16.4 32.9 0 42z" class="fa-primary"/></svg>
                                    </figure>
                                </a>
                            </div>
                        <?php
                        endwhile;
                    }
                    else {
                        echo "<p>";
                        esc_html_e('ویدیویی پیدا نشد!', 'shokrino');
                        echo "</p>";
                    }
                    wp_reset_postdata();
                    ?>

                </div>
        </div>
        <?php
        if ($show_wrapper_box == 'true') { ?>
            </section>
        <?php }

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_tv .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
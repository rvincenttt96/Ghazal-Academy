<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_post_table extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_post_table';
    }

    public function get_title() {
        return 'جدول آخرین پست ها';
    }

    public function get_icon() {
        return 'eicon-posts-group shokrino-el-icon';
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
            'style_image',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/videos-without-bg.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __( 'نمایش عنوان روی عکس', 'shokrino' ),
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
        $show_title = $settings['show_title'];
        ?>

        <div class="main-box-tile-posts-shkrn flex-center">

                <div class="col-right-tile-shkrn flex-center">

                    <?php
                    $video_tv = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 1,
                    ));
                    if ($video_tv->have_posts()) {
                        while ($video_tv->have_posts()) : $video_tv->the_post(); ?>
                            <div class="first-post-tile-shkrn">
                                <a href="<?php the_permalink(); ?>">
                                    <figure>
                                            <?php if (has_post_thumbnail()) {
                                                the_post_thumbnail('tv_cover');
                                            } else { ?>
                                                <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                                            <?php } ?>
                                            <?php if ($show_title == "true") { ?>
                                                <div class="posts-table-inner-main-shkrn">
                                                    <h2><?php the_title(); ?></h2>
                                                </div>
                                            <?php } ?>
                                    </figure>
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

                <div class="col-left-tile-shkrn">

                    <?php
                    $video_tv = new WP_Query(array(
                        'post_type' => 'post',
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
                                        <?php if ($show_title == "true") { ?>
                                            <div class="posts-table-inner-main-shkrn">
                                                <h2><?php the_title(); ?></h2>
                                            </div>
                                        <?php } ?>
                                    </figure>
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

            </div>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_posts_grid .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
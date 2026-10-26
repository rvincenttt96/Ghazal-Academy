<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_posts_grid extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_posts_grid';
    }

    public function get_title() {
        return 'آرشیو پست ها';
    }

    public function get_icon() {
        return 'eicon-posts-grid shokrino-el-icon';
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

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        ?>

        <div class="archive-grid-posts-shkrn shkrn-grid-columns-<?php echo shkrn_options('posts_columns'); ?>">
            <?php
            $posts_query = new WP_Query(array(
                'post_type' => $settings['select_post_type'],
                'posts_per_page' => $settings['posts-count'],
                'cat' => $settings['select_category'],
            ));
            if ($posts_query->have_posts()){
                while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                    <article class="box-article">
                        <a href="<?php esc_url(the_permalink()); ?>">
                            <?php if (has_post_thumbnail()) {
                                the_post_thumbnail(optimize_img_shkrn('posts_2'));
                            } else { ?>
                                <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                            <?php } ?>
                            <h2><?php the_title(); ?></h2>
                            <p><?php if ($blog_desc_text == "true" or empty($blog_desc_text) ) {
                                    echo shokrino_excerpt(get_the_excerpt());
                                } ?>
                            </p>
                            <?php if ($blog_btn_more == "true" or empty($blog_btn_more) ) { ?>
                                <div class="btn-more">
                                    <?php
                                    if (!empty($blog_btn_more_text) ) { echo $blog_btn_more_text; } else { esc_html_e('Read more', 'shokrino' ); } ?>
                                </div>
                            <?php } else { ?>
                                <style>
                                    .archive-grid-posts-shkrn .box-article {
                                        padding-bottom: 8px;
                                    }
                                </style>
                            <?php } ?>
                        </a>
                    </article>
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
        <div class="pagination">
            <?php echo paginate_links(array(
                'prev_text' => __( 'Previous' , 'shokrino' ),
                'next_text' => __( 'Next' , 'shokrino' ),
            )); ?>
        </div>

        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_posts_grid .elementor-widget-container{min-height: 1px;}
            </style>';
        }

    }

    protected function content_template() {}

}
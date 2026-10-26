<?php
defined( 'ABSPATH' ) || exit;
get_header();
$blog_desc_text = shkrn_options('blog_desc_text');
$blog_btn_more = shkrn_options('blog_btn_more');
$blog_btn_more_text = shkrn_options('blog_btn_more_text');
$blog_show_breadcrumb = shkrn_options('blog_show_breadcrumb');
if ($blog_show_breadcrumb == "false") { ?>
    <style>
        .breadcrumb-box-shkrn {
            display: none !important;
        }
        .shkrn-archive-title-desc {
            margin-bottom: 20px;
        }
    </style>
<?php } ?>
<div class="container">
    <div class="single-page-shkrn teacher-page-shkrn">
        <div class="single-page-main-part">
            <div class="archive-grid-posts-shkrn shkrn-grid-columns-<?php echo shkrn_options('posts_columns'); ?>">
                <?php if (have_posts()) {
                    while (have_posts()) : the_post(); ?>
                        <a href="<?php esc_url(the_permalink()); ?>" class="box-teacher-archive">
                            <div class="image-teacher">
                                <?php if (has_post_thumbnail()) {
                                    the_post_thumbnail();
                                } else { ?>
                                    <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/sample-teacher.jpg'; ?>">
                                <?php } ?>
                            </div>
                            <h2><?php the_title(); ?></h2>
                        </a>
                    <?php
                    endwhile;
                } else { ?>
                    <p class="not-found"><?php esc_html_e('Nothing found', 'shokrino' ); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="more-button-section-shkrn flex-center"><?php esc_html_e('Back to home', 'shokrino' ); ?></a>
                <?php } ?>
            </div>

                <?php echo paginate_links(array(
                    'before_page_number' => '<div class="pagination">',
                    'prev_text' => __( 'Previous' , 'shokrino' ),
                    'next_text' => __( 'Next' , 'shokrino' ),
                    'before_page_number' => '</div>'
                )); ?>

        </div>
    </div>
    <style>
        .sidebar-single-shkrn {
            display: none;
        }
        .box-teacher-archive.box-article .image-teacher {
            padding: 20px 20px 5px;
        }
        .box-teacher-archive.box-article h2 {
            color: #294559;
            text-align: center;
            font-weight: 800;
            margin-bottom: -30px;
        }
    </style>
</div>
<?php get_footer();

<?php
defined( 'ABSPATH' ) || exit;
$is_tax = is_tax();
$archive_title = get_queried_object()->name;
$archive_desc = get_queried_object()->description;
$archive_info = (property_exists($queried_object = get_queried_object(), 'taxonomy')) ? $queried_object->taxonomy : null;
$blog_desc_text = shkrn_options('blog_desc_text');
$blog_btn_more = shkrn_options('blog_btn_more');
$blog_btn_more_text = shkrn_options('blog_btn_more_text');
$show_sidebar = shkrn_options('show_sidebar');
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
<?php }
get_header(); ?>
    <div class="container">
        <?php if ( (!empty($archive_title) or !empty($archive_desc) ) && $is_tax) { ?>
            <div class="shkrn-archive-title-desc flex-center">
                <h1><?php echo $archive_title; ?></h1>
                <?php echo $archive_desc; ?>
            </div>
        <?php } ?>
        <?php if ($blog_show_breadcrumb == "true") { ?>
            <nav class="breadcrumb-box-shkrn">
                <?php shkrn_bread_crumb(); ?>
            </nav>
        <?php } ?>
        <div class="single-page-shkrn">
            <div class="single-page-main-part tax-podcast-tv-page-shkrn">
                <div class="archive-grid-posts-shkrn shkrn-grid-columns-<?php echo shkrn_options('posts_columns'); ?>">
                    <?php if (have_posts()) {
                        while (have_posts()) : the_post(); ?>
                            <article class="box-article">
                                <a href="<?php esc_url(the_permalink()); ?>">
                                    <?php if (has_post_thumbnail()) {
                                        the_post_thumbnail('posts_2');
                                    } else { ?>
                                        <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                                    <?php } ?>
                                    <h2><?php the_title(); ?></h2>
                                </a>
                            </article>
                        <?php
                        endwhile;
                    } else { ?>
                        <p class="not-found"><?php esc_html_e('Nothing found', 'shokrino' ); ?></p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="more-button-section-shkrn flex-center"><?php esc_html_e('Back to home', 'shokrino' ); ?></a>
                    <?php } ?>
                </div>
                <div class="pagination">
                    <?php echo paginate_links(array(
                        'prev_text' => __( 'Previous' , 'shokrino' ),
                        'next_text' => __( 'Next' , 'shokrino' ),
                    )); ?>
                </div>
            </div>
            <?php
            if ($show_sidebar == "yes" or empty($show_sidebar) ) {
                get_sidebar();
            } else { ?>
                <style>
                    .sidebar-single-shkrn {
                        display: none;
                    }
                </style>
            <?php } ?>
        </div>
    </div>
<?php get_footer();

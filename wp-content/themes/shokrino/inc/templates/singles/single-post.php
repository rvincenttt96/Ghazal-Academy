<?php
defined( 'ABSPATH' ) || exit;
get_header();
$show_related_posts = shkrn_options('blog_related_posts');
$show_comments = shkrn_options('blog_show_comments');
$blog_show_breadcrumb = shkrn_options('blog_show_breadcrumb');
$show_sidebar_single = shkrn_options('show_sidebar_single');
$post_sidebar_show = skrn_metaoptions( 'post_sidebar_show');
$post_header_show = skrn_metaoptions('post_header_show');
$post_footer_show = skrn_metaoptions('post_footer_show');
$post_breadcrumb_show = skrn_metaoptions('post_breadcrumb_show');
$post_content_table_show = skrn_metaoptions('post_content_table_show');
$show_product_offer_on_post = skrn_metaoptions('post_product_offer_show');
$id_product_offer_on_post = skrn_metaoptions('post_product_offer_id');
$type_of_video = skrn_metaoptions('type_of_video');
$video_metabox = skrn_metaoptions('shokrino_video_post');
$video_url_metabox = skrn_metaoptions('shokrino_video_post_url');
$video_shortcode_metabox = skrn_metaoptions('shokrino_video_shortcode_post');
$video_aparat_metabox = skrn_metaoptions('shokrino_video_aparat_post');
$excerpt_post = skrn_metaoptions('post-excerpt');
$download_btn_show = skrn_metaoptions('download_btn_show');
$download_btn_link = skrn_metaoptions('download_btn_link');
$download_btn_text = skrn_metaoptions('download_btn_text');
$show_author = skrn_metaoptions('show_author_post');
$author_id = get_post_field ('post_author', get_the_ID());
$author_name = get_the_author_meta( 'user_firstname' , $author_id ).' '.get_the_author_meta( 'user_lastname' , $author_id );
$author_desc = get_the_author_meta( 'user_description' , $author_id );
$author_avatar = get_avatar( $author_id , '125px');
if ($post_header_show == "false") { ?>
    <style>
        .header-shkrn , .header-notif-bar-shokrino {
            display: none !important;
        }
    </style>
<?php }
if ($post_footer_show == "false") { ?>
    <style>
        .footer-shkrn {
            display: none !important;
        }
    </style>
<?php }
if ($post_content_table_show == "false") { ?>
    <style>
        .toc-bound {
            display: none !important;
        }
    </style>
<?php }
if ($blog_show_breadcrumb != "true") { ?>
    <style>
        .content-single-shkrn {
            margin-top: 25px;
        }
    </style>
<?php } ?>
<div class="container">
    <?php if ($blog_show_breadcrumb == "true") { ?>
    <nav class="breadcrumb-box-shkrn">
        <?php shkrn_bread_crumb(); ?>
    </nav>
    <?php } ?>
    <div class="single-page-shkrn">
        <div class="single-page-main-part post-page-shkrn">
        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <article class="content-single-shkrn posts-content-shkrn">
                    <h1 class="post-title-heading1-shkrn entry-title"><?php the_title(); ?></h1>
                    <?php
                    $poster = get_the_post_thumbnail_url();
                    if ((!empty($video_metabox['url']) or !empty($video_url_metabox) or !empty($video_shortcode_metabox) or !empty($video_aparat_metabox)) && $type_of_video != 'disable') {
                        if ($type_of_video == 'shortcode') {
                            echo do_shortcode($video_shortcode_metabox); ?>
                            <style>
                                .overlay-set figure {
                                    margin: 0 !important;
                                }
                            </style>
                            <?php
                        } elseif ($type_of_video == 'aparat') {
                            echo do_shortcode("[aparat id='$video_aparat_metabox']"); ?>
                            <style>
                                .overlay-set figure {
                                    margin: 0 !important;
                                }
                            </style>
                            <?php
                        } else {
                            if ($type_of_video == 'from_uploads') {
                                $video = $video_metabox['url'];
                            } else {
                                $video = $video_url_metabox;
                            }
                            ?>
                            <figure class="video-post-shkrn">
                                <?php shkrn_video_player($video,$poster); ?>
                            </figure>
                            <?php
                        }
                    } else { ?>
                        <div class="post-thumbnail-image">
                            <?php if (has_post_thumbnail()) { ?>
                                <figure>
                                <?php the_post_thumbnail(optimize_img_shkrn('single_cover')); ?>
                                </figure>
                            <?php } else { ?>
                                <figure>
                                    <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                                </figure>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="post-meta-and-picture-box">
                        <div class="post-meta-boxes">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="4" y="5" width="16" height="16" rx="2" />
                                <line x1="16" y1="3" x2="16" y2="7" />
                                <line x1="8" y1="3" x2="8" y2="7" />
                                <line x1="4" y1="11" x2="20" y2="11" />
                                <line x1="11" y1="15" x2="12" y2="15" />
                                <line x1="12" y1="15" x2="12" y2="18" />
                            </svg>
                            <?php _e('Last Update:','shokrino'); ?>&nbsp;
                            <time datetime="<?php the_time('c'); ?>"><?php the_time('j F Y'); ?></time>
                        </div>
                        <div class="post-meta-boxes">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folders" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 4h3l2 2h5a2 2 0 0 1 2 2v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2" />
                                <path d="M17 17v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h2" />
                            </svg>
                            <?php $categories = get_the_category();
                            if ( ! empty( $categories ) && is_array($categories) ) {
                                echo '<a href="' . get_term_link( $categories[0]->term_id) . '">' . esc_html( $categories[0]->name ) . '</a>';
                            } ?>
                        </div>
                    </div>
                    <?php
                        if (!empty($excerpt_post)) {
                        echo $excerpt_post;
                        } ?>
                    <?php the_content(); ?>
                    <?php
                    if ($download_btn_show == "true") { ?>
                    <div class="download-btn-box-shkrn flex-center">
                        <a href="<?php echo $download_btn_link; ?>" class="download_btn_class">
                            <?php echo $download_btn_text; ?>
                        </a>
                    </div>
                    <?php }
                    if ($show_author == true or $show_author == NULL) { ?>
                        <div class="post-author-shkrn flex-center-in-col">
                            <div class="author-image flex-center">
                                <?php if (!empty($author_avatar)) {
                                    echo $author_avatar;
                                } else { ?>
                                    <img src="<?php echo SHKRN_ASSETS. '/img/user.jpg' ?>" width="125" height="125" class="avatar" alt="آواتار نویسنده پست"/>
                                <?php } ?>
                            </div>
                            <div class="author-desc flex-center-in-col">
                                <span><?php echo $author_name; ?></span>
                                <p><?php echo $author_desc; ?></p>
                            </div>
                        </div>
                    <?php }
                    $post_tags = wp_get_post_tags(get_the_ID());
                    if ($post_tags) {
                        echo '<div class="post-tags-shkrn flex-center">';
                        foreach($post_tags as $tag) {
                            $tag_link = get_tag_link($tag->term_id);
                            echo '<a href="' . esc_url($tag_link) . '" class="tag-item-shkrn">' . $tag->name . '</a>';
                        }
                        echo '</div>';
                    } ?>
                </article>
            <?php
            endwhile;
        endif;
        global $post;
        $related = get_posts( array(
            'category__in' => wp_get_post_categories(get_the_ID()),
            'numberposts' => 3,
            'post__not_in' => array(get_the_ID()) ) );
        if ($show_related_posts == "true" && !empty($related)) { ?>
            <section class="posts-section-shkrn flex-center">
                <div class="posts-section-header flex-center">
                    <div class="posts-title-shkrn flex-center">
                        <h2><?php esc_html_e('Related posts', 'shokrino' ); ?></h2>
                        <h5><?php esc_html_e('Don\'t miss reading these posts!', 'shokrino' ); ?></h5>
                    </div>
                </div>

                <div id="related-post-slider-shkrn" class="archive-grid-posts-shkrn shkrn-grid-columns-3">
                    <?php if( $related ) foreach( $related as $post ) { ?>
                        <div class="box-article swiper-slide">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) {
                                    the_post_thumbnail(optimize_img_shkrn('posts'));
                                } else { ?>
                                    <img src="<?php echo SHKRN_ASSETS . '/img/0.jpg'; ?>">
                                <?php } ?>
                                <h2><?php the_title(); ?></h2>
                                <p><?php the_excerpt(); ?></p>
                                <div class="btn-more"><?php esc_html_e('Read more', 'shokrino' ); ?></div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </section>
        <?php
            wp_reset_postdata();
        } ?>
        <?php if ($show_comments == "true") { ?>
        <section class="posts-section-shkrn flex-center">
            <div class="posts-section-header flex-center">
                <div class="posts-title-shkrn flex-center">
                    <h2><?php esc_html_e('Comments', 'shokrino' ); ?></h2>
                    <h5><?php esc_html_e('Share your comments with us', 'shokrino' ); ?></h5>
                </div>
            </div>
            <div class="comments-box">
                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>
        </section>
        <?php } ?>
    </div>
    <?php
    if ($show_sidebar_single == "yes" && $post_sidebar_show == "true") {
        get_sidebar();
    } ?>
    </div>
</div>
<?php get_footer();
<?php
defined( 'ABSPATH' ) || exit;

get_header();

$show_sidebar_single = shkrn_options('show_sidebar_single');
$show_related_posts = shkrn_options('blog_related_posts');
$show_comments = shkrn_options('blog_show_comments');
$blog_show_breadcrumb = shkrn_options('blog_show_breadcrumb');
$instagram = shkrn_options('instagram_btn_link');
$telegram = shkrn_options('telegram_btn_link');
$type_of_audio = skrn_metaoptions('type_of_audio_podcast');
$audio_metabox = skrn_metaoptions('shokrino_audio_podcast');
$audio_url_metabox = skrn_metaoptions('shokrino_audio_podcast_url');
$audio_shortcode_metabox = skrn_metaoptions('shokrino_audio_shortcode');
?>
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
                        <article>
                            <div class="video-box-top-shkrn">
                                <div class="overlay-set-video-shkrn">
                                    <?php
                                    $poster = get_the_post_thumbnail_url();
                                    if (empty($audio_metabox['url']) && empty($audio_url_metabox) && empty($audio_shortcode_metabox)) {
                                        if (has_post_thumbnail()) {
                                            ?><figure><?php
                                            the_post_thumbnail(optimize_img_shkrn('single_cover'));
                                            ?></figure><?php
                                        } else { ?>
                                            <figure>
                                                <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                                            </figure>
                                        <?php }
                                    } else {
                                        if ($type_of_audio == 'shortcode') {
                                            echo do_shortcode($audio_shortcode_metabox); ?>
                                            <style>
                                                .overlay-set figure {
                                                    margin: 0 !important;
                                                }
                                            </style>
                                            <?php
                                        } else {
                                            if ($type_of_audio == 'from_uploads') {
                                                $audio = $audio_metabox['url'];
                                            } else {
                                                $audio = $audio_url_metabox;
                                            }
                                            ?>
                                            <figure class="audio-post-shkrn">
                                                <?php shkrn_audio_player($audio,$poster); ?>
                                            </figure>
                                            <?php
                                        }
                                    } ?>
                                </div>
                            </div>
                            <div class="content-single-shkrn posts-content-shkrn">
                                <div class="tv-title-meta">
                                    <h1 class="post-title-heading1-shkrn entry-title"><?php the_title(); ?></h1>
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
                                    </div>
                                </div>
                                <?php the_content(); ?>
                            </div>
                        </article>
                    <?php
                    endwhile;
                endif;
                global $post;
                $related = get_posts( array(
                    'category__in' => wp_get_post_categories($post->ID),
                    'numberposts' => 5,
                    'post_type'        => 'podcast',
                    'post__not_in' => array($post->ID) ) );
                if ($show_related_posts == "true" && !empty($related)) { ?>
                    <section class="posts-section-shkrn flex-center">
                        <div class="posts-section-header flex-center">
                            <div class="posts-title-shkrn flex-center">
                                <h2><?php esc_html_e('Related posts', 'shokrino' ); ?></h2>
                                <h5><?php esc_html_e('Don\'t miss reading these posts!', 'shokrino' ); ?></h5>
                            </div>
                        </div>
                        <div id="related-post-slider-shkrn" class="swiper posts-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden">
                            <div class="swiper-wrapper post-slider-shokrino" aria-live="polite">
                                <?php if( $related ) foreach( $related as $post ) { ?>
                                    <div class="box-article swiper-slide">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) {
                                                the_post_thumbnail(optimize_img_shkrn('posts'));
                                            } else { ?>
                                                <img src="<?php echo SHKRN_ASSETS . '/img/0.jpg'; ?>">
                                            <?php } ?>
                                            <h2><?php the_title(); ?></h2>
                                            <p><?php echo shokrino_excerpt(get_the_excerpt()); ?></p>
                                            <div class="btn-more"><?php esc_html_e('Read more', 'shokrino' ); ?></div>
                                        </a>
                                    </div>
                                <?php }
                                wp_reset_postdata(); ?>
                            </div>
                            <div class="swiper-button-prev swiper-btn-style-shkrn"></div>
                            <div class="swiper-button-next swiper-btn-style-shkrn"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </section>
                <?php } ?>
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
            if ($show_sidebar_single == "yes") {
                get_sidebar();
            } ?>
        </div>
    </div>
<?php get_footer();
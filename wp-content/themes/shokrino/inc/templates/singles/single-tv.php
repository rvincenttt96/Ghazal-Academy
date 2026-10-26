<?php
defined( 'ABSPATH' ) || exit;

get_header();

$show_sidebar_single = shkrn_options('show_sidebar_single');
$show_related_posts = shkrn_options('blog_related_posts');
$show_comments = shkrn_options('blog_show_comments');
$blog_show_breadcrumb = shkrn_options('blog_show_breadcrumb');
$type_of_video = skrn_metaoptions('type_of_video_tv');
$video_metabox = skrn_metaoptions('shokrino_video_tv');
$video_url_metabox = skrn_metaoptions('shokrino_video_tv_url');
$video_shortcode_metabox = skrn_metaoptions('shokrino_video_shortcode');
$video_aparat_metabox = skrn_metaoptions('shokrino_video_aparat_tv');
$access_to_video = skrn_metaoptions('access_to_video');
$title_required_text = skrn_metaoptions('title_required_text');
$condition_product_id = skrn_metaoptions('condition_product_id');
$condition_of_video = skrn_metaoptions('condition_of_video');
$course_post_teacher = skrn_metaoptions('shokrino_video_tv_teacher');
$course_post_time = skrn_metaoptions('shokrino_video_tv_time');
$course_post_session = skrn_metaoptions('shokrino_video_tv_session');
$btn_required_url = skrn_metaoptions('btn_required_url');
$btn_required_text = skrn_metaoptions('btn_required_text');
$show_course_info_tv = skrn_metaoptions('show_course_info_tv');
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
                if($access_to_video == "free") { ?>
                    <?php
                    $poster = get_the_post_thumbnail_url();
                    if (empty($video_metabox['url']) && empty($video_url_metabox) && empty($video_shortcode_metabox) && empty($video_aparat_metabox)) {
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
                    } ?>
                <?php } else { ?>
                    <?php
                    $show_video_not_free = false;
                    if($condition_of_video == "login") {
                        if(is_user_logged_in()) {
                            $show_video_not_free = true;
                        }
                    } elseif($condition_of_video == "bought_product") {
                        $current_user = wp_get_current_user();
                        $user_id = $current_user->ID;
                        $product_id = $condition_product_id;
                        if (wc_customer_bought_product($current_user->user_email, $user_id, $product_id)) {
                            $show_video_not_free = true;
                        }
                    }
                    if($show_video_not_free) { ?>
                        <?php
                        $poster = get_the_post_thumbnail_url();
                        if (empty($video_metabox['url']) && empty($video_url_metabox) && empty($video_shortcode_metabox)) {
                            if (has_post_thumbnail()) {
                                ?><figure><?php
                                the_post_thumbnail('single_cover');
                                ?></figure><?php
                            } else { ?>
                                <figure>
                                    <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                                </figure>
                            <?php }
                        } else {
                            if ($type_of_video == 'shortcode') {
                                echo do_shortcode($video_shortcode_metabox); ?>
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
                        } ?>
                    <?php } else {
                        if($condition_of_video == "login"){
                            if(empty($btn_required_url)) {
                                $btn_required_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
                            }
                            if(empty($btn_required_text)) {
                                $btn_required_text = __( 'ورود به حساب کاربری','shokrino');
                            } ?>
                            <div class="video-post-shkrn not-access-video-shkrn flex-center">
                                        <span class="title-not-access">
                                            <?php echo $title_required_text; ?>
                                        </span>
                                <a href="<?php echo $btn_required_url; ?>" class="go-login-page-not-access">
                                    <?php echo $btn_required_text; ?>
                                </a>
                            </div>
                        <?php }elseif($condition_of_video == "bought_product"){
                            if(empty($btn_required_url)) {
                                $btn_required_url = get_permalink($condition_product_id);
                            }
                            if(empty($btn_required_text)) {
                                $btn_required_text = __( 'خرید محصول','shokrino');
                            } ?>
                            <div class="video-post-shkrn not-access-video-shkrn flex-center">
                                        <span class="title-not-access">
                                            <?php echo $title_required_text; ?>
                                        </span>
                                <a href="<?php echo $btn_required_url; ?>" class="go-login-page-not-access">
                                    <?php echo $btn_required_text; ?>
                                </a>
                            </div>
                        <?php } else {
                            $poster = get_the_post_thumbnail_url();
                            if (empty($video_metabox['url']) && empty($video_url_metabox) && empty($video_shortcode_metabox)) {
                                if (has_post_thumbnail()) {
                                    ?><figure><?php
                                    the_post_thumbnail('single_cover');
                                    ?></figure><?php
                                } else { ?>
                                    <figure>
                                        <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
                                    </figure>
                                <?php }
                            } else {
                                if ($type_of_video == 'shortcode') {
                                    echo do_shortcode($video_shortcode_metabox); ?>
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
                            }
                        } ?>
                    <?php } ?>
                    <?php } ?>
                        <?php if($show_course_info_tv){ ?>
                            <div class="col-items-course-shkrn flex">
                                <?php if (!empty($course_post_session)) { ?>
                                <div class="item-course-post-shkrn flex-center">
                                    <svg width="40" viewBox="0 0 25 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7169 2.69166C16.6042 1.00962 14.5859 0 12.5 0C10.4141 0 8.39587 1.00961 5.28315 2.69166L5.22727 2.72186L5.22726 2.72186C3.68828 3.55348 2.46412 4.21498 1.6299 4.77848C1.2074 5.06386 0.826207 5.36214 0.543285 5.68702C0.257095 6.01565 0 6.45496 0 7V15C0 15.5523 0.447715 16 1 16C1.55228 16 2 15.5523 2 15V9.46227C2.55931 9.81417 3.25267 10.2042 4.0643 10.6476C3.9998 11.452 3.99989 12.4916 3.99999 13.8335L4 13.9999C4 18.9999 5.49418 19.9999 12.4656 19.9999C19 19.9999 21 19 21 13.9999C21 12.5794 21 11.4883 20.9287 10.6514C21.9332 10.1028 22.757 9.63567 23.3701 9.22152C23.7926 8.93613 24.1738 8.63785 24.4567 8.31297C24.7429 7.98434 25 7.54503 25 6.99999C25 6.45496 24.7429 6.01565 24.4567 5.68702C24.1738 5.36214 23.7926 5.06386 23.3701 4.77847C22.5359 4.21498 21.3117 3.55348 19.7728 2.72186L19.7169 2.69166ZM18.9819 11.7034C16.277 13.1472 14.4171 14 12.5 14C10.5822 14 8.7216 13.1466 6.01498 11.7017C6.00026 12.2995 6 13.0465 6 13.9999C6 16.465 6.41412 16.9896 6.76855 17.2386C7.03417 17.4253 7.53095 17.6393 8.50505 17.7887C9.47215 17.937 10.7509 17.9999 12.4656 17.9999C14.0655 17.9999 15.2949 17.9375 16.2488 17.7893C17.2075 17.6404 17.7532 17.4239 18.0763 17.2092C18.523 16.9123 19 16.3212 19 13.9999C19 13.0462 18.9985 12.2997 18.9819 11.7034ZM2.74938 6.43581C2.37924 6.68583 2.16569 6.86955 2.05196 7C2.16569 7.13045 2.37924 7.31417 2.74938 7.56419C3.49128 8.06532 4.62684 8.68035 6.23397 9.54881C9.466 11.2953 11.0169 12 12.5 12C13.9832 12 15.534 11.2953 18.7661 9.54881C20.3732 8.68035 21.5087 8.06532 22.2506 7.56419C22.6208 7.31417 22.8343 7.13044 22.948 6.99999C22.8343 6.86955 22.6208 6.68582 22.2506 6.4358C21.5087 5.93467 20.3732 5.31965 18.7661 4.45119C15.534 2.70467 13.9832 2 12.5 2C11.0169 2 9.466 2.70467 6.23397 4.45119C4.62684 5.31965 3.49128 5.93468 2.74938 6.43581ZM23.0157 7.09148L23.0144 7.0888C23.0153 7.0906 23.0157 7.09148 23.0157 7.09148ZM23.0144 6.91119L23.0157 6.90851C23.0157 6.90851 23.0153 6.9094 23.0144 6.91119ZM1.98561 6.9112C1.98467 6.9094 1.9843 6.90851 1.98434 6.90852L1.98561 6.9112Z" fill="var(--shkrn-var-main-color)"/>
                                    </svg>
                                    <span class="name-course-item-shkrn">جلسه <?php echo $course_post_session; ?></span>
                                    <?php
                                    $categories = get_the_terms( $post->ID, 'cat_tv' );
                                    if(!empty($categories)){
                                        echo '<a href="' . get_term_link($categories[0]->term_id) . '" rel="category tag" class="name-course-cat-item-shkrn">' . esc_html($categories[0]->name) . '</a>';
                                    }
                                    ?>
                                </div>
                                <?php } ?>
                                <?php if (!empty($course_post_teacher)) { ?>
                                <div class="item-course-post-shkrn flex-center">
                                    <svg height="40" viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 11C1 13.0721 1.6662 15.0155 3.5 16.5C4.93978 17.6655 6.5154 18 8 18M8 18L8 21L11 21L5 21M8 18C9.62678 18 11.0602 17.6655 12.5 16.5C14.3338 15.0155 15 12.9833 15 11" stroke="var(--shkrn-var-main-color)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 1C7.05292 1 6.38701 1.23723 5.89889 1.58349C5.40324 1.93508 5.01959 2.45012 4.72866 3.1151C4.13114 4.48086 4 6.2973 4 8C4 9.7027 4.13114 11.5191 4.72866 12.8849C5.01959 13.5499 5.40324 14.0649 5.89889 14.4165C6.38701 14.7628 7.05292 15 8 15C8.94708 15 9.61299 14.7628 10.1011 14.4165C10.5968 14.0649 10.9804 13.5499 11.2713 12.8849C11.8689 11.5191 12 9.7027 12 8C12 6.2973 11.8689 4.48086 11.2713 3.1151C10.9804 2.45012 10.5968 1.93508 10.1011 1.58349C9.61299 1.23723 8.94708 1 8 1Z" stroke="var(--shkrn-var-main-color)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="name-course-item-shkrn">مدرس دوره</span>
                                    <span class="value-course-item-shkrn"><?php echo $course_post_teacher; ?></span>
                                </div>
                                <?php } ?>
                                <?php if (!empty($course_post_time)) { ?>
                                <div class="item-course-post-shkrn flex-center">
                                    <svg height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 12C1 14.4477 1.13246 16.3463 1.46153 17.827C1.78807 19.2963 2.29478 20.2921 3.00136 20.9986C3.70794 21.7052 4.70365 22.2119 6.17298 22.5385C7.65366 22.8675 9.55232 23 12 23C14.4477 23 16.3463 22.8675 17.827 22.5385C19.2963 22.2119 20.2921 21.7052 20.9986 20.9986C21.7052 20.2921 22.2119 19.2963 22.5385 17.827C22.8675 16.3463 23 14.4477 23 12C23 9.55232 22.8675 7.65366 22.5385 6.17298C22.2119 4.70365 21.7052 3.70794 20.9986 3.00136C20.2921 2.29478 19.2963 1.78807 17.827 1.46153C16.3463 1.13246 14.4477 1 12 1C9.55232 1 7.65366 1.13246 6.17298 1.46153C4.70365 1.78807 3.70794 2.29478 3.00136 3.00136C2.29478 3.70794 1.78807 4.70365 1.46153 6.17298C1.13246 7.65366 1 9.55232 1 12Z" stroke="var(--shkrn-var-main-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 6C12 6 12 10 12 11C12 12 12 12 13 12C14 12 18 12 18 12" stroke="var(--shkrn-var-main-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="name-course-item-shkrn">زمان ویدیو</span>
                                    <span class="value-course-item-shkrn"><?php echo $course_post_time; ?></span>
                                </div>
                                <?php } ?>
                                <div class="item-course-post-shkrn flex-center">
                                    <svg width="37" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 1C14.4525 1 16.3618 1.11137 17.855 1.39866C19.3432 1.68498 20.3438 2.13251 21.0445 2.75018C22.4367 3.97734 23 6.19513 23 10.6667C23 13.5482 22.7413 15.6685 22.0447 17.0498C21.7104 17.7127 21.2871 18.1797 20.7543 18.4903C20.2159 18.8042 19.494 19 18.5002 19C17.2191 19 16.2575 19.2877 15.5056 19.7971C14.7717 20.2944 14.329 20.9455 13.9997 21.4637C13.9499 21.542 13.9032 21.6161 13.8591 21.6862C13.583 22.1245 13.4043 22.4082 13.1562 22.6307C12.9352 22.8291 12.6262 23 12.0003 23C11.3744 23 11.0654 22.8291 10.8443 22.6307C10.5963 22.4081 10.4176 22.1245 10.1415 21.6862C10.0974 21.6161 10.0507 21.542 10.0009 21.4636C9.67154 20.9454 9.22884 20.2944 8.4949 19.7971C7.74298 19.2877 6.7814 19 5.50024 19C4.51174 19 3.79218 18.7993 3.25388 18.4789C2.71963 18.1609 2.29353 17.6832 1.95707 17.0102C1.25807 15.612 1 13.488 1 10.6667C1 6.25195 1.56175 4.02841 2.95861 2.78674C3.66142 2.16203 4.66352 1.70608 6.14984 1.41246C7.64154 1.11777 9.54955 1 12 1Z" stroke="var(--shkrn-var-main-color)" stroke-width="2"  stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M13 9H17" stroke="var(--shkrn-var-main-color)" stroke-width="2"  stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7 13H17" stroke="var(--shkrn-var-main-color)" stroke-width="2"  stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="name-course-item-shkrn">نوشتن نظر</span>
                                    <a href="#comments" class="btn-download-course-shkrn">
                                        کلیک کنید
                                    </a>
                                </div>
                                <div class="item-course-post-shkrn before-after-shkrn-post flex-center">
                                    <?php
                                    $svg_before = '
                                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 13L1 7L7 1" stroke="var(--shkrn-var-main-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>';
                                    $svg_after = '
                                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L1 13" stroke="var(--shkrn-var-main-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>';
                                        next_post_link('%link',$svg_after.'جلسه بعدی',true,'','cat_tv');
                                        previous_post_link('%link','جلسه قبلی'.$svg_before,true,'','cat_tv');
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
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
            'numberposts' => 3,
            'post_type'        => 'tv',
            'post__not_in' => array($post->ID) ) );
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
                                <p><?php echo shokrino_excerpt(get_the_excerpt(),20); ?></p>
                                <div class="btn-more"><?php esc_html_e('Read more', 'shokrino' ); ?></div>
                            </a>
                        </div>
                    <?php } ?>
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
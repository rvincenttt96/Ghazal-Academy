<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post,$product,$woocommerce;

$add_to_cart_text = shkrn_options('add_to_cart_text');
$course_student_text = shkrn_options('course_student_text');
$current_user = wp_get_current_user();
$current_user_id = wp_get_current_user()->ID;
$product_content = get_the_content();
$product_video_poster_array = skrn_metaoptions('shokrino_video_Woo_poster');
$product_video_array = skrn_metaoptions('shokrino_video_Woo');
$product_video_link = skrn_metaoptions('video_post_url_course');
$product_video_status = skrn_metaoptions('type_of_video_course');
$product_has_content = !empty($product_content) ? true : false;
if ($product_video_status == "from_url") {
    $product_has_video = ($product_video_status !== "disable" && !empty($product_video_link)) ? true : false;
} else {
    $product_has_video = ($product_video_status !== "disable" && !empty($product_video_array['url'])) ? true : false;
}
$product_show_related = shkrn_metaoptions('related_products_show');
$course_detail_reviews = shkrn_options('course_detail_reviews');
$id = $product->get_id();
$downloads = wc_get_customer_available_downloads($current_user_id);
$course_downloads = shkrn_options('course_downloads');
$is_download_files = $product->is_downloadable();
$has_download_files = !empty($downloads) ? true : false;
if (wc_customer_bought_product($current_user->user_email, $current_user_id, $id) && $is_download_files && $has_download_files && $course_downloads == 1) {
    $show_download_box = true;
} else {
    $show_download_box = false;
}
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'shkrn-course-product', $product ); ?>>
    <div class="product-top-header-shkrn flex-center-in-col">
        <div class="image-left-box-product-shkrn flex-center">
            <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
        </div>
        <div class="title-desc-right-box-product-shkrn">
            <?php
            woocommerce_template_single_title();
            woocommerce_template_single_excerpt();
            ?>
            <div class="box-btns-price-rate-product-shkrn">
                <div class="rate-seasons-box-row row-product-meta-shkrn flex-center-in-col">
                    <?php woocommerce_template_single_meta(); ?>
                </div>
                <div class="rate-seasons-box-row third-row-top-product flex-center-in-col">
                    <?php if ( comments_open() ) { ?>
                        <a href="#reviews" class="btn-show-seasons-shkrn flex-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="var(--shkrn-var-main-color)" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M12 20l-3 -3h-2a3 3 0 0 1 -3 -3v-6a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-2l-3 3"></path> <line x1="8" y1="9" x2="16" y2="9"></line> <line x1="8" y1="13" x2="14" y2="13"></line></svg>
                            <?php _e('مشاهده نظرات','shokrino'); ?>
                        </a>
                    <?php } ?>
                    <?php if ($course_detail_reviews) { ?>
                        <div class="shkrn-product-rate-stars">
                            <?php woocommerce_template_single_rating(); ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="rate-seasons-box-row sale-counter-course-shkrn flex-center-in-col">
                    <?php
                    require_once SHKRN_WOO . '/page-parts/sale-counter.php';
                    ?>
                </div>
                <?php if (is_product_variable_shkrn()) { ?>
                    <div class="rate-seasons-box-row fifth-row-top-product flex-center-in-col">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                <?php } else { ?>
                    <div class="rate-seasons-box-row forth-row-top-product flex-center-in-col">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                        <div class="price-product-shkrn">
                            <?php woocommerce_template_single_price(); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="single-page-seperator-shkrn">
        <div class="content-shkrn">
            <div id="nav-wrapper-shkrn" class="btn-list-on-product-shkrn flex-center-in-col">
                <?php
                shkrn_anchor_maker('video-shkrn','fromhere','','ویدیو معرفی','btn-item-on-product-shkrn',$product_has_video);
                shkrn_anchor_maker('desc-shkrn','fromhere','','توضیحات','btn-item-on-product-shkrn',$product_has_content);
                shkrn_anchor_maker('teachers-shkrn','fromhere','','مدرسان دوره','btn-item-on-product-shkrn',!empty(skrn_metaoptions('teacher_choose')));
                shkrn_anchor_maker('reviews','fromhere','','دیدگاه و پرسش ها','btn-item-on-product-shkrn',comments_open());
                if ($product_show_related == "true") {
                    shkrn_anchor_maker('related-shkrn', 'fromhere', '', 'محصولات مرتبط', 'btn-item-on-product-shkrn', $product_show_related);
                }
                ?>
            </div>
            <?php
            if ($show_download_box) {
                require_once SHKRN_WOO . '/page-parts/box-download-files.php';
            } ?>
            <?php if ($product_has_video) { ?>
                <section class="anchor-list-on-product-shkrn video-product-shkrn">
                    <?php shkrn_anchor_maker('video-shkrn'); ?>
                    <figure class="video-post-shkrn">
                        <?php if($product_video_status == "from_uploads") { ?>
                            <?php shkrn_video_player($product_video_array['url'],$product_video_poster_array['url']); ?>
                        <?php } elseif($product_video_status == "from_url") { ?>
                            <?php shkrn_video_player($product_video_link,$product_video_poster_array['url']); ?>
                        <?php } else { ?>
                            <?php shkrn_video_player($product_video_array['url'],$product_video_poster_array['url']); ?>
                        <?php } ?>
                    </figure>
                </section>
            <?php } ?>
            <?php if ($product_has_content or $elementor_preview_active = \Elementor\Plugin::$instance->preview->is_preview_mode()) { ?>
                <section class="anchor-list-on-product-shkrn content-single-shkrn posts-content-shkrn">
                    <?php shkrn_anchor_maker('desc-shkrn'); ?>
                    <h2 class="title-section-shkrn flex-center-in-col">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots-circle-horizontal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="var(--shkrn-var-main-color)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="12" r="9"></circle>
                            <line x1="8" y1="12" x2="8" y2="12.01"></line>
                            <line x1="12" y1="12" x2="12" y2="12.01"></line>
                            <line x1="16" y1="12" x2="16" y2="12.01"></line>
                        </svg>
                        توضیحات
                    </h2>
                    <?php
                    $darsad_pishraft_btn_show = skrn_metaoptions('progress_btn_show');
                    $darsad_pishraft = skrn_metaoptions('progress_percent');
                    $darsad_pishraft_text = skrn_metaoptions('progress_percent_text');
                    if ($darsad_pishraft_btn_show == 'true') { ?>
                        <div class="box-pishraft-dore">
                            <div class="title-pishraft-dore">
                                <?php if (!empty($progress_percent_text)) {
                                    echo $progress_percent_text;
                                } else { ?>
                                    <?php _e('Course progress percentage:','shokrino'); ?>
                                <?php } ?>
                                %
                                <?php echo $darsad_pishraft; ?>
                            </div>
                            <div class="navar-pishraft">
                                <div class="darsad-bar" style="width: <?php echo $darsad_pishraft; ?>%"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="box-features-course-side-shkrn shkrn-except-desktop">
                        <?php
                        require SHKRN_WOO . '/page-parts/box-meta-dore.php';
                        ?>
                    </div>
                    <?php the_content();
                    $product_tags = get_the_terms(get_the_ID(), 'product_tag');
                    if ($product_tags && !is_wp_error($product_tags)) {
                        echo '<div class="post-tags-shkrn flex-center">';
                        foreach($product_tags as $tag) {
                            $tag_link = get_term_link($tag->term_id, 'product_tag');
                            echo '<a href="' . esc_url($tag_link) . '" class="tag-item-shkrn">' . esc_html($tag->name) . '</a>';
                        }
                        echo '</div>';
                    }
                    ?>
                </section>
            <?php } ?>
            <?php if ($product->has_attributes()) { ?>
                <section class="content-single-shkrn posts-content-shkrn woo-attrs-shkrn">
                    <h2 class="title-section-shkrn flex-center-in-col">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots-circle-horizontal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="var(--shkrn-var-main-color)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="12" r="9"></circle>
                            <line x1="8" y1="12" x2="8" y2="12.01"></line>
                            <line x1="12" y1="12" x2="12" y2="12.01"></line>
                            <line x1="16" y1="12" x2="16" y2="12.01"></line>
                        </svg>
                        ویژگی ها
                    </h2>
                    <?php
                    wc_display_product_attributes($product);
                    ?>
                </section>
            <?php } ?>
            <?php
            require_once SHKRN_WOO . '/page-parts/teacher-course.php';
            ?>
            <?php if (comments_open()) { ?>
                <section class="anchor-list-on-product-shkrn posts-section-shkrn flex-center">
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
            <?php
            if ($product_show_related == "true") {
                do_action( 'woocommerce_after_single_product_summary' );
            } ?>
        </div>
        <?php
        require_once SHKRN_WOO . '/sidebar-product.php';
        require_once SHKRN_WOO . '/page-parts/fixed-addtocart.php';
        ?>
    </div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>

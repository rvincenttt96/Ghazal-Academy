<?php
/**
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

$id = get_the_ID();
$current_user = wp_get_current_user();
$current_user_id = wp_get_current_user()->ID;
$d_pro_extra_btn = skrn_metaoptions('downloady_product_extra_button');
$d_pro_extra_btn_txt = skrn_metaoptions('downloady_product_extra_btn_txt');
$d_pro_extra_btn_url = skrn_metaoptions('downloady_product_extra_btn_url');
$build_date = skrn_metaoptions('downloady_product_date_of_build');
$update_date = skrn_metaoptions('downloady_product_date_of_update');
$last_version = skrn_metaoptions('downloady_product_version');
$support_product_dwnld = skrn_metaoptions('downloady_product_support');
$course_detail_reviews = shkrn_options('course_detail_reviews');
$dl_c1 = skrn_metaoptions('downloady_product_custom1');
$dl_c2 = skrn_metaoptions('downloady_product_custom2');
$dl_c3 = skrn_metaoptions('downloady_product_custom3');
$dl_c1_a = skrn_metaoptions('downloady_product_custom1_answer');
$dl_c2_a = skrn_metaoptions('downloady_product_custom2_answer');
$dl_c3_a = skrn_metaoptions('downloady_product_custom3_answer');
$show_info_box_notices = skrn_metaoptions('downloady_product_extra_box');
$title_info_box_notices = skrn_metaoptions('downloady_product_extra_box_txt');
$info_box_notices = skrn_metaoptions('downloady_product_extra_box_content');
$product_content = get_the_content();
$product_has_content = !empty($product_content) ? true : false;
$product_show_related = shkrn_metaoptions('related_products_show');
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
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'shkrn-downloady-product', $product ); ?>>

    <section class="top-downloady-box-shkrn flex-center">
        <?php woocommerce_template_single_title(); ?>
        <h3><?php
            $product_category_downloady = get_the_terms(get_the_ID(), 'product_cat');
            $cat_name = wp_list_pluck($product_category_downloady, 'name');
            global $wp_query;
            $terms_post = get_the_terms($post->cat_ID, 'product_cat');
            foreach ($terms_post as $term_cat) {
                $term_cat_id = $term_cat->term_id;
                $cat_pro_id = $term_cat_id;
            }
            $page_url_link = get_term_link($cat_pro_id, 'product_cat');
            echo '<a href="' . $page_url_link . '">';
            echo $cat_name[0];
            echo '</a>';
            ?></h3>
    </section>
    <div class="row-product-shkrn">
        <section class="downloady-product-image-shkrn">
            <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
        </section>
        <aside class="downloady-product-side-shkrn">
            <div class="price-and-addtocart-box flex-center-in-col">
                <div class="price-align-box flex-center">
                    <?php
                    require_once SHKRN_WOO . '/page-parts/sale-counter.php';
                    ?>
                </div>
                <div class="price-align-box flex-center">
                    <?php if (!is_product_variable_shkrn()) { ?>
                    <div class="price-product-shkrn">
                        <?php
                        woocommerce_template_single_price(); ?>
                    </div>
                    <?php } ?>
                    <div class="rate-seasons-box-row flex-center-in-col addtocart-btn-product-shkrn">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                    <?php if ($d_pro_extra_btn == "true" && !empty($d_pro_extra_btn_txt)) { ?>
                        <a class="extra-btn-downloady" href="<?php echo $d_pro_extra_btn_url; ?>">
                            <?php echo $d_pro_extra_btn_txt; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php
            require_once SHKRN_WOO . '/page-parts/box-meta-dl.php';
            ?>
            <?php if ($course_detail_reviews) { ?>
                <div class="shkrn-product-rate-stars">
                    <?php woocommerce_template_single_rating(); ?>
                </div>
            <?php } ?>
        </aside>
    </div>
    <?php
    if ($show_download_box) {
        require_once SHKRN_WOO . '/page-parts/box-download-files.php';
    }
    if ($show_info_box_notices && !empty($info_box_notices)) { ?>
    <section class="anchor-list-on-product-shkrn main-boxex-product-download info-box-notices posts-section-shkrn flex-center">
    <div class="posts-section-header flex-center">
        <div class="posts-title-shkrn flex-center">
            <?php if (!empty($title_info_box_notices)) { ?>
                <h2><?php echo $title_info_box_notices; ?></h2>
            <?php } else { ?>
                <h2><?php esc_html_e('Shopping Guide', 'shokrino' ); ?></h2>
            <?php } ?>
        </div>
    </div>
    <div class="product-description">
        <?php echo $info_box_notices; ?>
    </div>
    </section>
    <?php } ?>
    <?php if ($product_has_content or $elementor_preview_active = \Elementor\Plugin::$instance->preview->is_preview_mode()) { ?>
        <section class="anchor-list-on-product-shkrn content-single-shkrn posts-content-shkrn">
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
<?php
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<?php
/**
 * woocommerce_sidebar hook.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

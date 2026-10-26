<?php
defined( 'ABSPATH' ) || exit;

$id_product_offer_on_post = skrn_metaoptions('post_product_offer_id');
$title_product_offer_on_post = skrn_metaoptions('post_product_offer_title');
$text_product_offer_on_post = skrn_metaoptions('post_product_offer_text');
$post_product_offer_price = skrn_metaoptions('post_product_offer_price');
$button_type_product_offer_on_post = skrn_metaoptions('post_product_offer_button_type');
$addtocart_text_product_offer_on_post = skrn_metaoptions('post_product_offer_addtocart_text');
$addtocart_link_product_offer_on_post = skrn_metaoptions('post_product_offer_addtocart_link');
$image_pro_on_post = wp_get_attachment_image_src( get_post_thumbnail_id( $id_product_offer_on_post ), 'single_cover' );
$product = wc_get_product($id_product_offer_on_post);
$permalink_pro = $product->get_permalink();
?>
<div class="product-offer-on-post">
    <div class="pro-on-post-row">
        <div class="pro-on-post-item">
            <img src="<?php echo $image_pro_on_post[0]; ?>">
        </div>
        <div class="pro-on-post-item pro-titles-box-on-post">
            <?php if (!empty($title_product_offer_on_post)) { ?>
                <h2><?php echo $title_product_offer_on_post; ?></h2>
            <?php } ?>
            <?php if (!empty($text_product_offer_on_post)) { ?>
                <p><?php echo $text_product_offer_on_post; ?></p>
            <?php } ?>
            <?php if ($post_product_offer_price == "true") { ?>
                <div class="price-pro-on-post-box pro-top-detail">
                    <div class="price">
                        <?php
                        global $woocommerce;
                        $product = new WC_Product($id_product_offer_on_post);
                        echo $product->get_price_html();
                        ?>
                    </div>
                </div>
            <?php } ?>
            <div class="add-to-cart-btn-on-post">
                <?php if ($button_type_product_offer_on_post == 'false') { ?>
                <a href="<?php echo do_shortcode("[add_to_cart_url id=$id_product_offer_on_post]"); ?>" class="pto-btn-on-post-add">
                    <?php esc_html_e('Add to cart', 'shokrino' ); ?>
                </a>
                <?php } else { ?>
                <a href="<?php if ($addtocart_link_product_offer_on_post) {echo $addtocart_link_product_offer_on_post;} else {echo $permalink_pro;} ?>" class="pto-btn-on-post-more">
                    <?php if (!empty($addtocart_text_product_offer_on_post)) {
                        echo $addtocart_text_product_offer_on_post;
                    } else {
                        esc_html_e('Show product', 'shokrino');
                    } ?>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<style>
    .product-offer-on-post {
        width: 100%;
        float: right;
        background-color: #fff;
        margin: 30px auto 0 auto;
        border-radius: 35px;
        box-shadow: 0 12px 50px 10px rgba(61,130,179,.08);
        overflow: hidden;
        padding: 25px;
        display: flex;
        justify-content: center;
        position: relative;
    }
    .price-pro-on-post-box {
        display: flex;
        margin-top: auto;
        border-bottom: 3px solid #1cbae9;
        width: 100% !important;
        padding: unset !important;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
    }
    .price-pro-on-post-box .price {
        width: fit-content !important;
        margin-top: auto;
        margin-bottom: 3px;
        padding-right: 0px !important;
    }
    .pro-on-post-item.pro-titles-box-on-post {
        padding: 12px 30px 0px 0px;
    }
    .pro-on-post-row {
        display: flex;
        justify-content: space-between;
        width: 100%;
        flex-wrap: nowrap;
    }
    .pro-on-post-item {
        display: flex;
        width: 50% !important;
        overflow: hidden;
        float: right;
        justify-content: center;
        flex-direction: column;
        flex-wrap: nowrap;
    }
    .pro-on-post-item > img {
        display: block;
        margin: auto;
        width: 100%;
        height: auto;
        border-radius: 20px;
    }
    .add-to-cart-btn-on-post > a {
        margin-bottom: auto;
        background-color: #29ca4a;
        box-shadow: 0 2px 12px rgba(31,189,80,.27);
        border-radius: 9px;
        font-size: 19px;
        color: white;
        font-size: 18px;
        padding: 5px 30px;
    }
    .add-to-cart-btn-on-post {
        display: flex;
        width: 100%;
        justify-content: center;
        margin-top: auto;
    }
    .pro-on-post-item > h2 {
        font-weight: 800;
        text-align: right;
    }
    .pro-on-post-item li {
        list-style: unset !important;
        margin-right: 10px;
    }
    @media screen and (max-width: 992px) {
        .pro-on-post-item {
            display: block !important;
        }
    }
    @media screen and (max-width: 780px) {
        .pro-on-post-row {
            flex-wrap: wrap !important;
        }
        .pro-on-post-item.pro-titles-box-on-post {
            padding: 5px 5px 0;
        }
        .pro-on-post-item {
            width: 100% !important;
        }
        .pro-on-post-item > h2 {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .add-to-cart-btn-on-post {
            margin-top: 10px;
        }
    }
</style>
<?php
defined('ABSPATH') || exit;

function make_product_item_shkrn($place='slider',$type='course') {
    ?>
    <div class="<?php echo $place=='archive' ? 'archive-product-shkrn ' : ''; echo $place=='slider' ? 'slider-product-shkrn swiper-slide ' : ''; ?>box-course">
        <a class="course-openner-link-box-shkrn" href="<?php the_permalink(); ?>">
            <?php
            global $product;
            $course_sale_tag = shkrn_options('course_sale_tag');
            $courses_rating = shkrn_options('courses_rating');
            $courses_teacher = shkrn_options('courses_teacher');
            $courses_customer = shkrn_options('courses_customer');
            $courses_addtocart = shkrn_options('courses_addtocart');
            $courses_price = shkrn_options('courses_price');
            if ($product->is_on_sale() && $course_sale_tag == 1) { ?>
                <div class="badge-darsad">
                    <?php
                    $max_darsad = 0;
                    $max_darsad = (($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100; ?>
                    <span class="content-darsad">
                        <?php echo round($max_darsad); ?>
                        %
                    </span>
                </div>
            <?php } ?>
            <?php if (has_post_thumbnail()) {
                the_post_thumbnail(optimize_img_shkrn('posts'));
            } else { ?>
                <img src="<?php echo SHKRN_ASSETS . '/img/0.jpg'; ?>">
            <?php } ?>
            <h2><?php the_title(); ?></h2>
            <div class="course-item-row-detail woocommerce flex">
                <div class="course-teacher-shkrn"><?php if ($courses_teacher == 1) { ?>
                        <?php
                        $teacher_id = redux_post_meta( "shokrino_options" , get_the_ID(), 'teacher_choose');
                        if (!empty($teacher_id)) { ?>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="var(--shkrn-var-main-color)" viewBox="0 0 640 512" height="16px"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M640 49.59v316.82c0 27.34-21.53 49.59-48 49.59H343.79c-.9-2.53-1.86-5.05-2.9-7.54A144.52 144.52 0 0 0 298.51 352H576V64H224v49.1a127 127 0 0 0-59.46-17h-.33c-1.4 0-2.8-.08-4.21-.08V49.59C160 22.25 181.53 0 208 0h384c26.47 0 48 22.25 48 49.59z" class="fa-secondary"></path><path d="M208 352a22.88 22.88 0 0 0-7.06 1.09A131.91 131.91 0 0 1 160 360a132 132 0 0 1-40.95-6.91 22.82 22.82 0 0 0-7-1.09A112 112 0 0 0 0 464.62C.14 490.88 21.73 512 48 512h224c26.27 0 47.86-21.12 48-47.38A112 112 0 0 0 208 352zm-48-32a96 96 0 1 0-96-96 96 96 0 0 0 96 96zm64-256v49.09a129 129 0 0 1 26.51 20.4 128 128 0 0 1 0 181 132.13 132.13 0 0 1-10.14 9.14A143.89 143.89 0 0 1 298.51 352H384v-64h128v64h64V64z" class="fa-primary"></path></svg>
                            <span><?php echo esc_attr( get_the_title( $teacher_id ) ); ?></span>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="rate"><?php if ($courses_rating == 1) { ?>
                        <?php woocommerce_template_loop_rating() ?>
                    <?php } ?>
                </div>
            </div>
            <div class="course-item-row-detail product-shkrn-<?php echo get_the_ID(); ?> flex">
                <?php if ($courses_customer == 1 && product_type_shkrn() == 'course') { ?>
                <div class="buyers-of-course-shkrn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="var(--shkrn-var-main-color)" viewBox="0 0 640 512" height="16px" style="margin-top: -3px;"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M96 224a64 64 0 1 0-64-64 64.06 64.06 0 0 0 64 64zm480 32h-64a63.81 63.81 0 0 0-45.1 18.6A146.27 146.27 0 0 1 542 384h66a32 32 0 0 0 32-32v-32a64.06 64.06 0 0 0-64-64zm-512 0a64.06 64.06 0 0 0-64 64v32a32 32 0 0 0 32 32h65.9a146.64 146.64 0 0 1 75.2-109.4A63.81 63.81 0 0 0 128 256zm480-32a64 64 0 1 0-64-64 64.06 64.06 0 0 0 64 64z" class="fa-secondary"/><path d="M396.8 288h-8.3a157.53 157.53 0 0 1-68.5 16c-24.6 0-47.6-6-68.5-16h-8.3A115.23 115.23 0 0 0 128 403.2V432a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48v-28.8A115.23 115.23 0 0 0 396.8 288zM320 256a112 112 0 1 0-112-112 111.94 111.94 0 0 0 112 112z" class="fa-primary"/></svg>
                    <div class="count-users-pro-shkrn"> <?php
                        $students_count_default = get_post_meta(get_the_ID(), 'total_sales', true);
                        $students_added_number = redux_post_meta("shokrino_options", get_the_ID(), 'add-to-students-number');
                        $students_total_number = $students_count_default + $students_added_number;
                        echo $students_total_number;
                        ?>
                    </div>
                </div>
                <?php } else { ?>
                    <style>
                        .course-item-row-detail.product-shkrn-<?php echo get_the_ID(); ?> .price {
                            margin: auto;
                        }
                    </style>
                <?php } ?>
                <?php if ($courses_price) { ?>
                    <div class="price">
                        <?php make_price_shkrn(); ?>
                    </div>
                <?php } ?>
            </div>
            <div class="course-item-row-detail product-counter-shkrn flex">
                <?php
                require SHKRN_WOO . '/page-parts/sale-counter.php';
                ?>
            </div>
        </a>
        <?php
        if ($courses_addtocart == 1) {
            woocommerce_template_loop_add_to_cart();
        } ?>
    </div>
    <?php
}

function make_addtocart_btn_shkrn($place,$type='course') {
    $id = get_the_ID();
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
    }
    $add_to_cart_text = shkrn_options('add_to_cart_text');
    $course_student_text = shkrn_options('course_student_text');
    $cart_button_name_public = $add_to_cart_text;
    $cart_button_name_this_product = redux_post_meta("shokrino_options", get_the_ID(), 'course_add_to_cart_text');
    $cart_button_name = !empty($cart_button_name_this_product) ? $cart_button_name_this_product : $cart_button_name_public;
    if ($place == 'item') {
        $courses_addtocart = shkrn_options('courses_addtocart');
        $class_btn_addtocart = 'btn-more-course-shkrn';
        if ($courses_addtocart == 1) {
            if (!is_product_variable_shkrn()) {
                if (shkrn_stock_status_product($id) == "outofstock") { ?>
                        <a class="<?php echo $class_btn_addtocart; ?> endofssell-btn-more flex-center">
                            <?php $endofsell_button_name = redux_post_meta("shokrino_options", get_the_ID(), 'endofsell_text');
                            if (!empty($endofsell_button_name)) { ?>
                                <?php echo $endofsell_button_name ?>
                            <?php } else { ?>
                                <?php esc_html_e('Not available', 'shokrino'); ?>
                            <?php } ?>
                            <style>
                                .endofssell-btn-more {
                                    background: #939393 !important;
                                    color: white !important;
                                }
                                .endofssell-btn-more:hover {
                                    background: #939393;
                                    color: white !important;
                                }
                            </style>
                        </a>
                <?php } else {
                    $is_downloady = redux_post_meta("shokrino_options", $id, 'downloady_product_check');
                    if ($is_downloady != 'true') {
                        if (!wc_customer_bought_product($current_user->user_email, $current_user->ID, $id)) {
                        if (!empty($cart_button_name)) { ?>
                            <a class="<?php echo $class_btn_addtocart; ?>" href="<?php echo do_shortcode("[add_to_cart_url id=$id]"); ?>">
                                <?php echo $cart_button_name ?>
                            </a>
                        <?php } else { ?>
                            <a class="<?php echo $class_btn_addtocart; ?>" href="<?php echo do_shortcode("[add_to_cart_url id=$id]"); ?>">
                                <?php if (!empty($add_to_cart_text)) {
                                    echo $add_to_cart_text;
                                } else {
                                    $is_downloady = redux_post_meta("shokrino_options", $id, 'downloady_product_check');
                                    if ($is_downloady != 'true') { ?>
                                        <?php esc_html_e('Add to cart', 'shokrino' ); ?>
                                    <?php } else { ?>
                                        <?php esc_html_e('Register in course', 'shokrino' ); ?>
                                    <?php } } ?>
                            </a>
                        <?php } } else { ?>
                            <a class="<?php echo $class_btn_addtocart; ?>">
                                <?php if (!empty($course_student_text)) {
                                    echo $course_student_text;
                                } else { ?>
                                    <?php esc_html_e('You are a course student!', 'shokrino'); ?>
                                <?php } ?>
                            </a>
                        <?php }
                    } else { ?>
                        <a class="<?php echo $class_btn_addtocart; ?>" href="<?php echo do_shortcode("[add_to_cart_url id=$id]"); ?>">
                            <?php esc_html_e('Add to cart', 'shokrino' ); ?>
                        </a>
                    <?php }
                } ?>
        <?php } else { ?>
                <a href="<?php the_permalink(); ?>" class="<?php echo $class_btn_addtocart; ?>">
                    <?php esc_html_e('مشاهده گزینه ها', 'shokrino' ); ?>
                </a>
            <?php }
        }
    } elseif ($place == 'single') {
        do_action('woocommerce_before_add_to_cart_button');
                if ($type == 'course') {
                    if (shkrn_stock_status_product($id) == "outofstock") {?>
                            <div class="endofssell-btn addtocart-btn-shkrn flex-center">
                                <?php $endofsell_button_name = skrn_metaoptions('endofsell_text');
                                if (!empty($endofsell_button_name)) { ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M576 224H0V128H576V224z"/><path class="fa-secondary" d="M512 32C547.3 32 576 60.65 576 96V128H0V96C0 60.65 28.65 32 64 32H512zM576 416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V224H576V416zM112 352C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384H176C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352H112zM240 384H368C376.8 384 384 376.8 384 368C384 359.2 376.8 352 368 352H240C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384z"/></svg>&nbsp;&nbsp;<?php echo $endofsell_button_name ?>
                                <?php } else { ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M576 224H0V128H576V224z"/><path class="fa-secondary" d="M512 32C547.3 32 576 60.65 576 96V128H0V96C0 60.65 28.65 32 64 32H512zM576 416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V224H576V416zM112 352C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384H176C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352H112zM240 384H368C376.8 384 384 376.8 384 368C384 359.2 376.8 352 368 352H240C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384z"/></svg>&nbsp;&nbsp;<?php esc_html_e('Not available', 'shokrino'); ?>
                                <?php } ?>
                            </div>
                            <style>
                                .endofssell-btn {
                                    background-color: #939393;
                                    color: white;
                                    box-shadow: 0 2px 12px rgba(138, 138, 138, 0.27);
                                }
                                .endofssell-btn:hover {
                                    background-color: #939393;
                                    box-shadow: none;
                                    cursor: not-allowed;
                                }
                            </style>
                        <?php } else {
                            if (!wc_customer_bought_product($current_user->user_email, $current_user->ID, $id)) {
								if( in_array( $id, array_column( WC()->cart->get_cart(), 'product_id' ) ) ) { ?>
								<a class="addtocart-course-btn addtocart-btn-shkrn flex-center" href="<?php echo wc_get_cart_url(); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M576 224H0V128H576V224z"/><path class="fa-secondary" d="M512 32C547.3 32 576 60.65 576 96V128H0V96C0 60.65 28.65 32 64 32H512zM576 416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V224H576V416zM112 352C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384H176C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352H112zM240 384H368C376.8 384 384 376.8 384 368C384 359.2 376.8 352 368 352H240C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384z"/></svg>&nbsp;&nbsp;
                                            <?php esc_html_e('نهایی سازی خرید', 'shokrino'); ?>
                                </a>
                                <?php } else { ?>
								<a class="addtocart-course-btn addtocart-btn-shkrn flex-center" href="<?php echo do_shortcode("[add_to_cart_url id=$id]"); ?>">
                                    <?php
                                    $cart_button_name = skrn_metaoptions('course_add_to_cart_text');
                                    if (!empty($cart_button_name)) { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M576 224H0V128H576V224z"/><path class="fa-secondary" d="M512 32C547.3 32 576 60.65 576 96V128H0V96C0 60.65 28.65 32 64 32H512zM576 416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V224H576V416zM112 352C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384H176C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352H112zM240 384H368C376.8 384 384 376.8 384 368C384 359.2 376.8 352 368 352H240C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384z"/></svg>&nbsp;&nbsp;
                                            <?php echo $cart_button_name ?>
                                    <?php } else { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M576 224H0V128H576V224z"/><path class="fa-secondary" d="M512 32C547.3 32 576 60.65 576 96V128H0V96C0 60.65 28.65 32 64 32H512zM576 416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V224H576V416zM112 352C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384H176C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352H112zM240 384H368C376.8 384 384 376.8 384 368C384 359.2 376.8 352 368 352H240C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384z"/></svg>&nbsp;&nbsp;
                                            <?php if (!empty($add_to_cart_text)) {
                                                echo $add_to_cart_text;
                                            } else { ?>
                                                <?php esc_html_e('Register in course', 'shokrino'); ?>
                                            <?php } ?>
                                    <?php }
                                    ?>
                                </a>
                            <?php } } else { ?>
                                <a class="addtocart-course-bought-btn addtocart-btn-shkrn flex-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M359.2 312.8l-103.2 103.2l-103.2-103.2c-69.93 22.3-120.8 87.2-120.8 164.5C32 496.5 47.53 512 66.67 512h378.7C464.5 512 480 496.5 480 477.3C480 400 429.1 335.1 359.2 312.8zM45.63 79.75L52 81.25v58.5C45 143.9 40 151.3 40 160c0 8.375 4.625 15.38 11.12 19.75L35.5 242C33.75 248.9 37.63 256 43.13 256h41.75c5.5 0 9.375-7.125 7.625-13.1L76.88 179.8C83.38 175.4 88 168.4 88 160c0-8.75-5-16.12-12-20.25V87.13L128 99.63l.001 60.37h255.1L384 99.62l82.25-19.87c18.25-4.375 18.25-27 0-31.5l-190.4-46c-13-3-26.62-3-39.63 0l-190.6 46C27.5 52.63 27.5 75.38 45.63 79.75z"/><path class="fa-secondary" d="M384 159.1c0 70.75-57.25 128-127.1 128S128 230.7 128 159.1H384z"/></svg>&nbsp;&nbsp;
                                    <?php if (!empty($course_student_text)) {
                                        echo $course_student_text;
                                    } else { ?>
                                        <?php esc_html_e('You are a course student!', 'shokrino'); ?>
                                    <?php } ?>
                                </a>
                            <?php }
                        }
                }
        do_action('woocommerce_after_add_to_cart_button');
    }
}

function make_price_shkrn() {
    global $woocommerce;
    $product = new WC_Product(get_the_ID());
    if (empty($product->get_sale_price()) && !empty($product->get_regular_price())) {
        $price = $product->get_price_html();
        ?>
        <span class="woocommerce-free-course-list-amount"><?php echo $price ?></span><?php
    } else {
        global $woocommerce;
        $product = new WC_Product(get_the_ID());
        echo $product->get_price_html();
    }
}
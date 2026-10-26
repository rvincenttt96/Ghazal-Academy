<?php
defined( 'ABSPATH' ) || exit;

function teachers_post_type() {
    $labels = array(
        'name'               => __( 'مدرسان' ),
        'singular_name'      => __( 'مدرس' ),
        'menu_name'          => __( 'مدرسان' ),
        'name_admin_bar'     => __( 'مدرسان' ),
        'add_new'            => __( 'افزودن مدرس' ),
        'add_new_item'       => __( 'افزودن مدرس' ),
        'new_item'           => __( 'مدرس جدید' ),
        'edit_item'          => __( 'ویرایش مشخصات مدرس' ),
        'view_item'          => __( 'مشاهده مدرس' ),
        'all_items'          => __( 'مدرسان' ),
        'search_items'       => __( 'جستجو در بین مدرسان' ),
        'parent_item_colon'  => __( 'دسته سطح بالاتر مدرس' ),
        'not_found'          => __( 'مدرس یافت نشد' ),
        'not_found_in_trash' => __( 'نام مدرس در زباله دان یافت نشد' )
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'توضیحات', 'shokrino' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          =>         'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxOTE3LjQ0IDE5MTcuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDE5MTcuNDQgMTkxNy40NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0iI2ZmZiI+DQogICAgICAgIDxnPg0KICAgICAgICAgICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTkyOS4yMSwxNzUxYy01Mi4yLDAtMTAzLjEyLTEwLjEzLTE1MS4zNS0zMC4xYy00OC4yMy0xOS45OC05MS40LTQ4LjgyLTEyOC4zMS04NS43M2wtMTE2LjU0LTExNi41NCAgIGMtOTIuODYtOTIuODYtOTIuODYtMjQzLjQxLDAtMzM2LjI3bDAsMGwyOTkuMTEsMjk5LjExYzMuOTcsMy45Nyw4LjUsNy4zMiwxMy40NSw5Ljk3YzY5LjkxLDM3LjQ5LDE0NS45NCwyNC44MSwxOTUuMTYtMjQuNDEgICBsMy44OC0zLjg4YzYxLjUtNjEuNSw2MS41LTE2MS41NiwwLTIyMy4wNWwtOTAuMTgtOTAuMThsMTY4LjEzLTE2OC4xM2w5MC4xOCw5MC4xOGMzNy43MiwzNy43Miw2Ni45NSw4MS43Niw4Ni44NywxMzAuODkgICBjMTkuMjIsNDcuNCwyOC45Nyw5Ny40NSwyOC45NywxNDguNzdjMCw1MS4zMS05Ljc1LDEwMS4zNi0yOC45NywxNDguNzdjLTE5LjkzLDQ5LjE0LTQ5LjE1LDkzLjE4LTg2Ljg3LDEzMC44OWwtMTEuNDQsMTEuNDQgICBjLTExLjcxLDExLjcxLTI0LjMsMjIuNTItMzcuODEsMzIuMUMxMDkyLjQ4LDE3MjUuMjMsMTAxMi40NywxNzUxLDkyOS4yMSwxNzUxeiIvPg0KICAgICAgICA8L2c+DQogICAgPGc+DQogICAgICAgICAgICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzM1Ljg2LDkzNi4yN2wtOTAuMTgtOTAuMThjLTM3LjcyLTM3LjcyLTY2Ljk0LTgxLjc2LTg2Ljg3LTEzMC44OWMtMTkuMjItNDcuNC0yOC45Ny05Ny40NS0yOC45Ny0xNDguNzcgICBzOS43NS0xMDEuMzYsMjguOTctMTQ4Ljc2YzE5LjkzLTQ5LjE0LDQ5LjE1LTkzLjE4LDg2Ljg3LTEzMC44OWwzLjg4LTMuODhjMzYuOTEtMzYuOTEsODAuMDgtNjUuNzYsMTI4LjMxLTg1LjczICAgYzQ4LjIzLTE5Ljk4LDk5LjE1LTMwLjExLDE1MS4zNS0zMC4xMWM1Mi4yLDAsMTAzLjEyLDEwLjEzLDE1MS4zNSwzMC4xMWM0OC4yMywxOS45OCw5MS40LDQ4LjgyLDEyOC4zMSw4NS43M2wxMDkuODEsMTA5LjgxICAgYzkyLjg2LDkyLjg2LDkyLjg2LDI0My40MSwwLDMzNi4yN2wwLDBsLTI3Ny45NC0yNzcuOTRjLTI5Ljc5LTI5Ljc5LTY5LjQtNDYuMi0xMTEuNTItNDYuMTljLTQyLjEzLDAtODEuNzQsMTYuNDEtMTExLjUyLDQ2LjIgICBsLTMuODgsMy44OGMtNjEuNSw2MS40OS02MS41LDE2MS41NSwwLDIyMy4wNWw5MC4xOCw5MC4xOEw3MzUuODYsOTM2LjI3eiIvPg0KICAgICAgICA8L2c+DQogICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0MDcuNjYsODc2Ljc0TDEwMTEuNyw0ODAuNzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzICBsMTEzLjM0LDExMy4zNEw3MzEuNTUsOTg3LjYyTDYxOS4zMiw4NzUuMzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzbDM5NS45NSwzOTUuOTUgIGM0Ni40Myw0Ni40MywxMjEuNyw0Ni40MywxNjguMTMsMGwwLDBjNDYuNDMtNDYuNDMsNDYuNDMtMTIxLjcsMC0xNjguMTNsLTExNS41OC0xMTUuNThsMjI1LjM2LTIyNS4zNmwxMTQuNDgsMTE0LjQ4ICBjNDYuNDMsNDYuNDMsMTIxLjcsNDYuNDMsMTY4LjEzLDBsMCwwQzE0NTQuMDksOTk4LjQ0LDE0NTQuMDksOTIzLjE3LDE0MDcuNjYsODc2Ljc0eiIvPg0KICAgIDwvc3ZnPg==',
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array('slug' => 'teacher','with_front' => false),
    );
    register_post_type( 'teacher', $args );
}

add_action('init' , 'teachers_post_type');

function get_all_teacher() {

    $teachers = array(
        'no-teacher' => esc_html__( 'Choose a Teacher', 'shokrino' ),
    );

    $teachers_args = array(
        'post_type'     => 'teacher',
        'post_status'   => 'publish',
        'posts_per_page'=> -1,
    );

    $teachers_query = new WP_Query( $teachers_args );

    foreach( $teachers_query->posts as $teacher ){
        $teachers[$teacher->ID] = $teacher->post_title;
    }

    return $teachers;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function post_type_videos_Matin() {
    $labels = array(
        'name'               => __( 'ویدئو ها' , 'shokrino' ),
        'singular_name'      => __( 'ویدئو ها' , 'shokrino' ),
        'menu_name'          => __( 'ویدئو ها' , 'shokrino' ),
        'name_admin_bar'     => __( 'ویدئو ها' , 'shokrino' ),
        'add_new'            => __( ' افزودن جدید' , 'shokrino' ),
        'add_new_item'       => __( 'پست مخصوص ویدیوهای آموزشی' , 'shokrino' ),
        'new_item'           => __( 'پست جدید' , 'shokrino' ),
        'edit_item'          => __( 'ویرایش پست' , 'shokrino' ),
        'view_item'          => __( 'مشاهده پست' , 'shokrino' ),
        'all_items'          => __( 'همه ویدیوها' , 'shokrino' ),
        'search_items'       => __( 'جستجو در بین ویدیوها' , 'shokrino' ),
        'parent_item_colon'  => __( 'مادر' , 'shokrino' ),
        'not_found'          => __( 'مطلب یافت نشد' , 'shokrino' ),
        'not_found_in_trash' => __( 'مطلب در زباله دان یافت نشد' )
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'توضیحات.' , 'shokrino' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_icon'          =>         'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxOTE3LjQ0IDE5MTcuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDE5MTcuNDQgMTkxNy40NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0iI2ZmZiI+DQogICAgICAgIDxnPg0KICAgICAgICAgICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTkyOS4yMSwxNzUxYy01Mi4yLDAtMTAzLjEyLTEwLjEzLTE1MS4zNS0zMC4xYy00OC4yMy0xOS45OC05MS40LTQ4LjgyLTEyOC4zMS04NS43M2wtMTE2LjU0LTExNi41NCAgIGMtOTIuODYtOTIuODYtOTIuODYtMjQzLjQxLDAtMzM2LjI3bDAsMGwyOTkuMTEsMjk5LjExYzMuOTcsMy45Nyw4LjUsNy4zMiwxMy40NSw5Ljk3YzY5LjkxLDM3LjQ5LDE0NS45NCwyNC44MSwxOTUuMTYtMjQuNDEgICBsMy44OC0zLjg4YzYxLjUtNjEuNSw2MS41LTE2MS41NiwwLTIyMy4wNWwtOTAuMTgtOTAuMThsMTY4LjEzLTE2OC4xM2w5MC4xOCw5MC4xOGMzNy43MiwzNy43Miw2Ni45NSw4MS43Niw4Ni44NywxMzAuODkgICBjMTkuMjIsNDcuNCwyOC45Nyw5Ny40NSwyOC45NywxNDguNzdjMCw1MS4zMS05Ljc1LDEwMS4zNi0yOC45NywxNDguNzdjLTE5LjkzLDQ5LjE0LTQ5LjE1LDkzLjE4LTg2Ljg3LDEzMC44OWwtMTEuNDQsMTEuNDQgICBjLTExLjcxLDExLjcxLTI0LjMsMjIuNTItMzcuODEsMzIuMUMxMDkyLjQ4LDE3MjUuMjMsMTAxMi40NywxNzUxLDkyOS4yMSwxNzUxeiIvPg0KICAgICAgICA8L2c+DQogICAgPGc+DQogICAgICAgICAgICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzM1Ljg2LDkzNi4yN2wtOTAuMTgtOTAuMThjLTM3LjcyLTM3LjcyLTY2Ljk0LTgxLjc2LTg2Ljg3LTEzMC44OWMtMTkuMjItNDcuNC0yOC45Ny05Ny40NS0yOC45Ny0xNDguNzcgICBzOS43NS0xMDEuMzYsMjguOTctMTQ4Ljc2YzE5LjkzLTQ5LjE0LDQ5LjE1LTkzLjE4LDg2Ljg3LTEzMC44OWwzLjg4LTMuODhjMzYuOTEtMzYuOTEsODAuMDgtNjUuNzYsMTI4LjMxLTg1LjczICAgYzQ4LjIzLTE5Ljk4LDk5LjE1LTMwLjExLDE1MS4zNS0zMC4xMWM1Mi4yLDAsMTAzLjEyLDEwLjEzLDE1MS4zNSwzMC4xMWM0OC4yMywxOS45OCw5MS40LDQ4LjgyLDEyOC4zMSw4NS43M2wxMDkuODEsMTA5LjgxICAgYzkyLjg2LDkyLjg2LDkyLjg2LDI0My40MSwwLDMzNi4yN2wwLDBsLTI3Ny45NC0yNzcuOTRjLTI5Ljc5LTI5Ljc5LTY5LjQtNDYuMi0xMTEuNTItNDYuMTljLTQyLjEzLDAtODEuNzQsMTYuNDEtMTExLjUyLDQ2LjIgICBsLTMuODgsMy44OGMtNjEuNSw2MS40OS02MS41LDE2MS41NSwwLDIyMy4wNWw5MC4xOCw5MC4xOEw3MzUuODYsOTM2LjI3eiIvPg0KICAgICAgICA8L2c+DQogICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0MDcuNjYsODc2Ljc0TDEwMTEuNyw0ODAuNzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzICBsMTEzLjM0LDExMy4zNEw3MzEuNTUsOTg3LjYyTDYxOS4zMiw4NzUuMzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzbDM5NS45NSwzOTUuOTUgIGM0Ni40Myw0Ni40MywxMjEuNyw0Ni40MywxNjguMTMsMGwwLDBjNDYuNDMtNDYuNDMsNDYuNDMtMTIxLjcsMC0xNjguMTNsLTExNS41OC0xMTUuNThsMjI1LjM2LTIyNS4zNmwxMTQuNDgsMTE0LjQ4ICBjNDYuNDMsNDYuNDMsMTIxLjcsNDYuNDMsMTY4LjEzLDBsMCwwQzE0NTQuMDksOTk4LjQ0LDE0NTQuMDksOTIzLjE3LDE0MDcuNjYsODc2Ljc0eiIvPg0KICAgIDwvc3ZnPg==',
        'menu_position'      => 6,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );
    register_post_type( 'tv', $args );
}

add_action('init' , 'post_type_videos_Matin');

function create_taxonomies_for_tv() {
    $labels = array(
        'name'              => _x( 'دسته بندی' , 'shokrino' ),
        'singular_name'     => _x( 'دسته بندی ویدیو ها ' , 'shokrino' ),
        'search_items'      => __( 'جستجویه دسته' , 'shokrino' ),
        'all_items'         => __( 'تمام دسته ها' , 'shokrino' ),
        'parent_item'       => __( 'زیر دسته' , 'shokrino' ),
        'parent_item_colon' => __( 'دسته بندی مادر:' , 'shokrino' ),
        'edit_item'         => __( 'ویرایش دسته' , 'shokrino' ),
        'update_item'       => __( 'بروزرسانی دسته' , 'shokrino' ),
        'add_new_item'      => __( 'افزودن دسته جدید' , 'shokrino' ),
        'new_item_name'     => __( 'نام جدید دسته' , 'shokrino' ),
        'menu_name'         => __( 'دسته بندی' , 'shokrino' ),
    );

    $ar = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
    );

    register_taxonomy( 'cat_tv', 'tv' , $ar );

global $wpdb;

// Change 'cat_shokrino_tv' to 'cat_tv'
$old_taxonomy = 'cat_shokrino_tv';
$new_taxonomy = 'cat_tv';

// Update wp_term_taxonomy table
$wpdb->update(
    $wpdb->term_taxonomy,
    array('taxonomy' => $new_taxonomy),
    array('taxonomy' => $old_taxonomy)
);

// Update wp_options table if necessary
$wpdb->update(
    $wpdb->options,
    array('option_value' => str_replace($old_taxonomy, $new_taxonomy, get_option('category_base'))),
    array('option_name' => 'category_base')
);

}
add_action( 'init', 'create_taxonomies_for_tv');

function get_all_tv() {

    $tvs = array(
        'no-tv' => esc_html__( 'انتخاب یک ویدیو', 'shokrino' ),
    );

    $tvs_args = array(
        'post_type'     => 'tv',
        'post_status'   => 'publish',
        'posts_per_page'=> -1,
    );

    $tvs_query = new WP_Query( $tvs_args );

    foreach( $tvs_query->posts as $tv ){
        $tvs[$tv->ID] = $tv->post_title;
    }

    return $tvs;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function post_type_podcasts_Matin() {
    $labels = array(
        'name'               => __( 'پادکست ها' , 'shokrino' ),
        'singular_name'      => __( 'پادکست ها' , 'shokrino' ),
        'menu_name'          => __( 'پادکست ها' , 'shokrino' ),
        'name_admin_bar'     => __( 'پادکست ها' , 'shokrino' ),
        'add_new'            => __( ' افزودن جدید' , 'shokrino' ),
        'add_new_item'       => __( 'پست مخصوص پادکست ها' , 'shokrino' ),
        'new_item'           => __( 'پست جدید' , 'shokrino' ),
        'edit_item'          => __( 'ویرایش پست' , 'shokrino' ),
        'view_item'          => __( 'مشاهده پست' , 'shokrino' ),
        'all_items'          => __( 'همه پادکست ها' , 'shokrino' ),
        'search_items'       => __( 'جستجو در بین پادکست ها' , 'shokrino' ),
        'parent_item_colon'  => __( 'مادر' , 'shokrino' ),
        'not_found'          => __( 'مطلب یافت نشد' , 'shokrino' ),
        'not_found_in_trash' => __( 'مطلب در زباله دان یافت نشد' )
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'توضیحات.' , 'shokrino' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_icon'          =>         'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxOTE3LjQ0IDE5MTcuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDE5MTcuNDQgMTkxNy40NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0iI2ZmZiI+DQogICAgICAgIDxnPg0KICAgICAgICAgICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTkyOS4yMSwxNzUxYy01Mi4yLDAtMTAzLjEyLTEwLjEzLTE1MS4zNS0zMC4xYy00OC4yMy0xOS45OC05MS40LTQ4LjgyLTEyOC4zMS04NS43M2wtMTE2LjU0LTExNi41NCAgIGMtOTIuODYtOTIuODYtOTIuODYtMjQzLjQxLDAtMzM2LjI3bDAsMGwyOTkuMTEsMjk5LjExYzMuOTcsMy45Nyw4LjUsNy4zMiwxMy40NSw5Ljk3YzY5LjkxLDM3LjQ5LDE0NS45NCwyNC44MSwxOTUuMTYtMjQuNDEgICBsMy44OC0zLjg4YzYxLjUtNjEuNSw2MS41LTE2MS41NiwwLTIyMy4wNWwtOTAuMTgtOTAuMThsMTY4LjEzLTE2OC4xM2w5MC4xOCw5MC4xOGMzNy43MiwzNy43Miw2Ni45NSw4MS43Niw4Ni44NywxMzAuODkgICBjMTkuMjIsNDcuNCwyOC45Nyw5Ny40NSwyOC45NywxNDguNzdjMCw1MS4zMS05Ljc1LDEwMS4zNi0yOC45NywxNDguNzdjLTE5LjkzLDQ5LjE0LTQ5LjE1LDkzLjE4LTg2Ljg3LDEzMC44OWwtMTEuNDQsMTEuNDQgICBjLTExLjcxLDExLjcxLTI0LjMsMjIuNTItMzcuODEsMzIuMUMxMDkyLjQ4LDE3MjUuMjMsMTAxMi40NywxNzUxLDkyOS4yMSwxNzUxeiIvPg0KICAgICAgICA8L2c+DQogICAgPGc+DQogICAgICAgICAgICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzM1Ljg2LDkzNi4yN2wtOTAuMTgtOTAuMThjLTM3LjcyLTM3LjcyLTY2Ljk0LTgxLjc2LTg2Ljg3LTEzMC44OWMtMTkuMjItNDcuNC0yOC45Ny05Ny40NS0yOC45Ny0xNDguNzcgICBzOS43NS0xMDEuMzYsMjguOTctMTQ4Ljc2YzE5LjkzLTQ5LjE0LDQ5LjE1LTkzLjE4LDg2Ljg3LTEzMC44OWwzLjg4LTMuODhjMzYuOTEtMzYuOTEsODAuMDgtNjUuNzYsMTI4LjMxLTg1LjczICAgYzQ4LjIzLTE5Ljk4LDk5LjE1LTMwLjExLDE1MS4zNS0zMC4xMWM1Mi4yLDAsMTAzLjEyLDEwLjEzLDE1MS4zNSwzMC4xMWM0OC4yMywxOS45OCw5MS40LDQ4LjgyLDEyOC4zMSw4NS43M2wxMDkuODEsMTA5LjgxICAgYzkyLjg2LDkyLjg2LDkyLjg2LDI0My40MSwwLDMzNi4yN2wwLDBsLTI3Ny45NC0yNzcuOTRjLTI5Ljc5LTI5Ljc5LTY5LjQtNDYuMi0xMTEuNTItNDYuMTljLTQyLjEzLDAtODEuNzQsMTYuNDEtMTExLjUyLDQ2LjIgICBsLTMuODgsMy44OGMtNjEuNSw2MS40OS02MS41LDE2MS41NSwwLDIyMy4wNWw5MC4xOCw5MC4xOEw3MzUuODYsOTM2LjI3eiIvPg0KICAgICAgICA8L2c+DQogICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0MDcuNjYsODc2Ljc0TDEwMTEuNyw0ODAuNzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzICBsMTEzLjM0LDExMy4zNEw3MzEuNTUsOTg3LjYyTDYxOS4zMiw4NzUuMzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzbDM5NS45NSwzOTUuOTUgIGM0Ni40Myw0Ni40MywxMjEuNyw0Ni40MywxNjguMTMsMGwwLDBjNDYuNDMtNDYuNDMsNDYuNDMtMTIxLjcsMC0xNjguMTNsLTExNS41OC0xMTUuNThsMjI1LjM2LTIyNS4zNmwxMTQuNDgsMTE0LjQ4ICBjNDYuNDMsNDYuNDMsMTIxLjcsNDYuNDMsMTY4LjEzLDBsMCwwQzE0NTQuMDksOTk4LjQ0LDE0NTQuMDksOTIzLjE3LDE0MDcuNjYsODc2Ljc0eiIvPg0KICAgIDwvc3ZnPg==',
        'menu_position'      => 6,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );
    register_post_type( 'podcast', $args );
}

add_action('init' , 'post_type_podcasts_Matin');

function create_taxonomies_for_podcast() {
    $labels = array(
        'name'              => _x( 'دسته بندی' , 'shokrino' ),
        'singular_name'     => _x( 'دسته بندی پادکست ها ' , 'shokrino' ),
        'search_items'      => __( 'جستجویه دسته' , 'shokrino' ),
        'all_items'         => __( 'تمام دسته ها' , 'shokrino' ),
        'parent_item'       => __( 'زیر دسته' , 'shokrino' ),
        'parent_item_colon' => __( 'دسته بندی مادر:' , 'shokrino' ),
        'edit_item'         => __( 'ویرایش دسته' , 'shokrino' ),
        'update_item'       => __( 'بروزرسانی دسته' , 'shokrino' ),
        'add_new_item'      => __( 'افزودن دسته جدید' , 'shokrino' ),
        'new_item_name'     => __( 'نام جدید دسته' , 'shokrino' ),
        'menu_name'         => __( 'دسته بندی' , 'shokrino' ),
    );

    $ar = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
    );

    register_taxonomy( 'cat_podcast', 'podcast' , $ar );
    
global $wpdb;

// Change 'cat_shokrino_tv' to 'cat_tv'
$old_taxonomy = 'cat_shokrino_podcast';
$new_taxonomy = 'cat_podcast';

// Update wp_term_taxonomy table
$wpdb->update(
    $wpdb->term_taxonomy,
    array('taxonomy' => $new_taxonomy),
    array('taxonomy' => $old_taxonomy)
);

// Update wp_options table if necessary
$wpdb->update(
    $wpdb->options,
    array('option_value' => str_replace($old_taxonomy, $new_taxonomy, get_option('category_base'))),
    array('option_name' => 'category_base')
);
}
add_action( 'init', 'create_taxonomies_for_podcast');

function get_all_podcast() {

    $podcasts = array(
        'no-podcast' => esc_html__( 'انتخاب یک پادکست', 'shokrino' ),
    );

    $podcasts_args = array(
        'post_type'     => 'podcast',
        'post_status'   => 'publish',
        'posts_per_page'=> -1,
    );

    $podcasts_query = new WP_Query( $podcasts_args );

    foreach( $podcasts_query->posts as $podcast ){
        $podcasts[$podcast->ID] = $podcast->post_title;
    }

    return $podcasts;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function panelet_notifications_post_type() {
    $labels = array(
        'name'               => 'اعلان ها' ,
        'singular_name'      => 'اعلان',
        'menu_name'          => 'اعلان ها',
        'name_admin_bar'     => 'اعلان ها',
        'add_new'            => 'افزودن اعلان',
        'add_new_item'       => 'افزودن اعلان',
        'new_item'           => 'اعلان جدید',
        'edit_item'          => 'ویرایش اعلان',
        'view_item'          => 'مشاهده اعلان',
        'all_items'          => 'اعلان ها',
        'search_items'       => 'جستجو در بین اعلان ها',
        'parent_item_colon'  => 'اعلان مادر',
        'not_found'          => 'اعلان یافت نشد',
        'not_found_in_trash' => 'اعلان در زباله دان یافت نشد'
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description', 'shokrino' ),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          =>         'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxOTE3LjQ0IDE5MTcuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDE5MTcuNDQgMTkxNy40NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0iI2ZmZiI+DQogICAgICAgIDxnPg0KICAgICAgICAgICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTkyOS4yMSwxNzUxYy01Mi4yLDAtMTAzLjEyLTEwLjEzLTE1MS4zNS0zMC4xYy00OC4yMy0xOS45OC05MS40LTQ4LjgyLTEyOC4zMS04NS43M2wtMTE2LjU0LTExNi41NCAgIGMtOTIuODYtOTIuODYtOTIuODYtMjQzLjQxLDAtMzM2LjI3bDAsMGwyOTkuMTEsMjk5LjExYzMuOTcsMy45Nyw4LjUsNy4zMiwxMy40NSw5Ljk3YzY5LjkxLDM3LjQ5LDE0NS45NCwyNC44MSwxOTUuMTYtMjQuNDEgICBsMy44OC0zLjg4YzYxLjUtNjEuNSw2MS41LTE2MS41NiwwLTIyMy4wNWwtOTAuMTgtOTAuMThsMTY4LjEzLTE2OC4xM2w5MC4xOCw5MC4xOGMzNy43MiwzNy43Miw2Ni45NSw4MS43Niw4Ni44NywxMzAuODkgICBjMTkuMjIsNDcuNCwyOC45Nyw5Ny40NSwyOC45NywxNDguNzdjMCw1MS4zMS05Ljc1LDEwMS4zNi0yOC45NywxNDguNzdjLTE5LjkzLDQ5LjE0LTQ5LjE1LDkzLjE4LTg2Ljg3LDEzMC44OWwtMTEuNDQsMTEuNDQgICBjLTExLjcxLDExLjcxLTI0LjMsMjIuNTItMzcuODEsMzIuMUMxMDkyLjQ4LDE3MjUuMjMsMTAxMi40NywxNzUxLDkyOS4yMSwxNzUxeiIvPg0KICAgICAgICA8L2c+DQogICAgPGc+DQogICAgICAgICAgICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzM1Ljg2LDkzNi4yN2wtOTAuMTgtOTAuMThjLTM3LjcyLTM3LjcyLTY2Ljk0LTgxLjc2LTg2Ljg3LTEzMC44OWMtMTkuMjItNDcuNC0yOC45Ny05Ny40NS0yOC45Ny0xNDguNzcgICBzOS43NS0xMDEuMzYsMjguOTctMTQ4Ljc2YzE5LjkzLTQ5LjE0LDQ5LjE1LTkzLjE4LDg2Ljg3LTEzMC44OWwzLjg4LTMuODhjMzYuOTEtMzYuOTEsODAuMDgtNjUuNzYsMTI4LjMxLTg1LjczICAgYzQ4LjIzLTE5Ljk4LDk5LjE1LTMwLjExLDE1MS4zNS0zMC4xMWM1Mi4yLDAsMTAzLjEyLDEwLjEzLDE1MS4zNSwzMC4xMWM0OC4yMywxOS45OCw5MS40LDQ4LjgyLDEyOC4zMSw4NS43M2wxMDkuODEsMTA5LjgxICAgYzkyLjg2LDkyLjg2LDkyLjg2LDI0My40MSwwLDMzNi4yN2wwLDBsLTI3Ny45NC0yNzcuOTRjLTI5Ljc5LTI5Ljc5LTY5LjQtNDYuMi0xMTEuNTItNDYuMTljLTQyLjEzLDAtODEuNzQsMTYuNDEtMTExLjUyLDQ2LjIgICBsLTMuODgsMy44OGMtNjEuNSw2MS40OS02MS41LDE2MS41NSwwLDIyMy4wNWw5MC4xOCw5MC4xOEw3MzUuODYsOTM2LjI3eiIvPg0KICAgICAgICA8L2c+DQogICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0MDcuNjYsODc2Ljc0TDEwMTEuNyw0ODAuNzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzICBsMTEzLjM0LDExMy4zNEw3MzEuNTUsOTg3LjYyTDYxOS4zMiw4NzUuMzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzbDM5NS45NSwzOTUuOTUgIGM0Ni40Myw0Ni40MywxMjEuNyw0Ni40MywxNjguMTMsMGwwLDBjNDYuNDMtNDYuNDMsNDYuNDMtMTIxLjcsMC0xNjguMTNsLTExNS41OC0xMTUuNThsMjI1LjM2LTIyNS4zNmwxMTQuNDgsMTE0LjQ4ICBjNDYuNDMsNDYuNDMsMTIxLjcsNDYuNDMsMTY4LjEzLDBsMCwwQzE0NTQuMDksOTk4LjQ0LDE0NTQuMDksOTIzLjE3LDE0MDcuNjYsODc2Ljc0eiIvPg0KICAgIDwvc3ZnPg==',
        'supports'           => array( 'title','editor')
    );
    register_post_type( 'notification', $args );
}
add_action('init' , 'panelet_notifications_post_type');
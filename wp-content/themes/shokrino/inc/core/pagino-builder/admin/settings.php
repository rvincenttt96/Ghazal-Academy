<?php defined('ABSPATH') || exit;

//admin styles added
function features_page_shkrn_styles() {
    wp_enqueue_style( 'features_page_shkrn_styles', SHKRN_DIR_URL.'/inc/core/pagino-builder/css/style.css');
}
add_action( 'admin_enqueue_scripts', 'features_page_shkrn_styles' );

//main features menu added
function features_page_shkrn() {
    require_once SHKRN_CORE . '/pagino-builder/template/features-page.php';
}
function features_page_register_admin_menus_shkrn() {
    add_menu_page(
        'قالب شکرینو',
        'قالب شکرینو',
        'manage_options',
        'features_shokrino',
        'features_page_shkrn',
        'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxOTE3LjQ0IDE5MTcuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDE5MTcuNDQgMTkxNy40NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0iI2ZmZiI+DQogICAgICAgIDxnPg0KICAgICAgICAgICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTkyOS4yMSwxNzUxYy01Mi4yLDAtMTAzLjEyLTEwLjEzLTE1MS4zNS0zMC4xYy00OC4yMy0xOS45OC05MS40LTQ4LjgyLTEyOC4zMS04NS43M2wtMTE2LjU0LTExNi41NCAgIGMtOTIuODYtOTIuODYtOTIuODYtMjQzLjQxLDAtMzM2LjI3bDAsMGwyOTkuMTEsMjk5LjExYzMuOTcsMy45Nyw4LjUsNy4zMiwxMy40NSw5Ljk3YzY5LjkxLDM3LjQ5LDE0NS45NCwyNC44MSwxOTUuMTYtMjQuNDEgICBsMy44OC0zLjg4YzYxLjUtNjEuNSw2MS41LTE2MS41NiwwLTIyMy4wNWwtOTAuMTgtOTAuMThsMTY4LjEzLTE2OC4xM2w5MC4xOCw5MC4xOGMzNy43MiwzNy43Miw2Ni45NSw4MS43Niw4Ni44NywxMzAuODkgICBjMTkuMjIsNDcuNCwyOC45Nyw5Ny40NSwyOC45NywxNDguNzdjMCw1MS4zMS05Ljc1LDEwMS4zNi0yOC45NywxNDguNzdjLTE5LjkzLDQ5LjE0LTQ5LjE1LDkzLjE4LTg2Ljg3LDEzMC44OWwtMTEuNDQsMTEuNDQgICBjLTExLjcxLDExLjcxLTI0LjMsMjIuNTItMzcuODEsMzIuMUMxMDkyLjQ4LDE3MjUuMjMsMTAxMi40NywxNzUxLDkyOS4yMSwxNzUxeiIvPg0KICAgICAgICA8L2c+DQogICAgPGc+DQogICAgICAgICAgICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzM1Ljg2LDkzNi4yN2wtOTAuMTgtOTAuMThjLTM3LjcyLTM3LjcyLTY2Ljk0LTgxLjc2LTg2Ljg3LTEzMC44OWMtMTkuMjItNDcuNC0yOC45Ny05Ny40NS0yOC45Ny0xNDguNzcgICBzOS43NS0xMDEuMzYsMjguOTctMTQ4Ljc2YzE5LjkzLTQ5LjE0LDQ5LjE1LTkzLjE4LDg2Ljg3LTEzMC44OWwzLjg4LTMuODhjMzYuOTEtMzYuOTEsODAuMDgtNjUuNzYsMTI4LjMxLTg1LjczICAgYzQ4LjIzLTE5Ljk4LDk5LjE1LTMwLjExLDE1MS4zNS0zMC4xMWM1Mi4yLDAsMTAzLjEyLDEwLjEzLDE1MS4zNSwzMC4xMWM0OC4yMywxOS45OCw5MS40LDQ4LjgyLDEyOC4zMSw4NS43M2wxMDkuODEsMTA5LjgxICAgYzkyLjg2LDkyLjg2LDkyLjg2LDI0My40MSwwLDMzNi4yN2wwLDBsLTI3Ny45NC0yNzcuOTRjLTI5Ljc5LTI5Ljc5LTY5LjQtNDYuMi0xMTEuNTItNDYuMTljLTQyLjEzLDAtODEuNzQsMTYuNDEtMTExLjUyLDQ2LjIgICBsLTMuODgsMy44OGMtNjEuNSw2MS40OS02MS41LDE2MS41NSwwLDIyMy4wNWw5MC4xOCw5MC4xOEw3MzUuODYsOTM2LjI3eiIvPg0KICAgICAgICA8L2c+DQogICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0MDcuNjYsODc2Ljc0TDEwMTEuNyw0ODAuNzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzICBsMTEzLjM0LDExMy4zNEw3MzEuNTUsOTg3LjYyTDYxOS4zMiw4NzUuMzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzbDM5NS45NSwzOTUuOTUgIGM0Ni40Myw0Ni40MywxMjEuNyw0Ni40MywxNjguMTMsMGwwLDBjNDYuNDMtNDYuNDMsNDYuNDMtMTIxLjcsMC0xNjguMTNsLTExNS41OC0xMTUuNThsMjI1LjM2LTIyNS4zNmwxMTQuNDgsMTE0LjQ4ICBjNDYuNDMsNDYuNDMsMTIxLjcsNDYuNDMsMTY4LjEzLDBsMCwwQzE0NTQuMDksOTk4LjQ0LDE0NTQuMDksOTIzLjE3LDE0MDcuNjYsODc2Ljc0eiIvPg0KICAgIDwvc3ZnPg==',
        5
    );
    add_submenu_page(
        'features_shokrino',
        'قالب شکرینو',
        __('Dashboard Theme','shokrino'),
        'manage_options',
        'features_shokrino'
    );
}
add_action('admin_menu' , 'features_page_register_admin_menus_shkrn');
function kalamatino_words_shkrn() {
    require_once SHKRN_CORE . '/pagino-builder/template/kalamatino.php';
}
function kalamatino_words_register_admin_menus_shkrn() {
    add_submenu_page(
        'features_shokrino',
        __('text changer','shokrino'),
        __('text changer','shokrino'),
        'manage_options',
        'kalamatino_shokrino',
        'kalamatino_words_shkrn',
    );
}
add_action('admin_menu' , 'kalamatino_words_register_admin_menus_shkrn');
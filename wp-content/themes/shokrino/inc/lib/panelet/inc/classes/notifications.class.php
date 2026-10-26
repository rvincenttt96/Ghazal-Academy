<?php
defined('ABSPATH') || exit;
class Panelet_NOTIF {
    public function __construct() {
        add_action('init' , [$this, 'post_type']);
        add_filter('query_vars', [$this, 'query_vars'], 0 );
        add_action('add_meta_boxes', [$this, 'add_metabox']);
        add_action('save_post', [$this, 'save_metabox']);
        add_action('post_submitbox_misc_actions', [$this, 'metabox_nonce']);
        create_my_account_endpoint_pnlt_func('اعلان ها', 'notifications', [$this, 'endpoint_content']);
    }
    public function post_type() {
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
            'description'        => "توضیحات",
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
    public function endpoint_content() {
    ?>
    <div class="user-panel-notifications-wrap">
        <div class="public-notifications">
            <div class="head-notifications">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100" height="100" x="0" y="0" viewBox="0 0 512.00001 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><defs><linearGradient xmlns="http://www.w3.org/2000/svg" id="b" gradientUnits="userSpaceOnUse" x1="198.4694562602" x2="198.4694562602" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="151.2006061938" y2="387.8526294488"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="d" gradientUnits="userSpaceOnUse" x1="85.966342821" x2="180.5195316846" xlink:href="#c" xmlns:xlink="http://www.w3.org/1999/xlink" y1="362.75919749" y2="391.3220834886"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="f" gradientUnits="userSpaceOnUse" x1="142.3959953034" x2="107.349881421" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="395.124638848" y2="333.607310454"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="h" gradientUnits="userSpaceOnUse" x1="111.2733089892" x2="148.734558027" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="359.5157262924" y2="370.8321358608"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="i" gradientUnits="userSpaceOnUse" x1="136.6075818358" x2="111.6277938474" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="434.974820677" y2="378.6771839376"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="j" gradientUnits="userSpaceOnUse" x1="111.2733089892" x2="148.734558027" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="405.6199406126" y2="416.936350181"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="k" gradientUnits="userSpaceOnUse" x1="117.2839907876" x2="93.4399397578" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="368.5816439134" y2="242.0863675992"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="l" gradientUnits="userSpaceOnUse" x1="33.1363530618" x2="57.558863948" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="196.4633330936" y2="262.4042429962"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="m" gradientUnits="userSpaceOnUse" x1="109.1977002542" x2="109.1977002542" xlink:href="#c" xmlns:xlink="http://www.w3.org/1999/xlink" y1="178.3600092364" y2="317.8091922932"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="n" gradientUnits="userSpaceOnUse" x1="333.143484324" x2="677.9598709186" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="154.3509134298" y2="449.2278024876"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="o" gradientUnits="userSpaceOnUse" x1="445.1401191142" x2="255.1825159232" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="217.951982327" y2="217.951982327"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="p" gradientUnits="userSpaceOnUse" x1="414.9934457684" x2="164.2522807732" y1="305.2854661276" y2="125.112287533"><stop offset="0" stop-color="#a1b5d8" stop-opacity="0"></stop><stop offset=".6699" stop-color="#8190a4" stop-opacity=".670588"></stop><stop offset="1" stop-color="#748290"></stop></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="q" gradientUnits="userSpaceOnUse" x1="180.9099569504" x2="144.1077827056" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="209.51050419" y2="209.51050419"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="r" gradientUnits="userSpaceOnUse" x1="217.5385530814" x2="180.7363788366" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="209.51050419" y2="209.51050419"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="s" gradientUnits="userSpaceOnUse" x1="147.4236366794" x2="121.4237635468" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="236.4360937988" y2="200.8228643786"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="t" gradientUnits="userSpaceOnUse" x1="125.180038102" x2="125.180038102" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="204.1757630742" y2="239.0365529554"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="u" gradientUnits="userSpaceOnUse" x1="356.544906144" x2="380.6436521852" xlink:href="#c" xmlns:xlink="http://www.w3.org/1999/xlink" y1="193.449258073" y2="258.5158523058"></linearGradient></defs><g><linearGradient xmlns="http://www.w3.org/2000/svg" id="a"><stop offset="0" stop-color="#fffcf8"></stop><stop offset=".1875" stop-color="#faf8f6"></stop><stop offset=".4041" stop-color="#eceef2"></stop><stop offset=".6353" stop-color="#d5dcea"></stop><stop offset=".875" stop-color="#b5c4df"></stop><stop offset="1" stop-color="#a1b5d8"></stop></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="b" gradientUnits="userSpaceOnUse" x1="198.4694562602" x2="198.4694562602" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="151.2006061938" y2="387.8526294488"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="c"><stop offset="0" stop-color="#ff4974"></stop><stop offset=".2277" stop-color="#fd4571"></stop><stop offset=".4662" stop-color="#f93867"></stop><stop offset=".7098" stop-color="#f12358"></stop><stop offset=".9554" stop-color="#e60642"></stop><stop offset="1" stop-color="#e4003d"></stop></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="d" gradientUnits="userSpaceOnUse" x1="85.966342821" x2="180.5195316846" xlink:href="#c" xmlns:xlink="http://www.w3.org/1999/xlink" y1="362.75919749" y2="391.3220834886"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="e"><stop offset="0" stop-color="#e4003d" stop-opacity="0"></stop><stop offset=".2234" stop-color="#cd1845" stop-opacity=".223529"></stop><stop offset=".5034" stop-color="#b82e4d" stop-opacity=".501961"></stop><stop offset=".7682" stop-color="#ab3c51" stop-opacity=".768627"></stop><stop offset="1" stop-color="#a64153"></stop></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="f" gradientUnits="userSpaceOnUse" x1="142.3959953034" x2="107.349881421" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="395.124638848" y2="333.607310454"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="g"><stop offset="0" stop-color="#ffd00d"></stop><stop offset=".2378" stop-color="#ffcb0e"></stop><stop offset=".5353" stop-color="#fb1"></stop><stop offset=".8632" stop-color="#ffa215"></stop><stop offset="1" stop-color="#ff9517"></stop></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="h" gradientUnits="userSpaceOnUse" x1="111.2733089892" x2="148.734558027" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="359.5157262924" y2="370.8321358608"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="i" gradientUnits="userSpaceOnUse" x1="136.6075818358" x2="111.6277938474" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="434.974820677" y2="378.6771839376"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="j" gradientUnits="userSpaceOnUse" x1="111.2733089892" x2="148.734558027" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="405.6199406126" y2="416.936350181"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="k" gradientUnits="userSpaceOnUse" x1="117.2839907876" x2="93.4399397578" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="368.5816439134" y2="242.0863675992"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="l" gradientUnits="userSpaceOnUse" x1="33.1363530618" x2="57.558863948" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="196.4633330936" y2="262.4042429962"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="m" gradientUnits="userSpaceOnUse" x1="109.1977002542" x2="109.1977002542" xlink:href="#c" xmlns:xlink="http://www.w3.org/1999/xlink" y1="178.3600092364" y2="317.8091922932"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="n" gradientUnits="userSpaceOnUse" x1="333.143484324" x2="677.9598709186" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="154.3509134298" y2="449.2278024876"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="o" gradientUnits="userSpaceOnUse" x1="445.1401191142" x2="255.1825159232" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="217.951982327" y2="217.951982327"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="p" gradientUnits="userSpaceOnUse" x1="414.9934457684" x2="164.2522807732" y1="305.2854661276" y2="125.112287533"><stop offset="0" stop-color="#a1b5d8" stop-opacity="0"></stop><stop offset=".6699" stop-color="#8190a4" stop-opacity=".670588"></stop><stop offset="1" stop-color="#748290"></stop></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="q" gradientUnits="userSpaceOnUse" x1="180.9099569504" x2="144.1077827056" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="209.51050419" y2="209.51050419"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="r" gradientUnits="userSpaceOnUse" x1="217.5385530814" x2="180.7363788366" xlink:href="#a" xmlns:xlink="http://www.w3.org/1999/xlink" y1="209.51050419" y2="209.51050419"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="s" gradientUnits="userSpaceOnUse" x1="147.4236366794" x2="121.4237635468" xlink:href="#e" xmlns:xlink="http://www.w3.org/1999/xlink" y1="236.4360937988" y2="200.8228643786"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="t" gradientUnits="userSpaceOnUse" x1="125.180038102" x2="125.180038102" xlink:href="#g" xmlns:xlink="http://www.w3.org/1999/xlink" y1="204.1757630742" y2="239.0365529554"></linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="u" gradientUnits="userSpaceOnUse" x1="356.544906144" x2="380.6436521852" xlink:href="#c" xmlns:xlink="http://www.w3.org/1999/xlink" y1="193.449258073" y2="258.5158523058"></linearGradient><path xmlns="http://www.w3.org/2000/svg" d="m123.101562 108.308594c-48.269531 0-87.402343 39.128906-87.402343 87.398437v43.230469c0 48.269531 39.132812 87.402344 87.402343 87.402344 76.546876 0 151.25 23.488281 214.023438 67.296875l24.113281 16.828125v-386.285156l-24.113281 16.832031c-62.773438 43.804687-137.476562 67.296875-214.023438 67.296875zm0 0" fill="url(&quot;#b&quot;)" data-original="url(#b)" class=""></path><path xmlns="http://www.w3.org/2000/svg" d="m137.625 297.253906h-78.019531l12.257812 28.886719c8.132813 19.167969 12.324219 39.777344 12.324219 60.597656v27.617188c0 21.542969 17.464844 39.007812 39.007812 39.007812 21.546876 0 39.011719-17.464843 39.011719-39.007812v-27.617188c0-20.820312-4.191406-41.429687-12.324219-60.597656zm0 0" fill="url(&quot;#d&quot;)" data-original="url(#d)"></path><path xmlns="http://www.w3.org/2000/svg" d="m132.535156 347.679688h-25.304687c-7.929688 0-14.355469 6.429687-14.355469 14.359374v.15625c0 4.316407 1.910156 8.175782 4.921875 10.808594l59.785156 59.78125c2.949219-5.488281 4.625-11.761718 4.625-18.429687v-27.617188c0-5.394531-.289062-10.773437-.847656-16.117187l-18.015625-18.015625c-2.632812-3.015625-6.492188-4.925781-10.808594-4.925781zm0 0" fill="url(&quot;#f&quot;)" data-original="url(#f)"></path><path xmlns="http://www.w3.org/2000/svg" d="m132.535156 376.554688h-25.304687c-7.929688 0-14.355469-6.429688-14.355469-14.359376v-.15625c0-7.929687 6.425781-14.359374 14.355469-14.359374h25.304687c7.929688 0 14.359375 6.429687 14.359375 14.359374v.15625c0 7.929688-6.429687 14.359376-14.359375 14.359376zm0 0" fill="url(&quot;#h&quot;)" data-original="url(#h)"></path><path xmlns="http://www.w3.org/2000/svg" d="m143.34375 398.707031c-2.632812-3.011719-6.492188-4.921875-10.808594-4.921875h-25.304687c-7.929688 0-14.359375 6.425782-14.359375 14.355469v.160156c0 4.3125 1.914062 8.171875 4.925781 10.804688l33.429687 33.429687c16.710938-3.5 29.484376-17.691406 30.847657-35.09375zm0 0" fill="url(&quot;#i&quot;)" data-original="url(#i)"></path><path xmlns="http://www.w3.org/2000/svg" d="m132.535156 422.65625h-25.304687c-7.929688 0-14.355469-6.425781-14.355469-14.355469v-.160156c0-7.929687 6.425781-14.355469 14.355469-14.355469h25.304687c7.929688 0 14.359375 6.425782 14.359375 14.355469v.160156c0 7.929688-6.429687 14.355469-14.359375 14.355469zm0 0" fill="url(&quot;#j&quot;)" data-original="url(#j)"></path><path xmlns="http://www.w3.org/2000/svg" d="m162.207031 402.359375v-15.621094c0-20.820312-4.191406-41.429687-12.324219-60.59375l-12.257812-28.890625h-78.019531l1.847656 4.355469zm0 0" fill="url(&quot;#k&quot;)" data-original="url(#k)"></path><path xmlns="http://www.w3.org/2000/svg" d="m81.726562 217.324219c0 22.566406-18.296874 40.863281-40.863281 40.863281-22.570312 0-40.863281-18.296875-40.863281-40.863281 0-22.566407 18.292969-40.863281 40.863281-40.863281 22.566407 0 40.863281 18.296874 40.863281 40.863281zm0 0" fill="url(&quot;#l&quot;)" data-original="url(#l)"></path><path xmlns="http://www.w3.org/2000/svg" d="m141.765625 244.867188v-55.089844c0-34.785156 15.949219-65.839844 40.929687-86.265625-19.617187 3.167969-39.542968 4.792969-59.59375 4.792969-48.269531 0-87.402343 39.132812-87.402343 87.402343v43.230469c0 48.269531 39.132812 87.402344 87.402343 87.402344 20.050782 0 39.976563 1.625 59.59375 4.792968-24.980468-20.421874-40.929687-51.480468-40.929687-86.265624zm0 0" fill="url(&quot;#m&quot;)" data-original="url(#m)"></path><path xmlns="http://www.w3.org/2000/svg" d="m512 217.953125c0-120.371094-46.777344-217.953125-104.484375-217.953125-57.703125 0-104.484375 97.582031-104.484375 217.953125s46.78125 217.953125 104.484375 217.953125c57.707031 0 104.484375-97.582031 104.484375-217.953125zm0 0" fill="url(&quot;#n&quot;)" data-original="url(#n)" class=""></path><path xmlns="http://www.w3.org/2000/svg" d="m482.703125 217.953125c0-96.441406-30.449219-174.621094-68.007813-174.621094-37.5625 0-68.011718 78.179688-68.011718 174.621094s30.449218 174.621094 68.011718 174.621094c37.558594 0 68.007813-78.179688 68.007813-174.621094zm0 0" fill="url(&quot;#o&quot;)" data-original="url(#o)"></path><path xmlns="http://www.w3.org/2000/svg" d="m477.71875 283.585938c-.109375.785156-.238281 1.5625-.371094 2.335937-.453125 2.800781-.941406 5.550781-1.464844 8.269531-.203124 1.09375-.40625 2.179688-.632812 3.25-1.511719 7.640625-3.25 14.945313-5.183594 21.878906-.285156 1.050782-.582031 2.085938-.878906 3.109376-.023438.109374-.058594.203124-.085938.300781-.296874 1.011719-.605468 2.023437-.914062 3.027343-.335938 1.09375-.667969 2.191407-1.027344 3.261719-.273437.871094-.546875 1.71875-.832031 2.5625-.121094.371094-.238281.726563-.371094 1.097657-.308593.90625-.621093 1.800781-.929687 2.679687-.382813 1.097656-.773438 2.195313-1.191406 3.277344-.34375.941406-.703126 1.871093-1.058594 2.789062-.191406.488281-.382813.988281-.585938 1.476563-.191406.488281-.382812.964844-.585937 1.453125-.390625.964843-.785157 1.90625-1.191407 2.851562-.238281.558594-.476562 1.105469-.726562 1.667969-.261719.605469-.523438 1.203125-.796875 1.785156-.308594.691406-.621094 1.371094-.941406 2.039063-1.597657 3.394531-3.265625 6.613281-4.992188 9.628906-10.902343 19.085937-24.070312 30.242187-38.261719 30.242187-32.210937 0-59.199218-57.519531-66.21875-134.738281-.09375-1.046875-.191406-2.097656-.273437-3.15625-.167969-1.917969-.320313-3.859375-.453125-5.816406-.023438-.296875-.035156-.59375-.058594-.894531-.121094-1.878906-.238281-3.777344-.335937-5.683594-.070313-1.226562-.128907-2.453125-.1875-3.679688-.015625-.261718-.027344-.511718-.039063-.777343l-.175781-4.429688c0-.132812 0-.265625-.015625-.382812-.046875-1.464844-.082031-2.917969-.117188-4.386719-.011718-.261719-.011718-.511719-.023437-.761719v-.109375c-.023437-1.035156-.050781-2.070312-.058594-3.109375-.015625-.726562-.027343-1.453125-.027343-2.191406-.011719-1.503906-.023438-3.003906-.023438-4.503906 0-1.503907.011719-3.003907.023438-4.503907 0-.714843.011718-1.429687.027343-2.148437.023438-1.191406.046875-2.382813.070313-3.574219 0-.25.011718-.5.011718-.75.023438-.847656.046876-1.679687.070313-2.515625.023437-1.070312.058594-2.132812.109375-3.191406.035156-1.191406.082031-2.382813.140625-3.578125.011719-.152344.011719-.320312.023437-.476562.011719-.414063.039063-.832032.058594-1.238282.238282-5.015625.574219-9.972656.992188-14.847656.117187-1.453125.25-2.894531.378906-4.335938 1.679688-.214843 3.386719-.332031 5.125-.332031 11.976562 0 22.734375 5.144531 30.207031 13.34375zm0 0" fill="url(&quot;#p&quot;)" data-original="url(#p)"></path><path xmlns="http://www.w3.org/2000/svg" d="m175.820312 237.097656h-1.621093c-6.828125 0-12.363281-5.539062-12.363281-12.367187v-30.441407c0-6.828124 5.535156-12.363281 12.363281-12.363281h1.621093c6.832032 0 12.367188 5.535157 12.367188 12.363281v30.441407c0 6.828125-5.535156 12.367187-12.367188 12.367187zm0 0" fill="url(&quot;#q&quot;)" data-original="url(#q)"></path><path xmlns="http://www.w3.org/2000/svg" d="m212.449219 237.097656h-1.621094c-6.828125 0-12.363281-5.539062-12.363281-12.367187v-30.441407c0-6.828124 5.535156-12.363281 12.363281-12.363281h1.621094c6.832031 0 12.367187 5.535157 12.367187 12.363281v30.441407c0 6.828125-5.535156 12.367187-12.367187 12.367187zm0 0" fill="url(&quot;#r&quot;)" data-original="url(#r)"></path><path xmlns="http://www.w3.org/2000/svg" d="m141.765625 244.867188v-48.769532h-22.679687c-5.792969 0-10.492188 4.699219-10.492188 10.492188v5.839844c0 3.066406 1.324219 5.8125 3.417969 7.730468l29.882812 29.882813c-.078125-1.714844-.128906-3.4375-.128906-5.175781zm0 0" fill="url(&quot;#s&quot;)" data-original="url(#s)"></path><path xmlns="http://www.w3.org/2000/svg" d="m141.765625 196.097656h-22.679687c-5.792969 0-10.492188 4.699219-10.492188 10.492188v5.839844c0 5.796874 4.699219 10.492187 10.492188 10.492187h22.679687zm0 0" fill="url(&quot;#t&quot;)" data-original="url(#t)"></path><path xmlns="http://www.w3.org/2000/svg" d="m353.722656 176.460938c-1.738281 0-3.449218.121093-5.132812.332031-1.242188 13.199219-1.90625 26.980469-1.90625 41.160156 0 13.722656.621094 27.074219 1.785156 39.886719 1.722656.21875 3.472656.34375 5.253906.34375 22.566406 0 40.863282-18.292969 40.863282-40.859375 0-22.570313-18.296876-40.863281-40.863282-40.863281zm0 0" fill="url(&quot;#u&quot;)" data-original="url(#u)"></path></g></svg>
                <div class="headings-notifications">
                    <h2 class="heading-notifications">
                        <?php echo "پیام های عمومی";?>
                    </h2>
                    <span class="description-notifications"><?php echo "این پیام ها برای همه کاربران سایت است";?></span>
                </div>
            </div>
            <div class="list-notifications">
                <?php
                $user_messages = get_posts( array(
                    'numberposts' => -1,
                    'meta_key'    => 'notif_type',
                    'meta_value'  => 'public',
                    'post_type'   => 'notification',
                    'post_status' => 'publish',
                ) );
                if ( $user_messages ) {
                    foreach ($user_messages as $value) {
                        ?>
                        <div class="notif-item">
                            <div class="notif-header">
                                <h3 class="notif"><?php echo $value->post_title; ?></h3>
                                <span>
                                    <?php
                                    $date_notif = $value->post_date;
                                    echo date( 'H:i', strtotime( $date_notif ));
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo date( 'Y/m/d', strtotime( $date_notif ));
                                    ?>
                                </span>
                            </div>
                            <div class="notif-content">
                                <p><?php echo $value->post_content; ?></p>
                            </div>
                        </div>
                        <?php
                    }
                } else { ?>
                    <span>پیامی وجود ندارد!</span>
                <?php } ?>
            </div>
        </div>
        <div class="private-notifications">
            <div class="head-notifications">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100" height="100" x="0" y="0" viewBox="0 0 512.002 512.002" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><defs><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="243.097" y1="106.6264" x2="330.117" y2="-38.4036" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200"/>
                            <stop offset="0.268" style="stop-color:#FFBB00"/>
                            <stop offset="0.659" style="stop-color:#FFA801"/>
                            <stop offset="1" style="stop-color:#FF9102"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_2_" gradientUnits="userSpaceOnUse" x1="329.4405" y1="27.8793" x2="213.7505" y2="143.5693" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200;stop-opacity:0"/>
                            <stop offset="0.203" style="stop-color:#FFBB00;stop-opacity:0.203"/>
                            <stop offset="0.499" style="stop-color:#FFA700;stop-opacity:0.499"/>
                            <stop offset="0.852" style="stop-color:#FF8800;stop-opacity:0.852"/>
                            <stop offset="1" style="stop-color:#FF7800"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_3_" gradientUnits="userSpaceOnUse" x1="200.407" y1="224.9864" x2="417.617" y2="-137.0336" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200"/>
                            <stop offset="0.268" style="stop-color:#FFBB00"/>
                            <stop offset="0.659" style="stop-color:#FFA801"/>
                            <stop offset="1" style="stop-color:#FF9102"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_4_" gradientUnits="userSpaceOnUse" x1="199.7254" y1="310.434" x2="397.8954" y2="123.774" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200"/>
                            <stop offset="0.268" style="stop-color:#FFBB00"/>
                            <stop offset="0.659" style="stop-color:#FFA801"/>
                            <stop offset="1" style="stop-color:#FF9102"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_5_" gradientUnits="userSpaceOnUse" x1="330.6714" y1="128.2206" x2="-1.8287" y2="85.9506" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200;stop-opacity:0"/>
                            <stop offset="0.203" style="stop-color:#FFBB00;stop-opacity:0.203"/>
                            <stop offset="0.499" style="stop-color:#FFA700;stop-opacity:0.499"/>
                            <stop offset="0.852" style="stop-color:#FF8800;stop-opacity:0.852"/>
                            <stop offset="1" style="stop-color:#FF7800"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_6_" gradientUnits="userSpaceOnUse" x1="304.8369" y1="160.4757" x2="239.7869" y2="225.5257" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200;stop-opacity:0"/>
                            <stop offset="0.13" style="stop-color:#FDBB00;stop-opacity:0.13"/>
                            <stop offset="0.32" style="stop-color:#F7A701;stop-opacity:0.32"/>
                            <stop offset="0.547" style="stop-color:#EE8702;stop-opacity:0.547"/>
                            <stop offset="0.799" style="stop-color:#E25B04;stop-opacity:0.799"/>
                            <stop offset="1" style="stop-color:#D63305"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_7_" gradientUnits="userSpaceOnUse" x1="227.8685" y1="249.7073" x2="258.8985" y2="218.6873" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#D63305"/>
                            <stop offset="0.366" style="stop-color:#CF3004"/>
                            <stop offset="0.899" style="stop-color:#BC2602"/>
                            <stop offset="1" style="stop-color:#B72401"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_8_" gradientUnits="userSpaceOnUse" x1="97.6784" y1="435.9772" x2="133.6684" y2="399.9872" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#1ACEB8"/>
                            <stop offset="1" style="stop-color:#0BBFBA"/>
                        </linearGradient><linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_9_" gradientUnits="userSpaceOnUse" x1="35.847" y1="498.1548" x2="105.08" y2="428.9189" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#1ACEB8"/>
                            <stop offset="1" style="stop-color:#0BBFBA"/>
                        </linearGradient></defs><g>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="243.097" y1="106.6264" x2="330.117" y2="-38.4036" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200"/>
                            <stop offset="0.268" style="stop-color:#FFBB00"/>
                            <stop offset="0.659" style="stop-color:#FFA801"/>
                            <stop offset="1" style="stop-color:#FF9102"/>
                        </linearGradient>
                        <circle xmlns="http://www.w3.org/2000/svg" style="" cx="271.126" cy="458.059" r="53.943" fill="url(&quot;#SVGID_1_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_2_" gradientUnits="userSpaceOnUse" x1="329.4405" y1="27.8793" x2="213.7505" y2="143.5693" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200;stop-opacity:0"/>
                            <stop offset="0.203" style="stop-color:#FFBB00;stop-opacity:0.203"/>
                            <stop offset="0.499" style="stop-color:#FFA700;stop-opacity:0.499"/>
                            <stop offset="0.852" style="stop-color:#FF8800;stop-opacity:0.852"/>
                            <stop offset="1" style="stop-color:#FF7800"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M271.128,404.113c-28.098,0-51.168,21.486-53.702,48.922l58.724,58.724  c27.437-2.534,48.922-25.604,48.922-53.702C325.071,428.264,300.92,404.113,271.128,404.113z" fill="url(&quot;#SVGID_2_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_3_" gradientUnits="userSpaceOnUse" x1="200.407" y1="224.9864" x2="417.617" y2="-137.0336" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200"/>
                            <stop offset="0.268" style="stop-color:#FFBB00"/>
                            <stop offset="0.659" style="stop-color:#FFA801"/>
                            <stop offset="1" style="stop-color:#FF9102"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M447.896,363.984H94.358c-24.145,0-43.719,19.573-43.719,43.719s19.573,43.719,43.719,43.719  h353.537c24.145,0,43.719-19.573,43.719-43.719S472.042,363.984,447.896,363.984z" fill="url(&quot;#SVGID_3_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_4_" gradientUnits="userSpaceOnUse" x1="199.7254" y1="310.434" x2="397.8954" y2="123.774" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200"/>
                            <stop offset="0.268" style="stop-color:#FFBB00"/>
                            <stop offset="0.659" style="stop-color:#FFA801"/>
                            <stop offset="1" style="stop-color:#FF9102"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M422.591,330.999v-87.638c0-70.877-48.681-130.359-114.427-146.884  c3.749-6.381,5.921-13.814,5.921-21.748c0-23.722-19.235-42.958-42.958-42.958c-23.723,0-42.958,19.235-42.958,42.958  c0,7.934,2.171,15.367,5.921,21.748c-65.745,16.525-114.413,76.007-114.413,146.884v87.638c0,18.973-15.38,34.353-34.353,34.353  H456.93C437.958,365.352,422.591,349.972,422.591,330.999z M271.128,91.897c-9.486,0-17.183-7.683-17.183-17.17  c0-9.5,7.697-17.183,17.183-17.183s17.183,7.683,17.183,17.183C288.31,84.213,280.613,91.897,271.128,91.897z" fill="url(&quot;#SVGID_4_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_5_" gradientUnits="userSpaceOnUse" x1="330.6714" y1="128.2206" x2="-1.8287" y2="85.9506" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200;stop-opacity:0"/>
                            <stop offset="0.203" style="stop-color:#FFBB00;stop-opacity:0.203"/>
                            <stop offset="0.499" style="stop-color:#FFA700;stop-opacity:0.499"/>
                            <stop offset="0.852" style="stop-color:#FF8800;stop-opacity:0.852"/>
                            <stop offset="1" style="stop-color:#FF7800"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M51.183,416.391c3.3,20.876,21.372,36.839,43.177,34.866h353.537  c21.804,1.973,39.875-13.99,43.177-34.866H51.183z" fill="url(&quot;#SVGID_5_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_6_" gradientUnits="userSpaceOnUse" x1="304.8369" y1="160.4757" x2="239.7869" y2="225.5257" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#FFC200;stop-opacity:0"/>
                            <stop offset="0.13" style="stop-color:#FDBB00;stop-opacity:0.13"/>
                            <stop offset="0.32" style="stop-color:#F7A701;stop-opacity:0.32"/>
                            <stop offset="0.547" style="stop-color:#EE8702;stop-opacity:0.547"/>
                            <stop offset="0.799" style="stop-color:#E25B04;stop-opacity:0.799"/>
                            <stop offset="1" style="stop-color:#D63305"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M491.615,407.703c0-24.145-19.573-43.719-43.719-43.719h-0.56  c-14.298-4.154-24.746-17.344-24.746-32.985v-87.638c0-33.382-10.805-64.233-29.1-89.261L96.166,451.423h121.438  c-0.267,2.175-0.42,4.386-0.42,6.634c0,29.792,24.151,53.943,53.943,53.943s53.943-24.151,53.943-53.943  c0-2.247-0.154-4.458-0.421-6.634h123.246C472.042,451.423,491.615,431.848,491.615,407.703z" fill="url(&quot;#SVGID_6_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_7_" gradientUnits="userSpaceOnUse" x1="227.8685" y1="249.7073" x2="258.8985" y2="218.6873" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#D63305"/>
                            <stop offset="0.366" style="stop-color:#CF3004"/>
                            <stop offset="0.899" style="stop-color:#BC2602"/>
                            <stop offset="1" style="stop-color:#B72401"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M76.523,495.423L76.523,495.423c-6.725-6.725-6.725-17.631,0-24.356L426.22,121.369  c6.725-6.725,17.631-6.725,24.356,0l0,0c6.725,6.725,6.725,17.631,0,24.356L100.879,495.423  C94.154,502.149,83.249,502.149,76.523,495.423z" fill="url(&quot;#SVGID_7_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_8_" gradientUnits="userSpaceOnUse" x1="97.6784" y1="435.9772" x2="133.6684" y2="399.9872" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#1ACEB8"/>
                            <stop offset="1" style="stop-color:#0BBFBA"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M166.558,54.518c-41.413,23.247-74.363,59.843-92.985,103.915  c-3.658,8.657,0.54,18.639,9.236,22.203l0.494,0.203c8.665,3.55,18.464-0.637,22.118-9.258  c15.646-36.907,43.277-67.55,77.979-87.013c7.433-4.17,10.633-13.189,7.459-21.099l-0.202-0.501  C186.836,53.44,175.508,49.494,166.558,54.518z" fill="url(&quot;#SVGID_8_&quot;)"/>
                        <linearGradient xmlns="http://www.w3.org/2000/svg" id="SVGID_9_" gradientUnits="userSpaceOnUse" x1="35.847" y1="498.1548" x2="105.08" y2="428.9189" gradientTransform="matrix(1.0667 0 0 -1.0667 3.2666 557.5331)">
                            <stop offset="0" style="stop-color:#1ACEB8"/>
                            <stop offset="1" style="stop-color:#0BBFBA"/>
                        </linearGradient>
                        <path xmlns="http://www.w3.org/2000/svg" style="" d="M145.622,2.013C90.256,31.131,46.171,78.879,21.732,136.866  c-3.71,8.803,0.525,18.947,9.364,22.571l0.006,0.002c8.731,3.58,18.716-0.537,22.38-9.233  C74.84,99.509,113.419,57.78,161.857,32.379c7.778-4.079,11.138-13.44,7.869-21.591l0,0C165.907,1.27,154.699-2.761,145.622,2.013z" fill="url(&quot;#SVGID_9_&quot;)"/>
                    </g></svg>
                <div class="headings-notifications">
                    <h2 class="heading-notifications"><?php echo "پیام های خصوصی"; ?></h2>
                    <span class="description-notifications"><?php echo "این پیام ها فقط برای شما ارسال شده اند"; ?></span>
                </div>
            </div>
            <div class="list-notifications">
                <?php
                $user = wp_get_current_user();

                $user_messages = get_posts( array(
                    'numberposts' => -1,
                    'meta_key'    => 'notif_type',
                    'meta_value'  => 'private',
                    'post_type'   => 'notification',
                    'post_status' => 'publish',
                ) );

                if ( $user_messages ){
                    foreach ($user_messages as $post) {
                        $post_id = $post->ID;
                        $selected_buyers_option = get_post_meta($post_id, 'selected_buyers_option', true);
                        $selected_product_id = get_post_meta($post_id, 'buyers_of_product', true);
                        $selected_user_id = get_post_meta($post_id, 'selected_users', true);
                        $show_status = true;
                        if ($selected_buyers_option == "select_users") {
                            $show_status = $selected_user_id == $user->ID ? true : false;
                        } elseif ($selected_buyers_option == "select_buyers_of_product") {
                            $show_status = wc_customer_bought_product($user->user_email, $user->ID, $selected_product_id) ? true : false;
                        }
                        if ($show_status) {
                            ?>
                            <div class="notif-item">
                                <div class="notif-header">
                                    <h3 class="private"><?php echo $post->post_title; ?></h3>
                                    <span>
                                        <?php
                                        $date_notif = $post->post_date;
                                        echo date('H:i', strtotime($date_notif));
                                        echo '&nbsp;&nbsp;&nbsp;';
                                        echo date('Y/m/d', strtotime($date_notif));
                                        ?>
                                    </span>
                                </div>
                                <div class="notif-content">
                                    <p><?php echo $post->post_content; ?></p>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <span>پیامی وجود ندارد!</span>
                        <?php }
                    }
                } else {
                    ?>
                    <span>پیامی وجود ندارد!</span>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
    }
    public function query_vars( $vars ) {
        $vars[] = 'notification';
        $vars[] = 'private-messages';
        return $vars;
    }
    public function add_metabox() {
        add_meta_box(
            'notification_meta_box',
            'تنظیمات اعلان',
            'display_notification_metabox_pnlt',
            'notification',
            'side',
            'high'
        );
    }
    public function save_metabox($post_id) {
        if (!isset($_POST['notification_meta_box_nonce'])) {
            return;
        }
        if (!wp_verify_nonce($_POST['notification_meta_box_nonce'], 'notification_meta_box_nonce')) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (isset($_POST['notif_type'])) {
            update_post_meta($post_id, 'notif_type', sanitize_text_field($_POST['notif_type']));
        }
        if (isset($_POST['selected_buyers_option'])) {
            update_post_meta($post_id, 'selected_buyers_option', sanitize_text_field($_POST['selected_buyers_option']));
        }
        if (isset($_POST['buyers_of_product'])) {
            update_post_meta($post_id, 'buyers_of_product', sanitize_text_field($_POST['buyers_of_product']));
        }
        if (isset($_POST['selected_users'])) {
            update_post_meta($post_id, 'selected_users', sanitize_text_field($_POST['selected_users']));
        }
    }
    public function metabox_nonce() {
        wp_nonce_field('notification_meta_box_nonce', 'notification_meta_box_nonce');
    }
}
new Panelet_NOTIF;
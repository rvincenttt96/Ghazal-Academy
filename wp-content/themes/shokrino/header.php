<?php defined('ABSPATH') || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?> >
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    wp_head();
    do_action('shkrn-wp-head'); ?>

    <script>
        $ = jQuery;
    </script>
</head>
<body id="shokrino-theme" <?php body_class(); ?> >
<?php
do_action('shkrn_header_template');

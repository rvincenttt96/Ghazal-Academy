<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container">
    <?php if(!shkrn_protected_boolean(shkrn_get_zgboolean())) { ?>
        <div class="title-default-theme-front-page-WC">
            <h2 style="font-weight: 800;color: red;"><?php esc_html_e('The theme license is not registered!', 'shokrino' ); ?></h2>
            <h4 style="font-weight: 400"><?php esc_html_e('To access the features of the theme and activate it, please enter the license in the license registration section.', 'shokrino' ); ?>
            </h4>
            <a href="<?php echo get_site_url().'/wp-admin/admin.php?page=add_shokrino_zg' ?>"><?php esc_html_e('License registration page', 'shokrino' ); ?></a>
        </div>
    <?php } ?>
    <?php if(!function_exists('WC')) { ?>
        <div class="title-default-theme-front-page-WC">
            <h2 style="font-weight: 800;color: red;"><?php esc_html_e('Warning! Required theme plugins are not active', 'shokrino' ); ?></h2>
            <h4 style="font-weight: 400"><?php esc_html_e('Please install and activate the plugins required for the theme as soon as possible so that the theme works properly.', 'shokrino' ); ?>
            </h4>
            <a href="<?php echo get_site_url().'/wp-admin/themes.php?page=install-required-plugins'; ?>"><?php esc_html_e('Install plugins', 'shokrino' ); ?></a>
        </div>
    <?php } ?>
    <?php if(is_front_page()) { ?>
        <div class="title-default-theme-front-page">
            <h2 style="font-weight: 800"><?php esc_html_e('صفحه اصلی برای قالب تعیین نشده است', 'shokrino' ); ?></h2>
            <h4 style="font-weight: 400"><?php esc_html_e('To change the front page, first create a new "page" and then select it from "Settings > Reading" as the home page.', 'shokrino' ); ?></h4>
        </div>
    <?php } ?>
    <?php require_once SHKRN_TMPL.'/page-parts/last-posts-slider.php'; ?>
</div>
<?php get_footer();
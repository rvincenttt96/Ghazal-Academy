<?php
/**
 * Merlin WP configuration file.
 *
 * @package   Merlin WP
 * @version   @@pkg.version
 * @link      https://merlinwp.com/
 * @author    Rich Tabor, from ThemeBeans.com & the team at ProteusThemes.com
 * @copyright Copyright (c) 2018, Merlin WP of Inventionn LLC
 * @license   Licensed GPLv3 for Open Source Use
 */

if ( ! class_exists( 'Merlin' ) ) {
	return;
}

/**
 * Set directory locations, text strings, and settings.
 */
$wizard = new Merlin(

	$config = array(
		'directory'            => 'inc/lib/wizard', // Location / directory where Merlin WP is placed in your theme.
		'merlin_url'           => 'installer-shokrino', // The wp-admin page slug where Merlin WP loads.
		'parent_slug'          => 'features_shokrino', // The wp-admin parent page slug for the admin menu item.
		'capability'           => 'manage_options', // The capability required for this menu to be displayed to the user.
		'child_action_btn_url' => 'https://www.zhaket.com/blog/how-to-create-a-child-theme/', // URL for the 'child-action-link'.
		'dev_mode'             => false, // Enable development mode for testing.
		'license_step'         => false, // EDD license activation step.
		'license_required'     => false, // Require the license activation step.
		'license_help_url'     => '', // URL for the 'license-tooltip'.
		'edd_remote_api_url'   => '', // EDD_Theme_Updater_Admin remote_api_url.
		'edd_item_name'        => '', // EDD_Theme_Updater_Admin item_name.
		'edd_theme_slug'       => '', // EDD_Theme_Updater_Admin item_slug.
		'ready_big_button_url' => get_site_url().'/wp-admin/admin.php?page=add_shokrino_zg', // Link for the big button on the ready step.
	),
	$strings = array(
		'admin-menu'               => esc_html__( 'نصب کننده قالب', 'shokrino' ),

		/* translators: 1: Title Tag 2: Theme Name 3: Closing Title Tag */
		'title%s%s%s%s'            => esc_html__( '%1$s%2$s Themes &lsaquo; Theme Setup: %3$s%4$s', 'shokrino' ),
		'return-to-dashboard'      => esc_html__( 'بازگشت به پیشخوان وردپرس', 'shokrino' ),
		'ignore'                   => esc_html__( 'خروج از نصب کننده', 'shokrino' ),

		'btn-skip'                 => esc_html__( 'رد شدن', 'shokrino' ),
		'btn-next'                 => esc_html__( 'بعدی', 'shokrino' ),
		'btn-start'                => esc_html__( 'شروع', 'shokrino' ),
		'btn-no'                   => esc_html__( 'لغو', 'shokrino' ),
		'btn-plugins-install'      => esc_html__( 'نصب', 'shokrino' ),
		'btn-child-install'        => esc_html__( 'نصب', 'shokrino' ),
		'btn-content-install'      => esc_html__( 'نصب', 'shokrino' ),
		'btn-import'               => esc_html__( 'درون ریزی', 'shokrino' ),
		'btn-license-activate'     => esc_html__( 'فعال سازی', 'shokrino' ),
		'btn-license-skip'         => esc_html__( 'بعدا', 'shokrino' ),

		/* translators: Theme Name */
		'license-header%s'         => esc_html__( 'فعال سازی %s', 'shokrino' ),
		/* translators: Theme Name */
		'license-header-success%s' => esc_html__( '%s فعال شد', 'shokrino' ),
		/* translators: Theme Name */
		'license%s'                => esc_html__( 'Enter your license key to enable remote updates and theme support.', 'shokrino' ),
		'license-label'            => esc_html__( 'License key', 'shokrino' ),
		'license-success%s'        => esc_html__( 'The theme is already registered, so you can go to the next step!', 'shokrino' ),
		'license-json-success%s'   => esc_html__( 'Your theme is activated! Remote updates and theme support are enabled.', 'shokrino' ),
		'license-tooltip'          => esc_html__( 'Need help?', 'shokrino' ),

		/* translators: Theme Name */
		'welcome-header%s'         => esc_html__( 'به نصب کننده شکرینو خوش آمدید', 'shokrino' ),
		'welcome-header-success%s' => esc_html__( 'سلام . خوش برگشتی', 'shokrino' ),
		'welcome%s'                => esc_html__( 'این نصب کننده فقط با چند کلیک افزونه های مورد نیاز قالب و تنظیمات آن را برای شما انجام میدهد.', 'shokrino' ),
		'welcome-success%s'        => esc_html__( 'احتمالا قبلا از این نصب کننده استفاده کرده این با این حال اگر مایل به اجرای مجدد آن هستید روی کلید "شروع" بزنید', 'shokrino' ),

		'child-header'             => esc_html__( 'نصب قالب فرزند', 'shokrino' ),
		'child-header-success'     => esc_html__( 'با موفقیت تصب شد!', 'shokrino' ),
		'child'                    => esc_html__( 'اگر نیاز به کدنویسی روی قالب دارید ، قالب فرزند را نصب کنید و کد های خود را داخل آن قرار دهید .در غیر این صورت روی "رد شدن" کلیک کنید.', 'shokrino' ),
		'child-success%s'          => esc_html__( 'قالب فرزند شکرینو با موفقیت نصب و فعال سازی شد.', 'shokrino' ),
		'child-action-link'        => esc_html__( 'اطلاعات بیشتر درباره قالب فرزند', 'shokrino' ),
		'child-json-success%s'     => esc_html__( 'قالب فرزند شکرینو قبلا نصب شده بود و حالا فعال شد.', 'shokrino' ),
		'child-json-already%s'     => esc_html__( 'عالی است! قالب فرزند شکرینو ساخته شد و با موفقیت روی سایت شما نصب شد.', 'shokrino' ),

		'plugins-header'           => esc_html__( 'نصب افزونه ها', 'shokrino' ),
		'plugins-header-success'   => esc_html__( 'با موفقیت انجام شد', 'shokrino' ),
		'plugins'                  => esc_html__( 'افزونه های مورد نیاز قالب شکرینو در این قسمت نصب میشوند', 'shokrino' ),
		'plugins-success%s'        => esc_html__( 'همه افزونه های مورد نیاز قالب نصب و آپدیت شدند. برای ادامه روی کلید " بعدی" کلیک کنید', 'shokrino' ),
		'plugins-action-link'      => esc_html__( 'مشاهده افزونه ها', 'shokrino' ),

		'import-header'            => esc_html__( 'درون ریزی دمو ها', 'shokrino' ),
		'import'                   => esc_html__( 'تنظیمات و ظاهر پیشنمایش ها را روی سایت خود اعمال کنید!', 'shokrino' ),
		'import-action-link'       => esc_html__( 'پیشرفته', 'shokrino' ),

		'ready-header'             => esc_html__( 'تبریک! همه کار ها انجام شد', 'shokrino' ),

		/* translators: Theme Author */
		'ready%s'                  => esc_html__( 'تمام تنظیمات نصب قالب انجام شد. از قالب شکرینو لذت ببرید!', 'shokrino' ),
		'ready-action-link'        => esc_html__( 'بیشتر', 'shokrino' ),
		'ready-big-button'         => esc_html__( 'فعالسازی لایسنس قالب', 'shokrino' ),
		'ready-link-1'             => sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://www.zhaket.com/web/shokrino-wordpress-theme/support', esc_html__( 'ارتباط با پشتیبانی در ژاکت', 'shokrino' ) ),
		'ready-link-2'             => sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=features_shokrino' ), esc_html__( 'رفتن به شخصی سازی قالب شکرینو', 'shokrino' ) ),
	)
);
function merlin_local_import_files() {
    return array(
        array(
//            'import_file_name'             => 'Demo Shokrino',
//            'local_import_file'            => get_parent_theme_file_path( '/inc/lib/wizard/demo-content/content.xml' ),
//            'local_import_widget_file'     => get_parent_theme_file_path( '/inc/lib/wizard/demo-content/widgets.wie' ),
//            'local_import_customizer_file' => get_parent_theme_file_path( '/inc/lib/wizard/demo-content/customizer.dat' ),
//            'import_redux'               => array(
//                array(
//                    'file_url'    => get_parent_theme_file_path( '/inc/lib/wizard/demo-content/redux_options_.json' ),
//                    'option_name' => 'shokrino_options',
//                ),
//            ),
//            'import_notice'                => __( 'پیشنمایش های قالب شکرینو', 'shokrino' ),
//            'preview_url'                  => 'https://www.zhaket.com/web/shokrino-wordpress-theme/demo',
        ),
    );
}
add_filter( 'merlin_import_files', 'merlin_local_import_files' );

function prefix_merlin_after_import_setup() {
    $main_menu = get_term_by( 'name', 'فهرست اصلی', 'nav_menu' );

    set_theme_mod(
        'nav_menu_locations', array(
            'header-menu' => $main_menu->term_id,
        )
    );

    $front_page_id = get_page_by_title( 'صفحه اصلی حرفه ای' );
    $blog_page_id  = get_page_by_title( 'بلاگ' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

}
add_action( 'merlin_after_all_import', 'prefix_merlin_after_import_setup' );
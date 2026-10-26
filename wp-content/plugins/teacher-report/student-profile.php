<?php
// افزودن تب دانش‌آموز
add_filter('woocommerce_account_menu_items', 'add_student_menu_item');
function add_student_menu_item($items) {
    if (current_user_can('student')) {
        $items['reportcard-student'] = 'کارنامه‌های من';
    }
    return $items;
}
// تاریخ فارسی
require_once plugin_dir_path(__FILE__) . 'include/jdate.php';
// تعریف endpoint برای دانش‌آموز
add_action('init', function() {
    add_rewrite_endpoint('reportcard-student', EP_PAGES);
});
// افزودن تب به پنل کاربری دانش‌آموز
// add_filter('woocommerce_account_menu_items', 'add_student_reportcard_menu_item');
// function add_student_reportcard_menu_item($items) {
//     if (current_user_can('student')) {
//         $items['reportcard-tab'] = 'کارنامه‌های من'; // عنوان تب
//     }
//     return $items;
// }

// ایجاد endpoint برای تب "کارنامه‌های من"
// add_action('init', function() {
//     add_rewrite_endpoint('reportcard-tab', EP_PAGES);
// });
// نمایش کارنامه‌های تأیید شده برای دانش‌آموز
add_action('woocommerce_account_reportcard-student_endpoint', 'display_student_reportcards');
function display_student_reportcards() {
    $current_user = wp_get_current_user(); // تعریف متغیر current_user

    // بررسی اینکه آیا کاربر نقش "student" دارد
    if (!in_array('student', $current_user->roles)) {
        echo "<p>فقط دانش‌آموزان می‌توانند این بخش را مشاهده کنند.</p>";
        return;
    }

    $student_id = $current_user->user_login; // کد دانش‌آموز (user_login)
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    // دریافت کارنامه‌های تایید شده توسط مدیر برای دانش‌آموز
    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name WHERE student_id = %s AND approved_by_admin = 1 ORDER BY created_at DESC", $student_id),
        ARRAY_A
    );


    if (!empty($results)) {
		echo '<div class="student-reportcard-container">';
		echo '<h2>My Reportcard</h2>';
		echo '<table class="student-reportcard-table">';
		echo '<tr><th>Student Code</th><th>Full name</th><th>Level</th><th>Absence</th><th>Activity</th><th>Midterm</th><th>Final</th><th>Total</th><th>supervisor score</th><th>data</th><th>print</th></tr>';

        foreach ($results as $report) {
            echo '<tr>';
            echo '<td>' . esc_html($report['student_id']) . '</td>';
            echo '<td>' . esc_html($report['student_name']) . '</td>';
            echo '<td>' . esc_html($report['student_level']) . '</td>';
            echo '<td>' . esc_html($report['absence']) . '</td>';
            echo '<td>' . esc_html($report['activity']) . '</td>';
            echo '<td>' . esc_html($report['midterm']) . '</td>';
            echo '<td>' . esc_html($report['final']) . '</td>';
            echo '<td>' . esc_html($report['total']) . '</td>';
			echo '<td>' . esc_html($report['supervisor_score']) . '</td>';
            echo '<td>' . esc_html(jdate('Y/m/d', strtotime($report['created_at']))) . '</td>';
            echo '<td><a href="' . site_url() . '/wp-content/plugins/teacher-report/reportcard-print.php?id=' . $report['id'] . '" target="_blank">print <i class="fa-solid fa-print"></i></a></td>';
        }
        echo '</table>';
		echo '</div>';
    } else {
        echo '<p>هیچ کارنامه‌ای برای شما ثبت نشده است یا کارنامه‌ها تایید نشده‌اند.</p>';
    }
}
add_action('wp_enqueue_scripts', 'enqueue_student_profile_styles');
function enqueue_student_profile_styles() {
    // فقط زمانی که کاربر لاگین کرده و در صفحه حساب کاربری ووکامرس هست
    if (is_user_logged_in() && is_account_page()) {
        wp_enqueue_style('student-profile-style', plugin_dir_url(__FILE__) . 'css/student-profile.css');
    }
}

?>

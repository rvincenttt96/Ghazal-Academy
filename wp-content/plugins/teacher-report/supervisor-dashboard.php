<?php

// تعریف اندپوینت برای جلوگیری از 404
add_action('init', function() {
    add_rewrite_endpoint('supervisor-dashboard', EP_ROOT | EP_PAGES);
});

// افزودن تب سوپروایزر به حساب کاربری
add_filter('woocommerce_account_menu_items', 'add_supervisor_menu_item');
function add_supervisor_menu_item($items) {
    if (current_user_can('supervisor')) {
        $items['supervisor-dashboard'] = 'مدیریت کارنامه‌ها';
    }
    return $items;
}

// لود فایل هندلر ایجکس
include_once plugin_dir_path(__FILE__) . 'js/ajax-handler.php';

add_action('woocommerce_account_supervisor-dashboard_endpoint', 'supervisor_reportcard_admin_page');

function supervisor_reportcard_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    // تایید کارنامه
    if (isset($_POST['approve_report_id'])) {
        $wpdb->update($table_name, ['approved_by_admin' => 1], ['id' => intval($_POST['approve_report_id'])]);
        echo '<div class="notice notice-success"><p>✅ کارنامه تایید شد.</p></div>';
    }

    // حذف کارنامه
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $wpdb->delete($table_name, ['id' => intval($_GET['id'])]);
        echo "<div class='notice notice-success'><p>✅ کارنامه حذف شد.</p></div>";
    }

    // افزودن کارنامه
    if (isset($_GET['action']) && $_GET['action'] == 'add') {
        if (isset($_POST['add_reportcard'])) {
            $absence = intval($_POST['absence']);
            $activity = intval($_POST['activity']);
            $midterm = intval($_POST['midterm']);
            $final = intval($_POST['final']);
            $total = $absence + $activity + $midterm + $final;

            $wpdb->insert($table_name, [
                'student_id' => sanitize_text_field($_POST['student_id']),
                'student_name' => sanitize_text_field($_POST['student_name']),
                'student_level' => sanitize_text_field($_POST['student_level']),
                'teacher_name' => sanitize_text_field($_POST['teacher_name']),
                'absence' => $absence,
                'activity' => $activity,
                'midterm' => $midterm,
                'final' => $final,
                'total' => $total,
                'supervisor_score' => intval($_POST['supervisor_score']),
                'created_at' => current_time('mysql')
            ]);
            echo "<div class='notice notice-success'><p>✅ کارنامه جدید ثبت شد.</p></div>";
        }

        include plugin_dir_path(__FILE__) . 'partials/supervisor-form-add.php';
        return;
    }

    // ویرایش کارنامه
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $record = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

        if ($record) {
            if (isset($_POST['update_reportcard'])) {
                $absence = intval($_POST['absence']);
                $activity = intval($_POST['activity']);
                $midterm = intval($_POST['midterm']);
                $final = intval($_POST['final']);
                $total = $absence + $activity + $midterm + $final;

                $wpdb->update($table_name, [
                    'student_id' => sanitize_text_field($_POST['student_id']),
                    'student_name' => sanitize_text_field($_POST['student_name']),
                    'student_level' => sanitize_text_field($_POST['student_level']),
                    'teacher_name' => sanitize_text_field($_POST['teacher_name']),
                    'absence' => $absence,
                    'activity' => $activity,
                    'midterm' => $midterm,
                    'final' => $final,
                    'total' => $total,
                    'supervisor_score' => intval($_POST['supervisor_score']),
                ], ['id' => $id]);

                echo "<div class='notice notice-success'><p>✅ ویرایش انجام شد.</p></div>";
            }

            include plugin_dir_path(__FILE__) . 'partials/supervisor-form-edit.php';
            return;
        }
    }

    // فرم سرچ و افزودن
    echo '<div class="top-bar">
        <div class="add_reportcart">
			<a id="addReportBtn" href="#" class="add-report-btn"><i class="fa-solid fa-plus fa-bounce" style="color: #ffffff;"></i> add report card</a>
        </div>
        <form class="form_search" method="get">
            <input type="hidden" name="supervisor-dashboard" value="">
            <input type="text" name="student_id" placeholder="Student code search" value="' . (isset($_GET['student_id']) ? esc_attr($_GET['student_id']) : '') . '">
            <input type="submit" class="button_search" value="search">
			</form>
    </div>';

    // فیلتر بر اساس جستجو
    $where_clause = '';
    if (!empty($_GET['student_id'])) {
        $search_id = sanitize_text_field($_GET['student_id']);
        $where_clause = $wpdb->prepare("WHERE student_id LIKE %s", '%' . $search_id . '%');
    }

    $results = $wpdb->get_results("SELECT * FROM $table_name $where_clause ORDER BY id DESC");
    $total_reports = count($results);
    echo '<div class="report-table-wrapper">';
    echo "<h2 style='margin-top:30px;'>Score list({$total_reports}item)</h2>";
    if ($results) {
        echo "<table class='wp-list-table widefat striped'>
        <thead><tr>
            <th>row</th>
            <th>code</th>
            <th>Full Name</th>
            <th>Level</th>
            <th>Teacher name</th>
            <th>Absence</th>
            <th>Activity</th>
            <th>Midterm</th>
            <th>Final</th>
            <th>Total</th>
            <th>Supervisor mark</th>
            <th>Date</th>
            <th>status</th>
            <th>activities</th>
            <th>print</th>
        </tr></thead><tbody>";

        $i = 1;
        foreach ($results as $row) {
            $highlight = (empty($row->supervisor_score)) ? "style='background:#ffe0e0'" : "";
            echo "<tr $highlight>
                <td>{$i}</td>
                <td>{$row->student_id}</td>
                <td>{$row->student_name}</td>
                <td>{$row->student_level}</td>
                <td>{$row->teacher_name}</td>
                <td>{$row->absence}</td>
                <td>{$row->activity}</td>
                <td>{$row->midterm}</td>
                <td>{$row->final}</td>
                <td>{$row->total}</td>
                <td>{$row->supervisor_score}</td>
                <td>" . jdate('Y/m/d', strtotime($row->created_at)) . "</td>
                <td>";
                if ($row->approved_by_admin) {
                    echo '✅ تایید شده';
                } else {
                    echo '❌ نیاز به تایید';
                    echo "<form method='post' style='display:inline'><input type='hidden' name='approve_report_id' value='{$row->id}'><input type='submit' class='button' value='تایید'></form>";
                }
            echo "</td>
                <td>
					<a href='#' class='edit-btn' data-id='{$row->id}'>✏️</a>
					<a href='#' class='delete-reportcard' data-id='{$row->id}';'>🗑️</a>
                </td>
                <td><a href='" . site_url("/wp-content/plugins/teacher-report/reportcard-print.php?id={$row->id}") . "' target='_blank'>🖨</a></td>
            </tr>";
            $i++;
        }

        echo "</tbody></table>";
    } else {
        echo "<p>هیچ کارنامه‌ای یافت نشد.</p>";
    }
// پاپ‌آپ ویرایش نمرات
echo '
<div id="Popup" class="edit-popup-overlay" style="display:none;"> 
    <div class="edit-popup-content">
        <span id="closePopup" class="close-popup">×</span>
        <div id="popupFormContainer">در حال بارگذاری...</div>
    </div>
</div>';}

// لود CSS و JS فقط برای کاربران سوپروایزر
add_action('wp_enqueue_scripts', function() {
    if (is_account_page() && is_user_logged_in() && current_user_can('supervisor')) {
        wp_enqueue_style('supervisor-dashboard', plugin_dir_url(__FILE__) . 'css/supervisor-dashboard.css');
        wp_enqueue_script('supervisor-dashboard-js', plugin_dir_url(__FILE__) . 'js/supervisor-dashboard.js', ['jquery'], null, true);
        wp_localize_script('supervisor-dashboard-js', 'supervisor_ajax', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }
});

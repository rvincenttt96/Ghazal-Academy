<?php
// افزودن منو به پنل مدیریت
require_once plugin_dir_path(__FILE__) . 'include/jdate.php';

// لود اسکریپت‌ها توی داشبورد مدیریت
function reportcard_enqueue_admin_scripts($hook) {
    if ($hook != 'toplevel_page_reportcard-admin') {
        return;
    }
    wp_enqueue_script(
        'admin-dashboard-script',
        plugins_url('js/admin-dashboard.js', __FILE__), // اطمینان حاصل کن که این فایل وجود دارد
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'js/admin-dashboard.js'),
        true
    );
	    wp_localize_script('admin-dashboard-script', 'reportcard_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
    // لود استایل
    $css_path = plugins_url('css/admin-dashboard.css', __FILE__);
    echo "<script>console.log('CSS Path: " . $css_path . "');</script>";
    wp_enqueue_style(
        'admin-dashboard-style',
        $css_path,
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'css/admin-dashboard.css')
    );
	echo "<script>console.log('Scripts loaded. Ajax URL: " . admin_url('admin-ajax.php') . "');</script>";
}
add_action('admin_enqueue_scripts', 'reportcard_enqueue_admin_scripts');


add_action('admin_menu', 'reportcard_admin_menu');
function reportcard_admin_menu() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    $new_reports_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_seen = 0");

    add_menu_page(
        'مدیریت کارنامه‌ها',
        'کارنامه‌ها' . ($new_reports_count > 0 ? " <span class='awaiting-mod count-{$new_reports_count}'>{$new_reports_count}</span>" : ''),
        'manage_options',
        'reportcard-admin',
        'reportcard_admin_page',
        'dashicons-clipboard',
        5
    );
}

// تغییر وضعیت کارنامه‌ها به "دیده شده"
add_action('admin_init', 'mark_reports_as_seen');
function mark_reports_as_seen() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    if (isset($_GET['page']) && $_GET['page'] === 'reportcard-admin') {
        $wpdb->update(
            $table_name,
            ['is_seen' => 1],
            ['is_seen' => 0]
        );
    }
}

// صفحه مدیریت
function reportcard_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    // تایید کارنامه توسط مدیر
    if (isset($_POST['approve_report_id'])) {
        $report_id = intval($_POST['approve_report_id']);
        $wpdb->update(
            $table_name,
            ['approved_by_admin' => 1],
            ['id' => $report_id]
        );
        echo '<div class="notice notice-success"><p>✅ کارنامه تایید شد.</p></div>';
    }

    // حذف کارنامه
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, ['id' => $id]);
        echo "<div class='notice notice-success'><p>کارنامه با موفقیت حذف شد.</p></div>";
    }

    // فرم افزودن کارنامه جدید
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

            echo "<div class='notice notice-success'><p>✅ کارنامه جدید با موفقیت اضافه شد.</p></div>";
        }

        // نمایش فرم
        echo '<h2>افزودن کارنامه جدید</h2>
        <form method="post">
            <p>    <label>کد دانش‌آموز: 
        <input type="text" name="student_id" id="admin_student_id" required>
        <span id="student_error" style="display:none;"></span>
    </label></p>
            <p><label>نام و نام خانوادگی: <input type="text" name="student_name" required></label></p>
            <p><label>سطح: <input type="text" name="student_level" required></label></p>
            <p><label>نام معلم: <input type="text" name="teacher_name" required></label></p>
            <p><label>Absence (0-4): <input type="number" name="absence" min="0" max="4" required></label></p>
            <p><label>Activity (0-30): <input type="number" name="activity" min="0" max="30" required></label></p>
            <p><label>Midterm (0-16): <input type="number" name="midterm" min="0" max="16" required></label></p>
            <p><label>Final (0-50): <input type="number" name="final" min="0" max="50" required></label></p>
            <p><label>نمره سوپروایزر: <input type="number" name="supervisor_score" min="0" max="100"></label></p>
            <p><input type="submit" name="add_reportcard" value="ثبت کارنامه" class="button button-primary"></p>
			</form><hr>';
        return;
    }

    // فرم ویرایش
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

                echo "<div class='notice notice-success'><p>کارنامه با موفقیت ویرایش شد.</p></div>";
                $record = null;
            } else {
                ?>
                <h2>ویرایش کارنامه</h2>
                <form method="post">
                    <p><label>کد دانش‌آموز:<input type="text" name="student_id" id="edit_student_id" value="<?php echo esc_attr($record->student_id); ?>"></label></p>
                    <p><label>نام و نام خانوادگی: <input type="text" name="student_name" value="<?php echo esc_attr($record->student_name); ?>"></label></p>
                    <p><label>سطح: <input type="text" name="student_level" value="<?php echo esc_attr($record->student_level); ?>"></label></p>
                    <p><label>نام معلم: <input type="text" name="teacher_name" value="<?php echo esc_attr($record->teacher_name); ?>"></label></p>
                    <p><label>Absence (0-4): <input type="number" name="absence" min="0" max="4" value="<?php echo esc_attr($record->absence); ?>"></label></p>
                    <p><label>Activity (0-30): <input type="number" name="activity" min="0" max="30" value="<?php echo esc_attr($record->activity); ?>"></label></p>
                    <p><label>Midterm (0-16): <input type="number" name="midterm" min="0" max="16" value="<?php echo esc_attr($record->midterm); ?>"></label></p>
                    <p><label>Final (0-50): <input type="number" name="final" min="0" max="50" value="<?php echo esc_attr($record->final); ?>"></label></p>
                    <p><label>نمره سوپروایزر: <input type="number" name="supervisor_score" min="0" max="100" value="<?php echo esc_attr($record->supervisor_score); ?>"></label></p>
                    <p><input type="submit" name="update_reportcard" value="ذخیره تغییرات" class="button button-primary"></p>
                </form>
                <hr>
                <?php
                return;
            }
        }
    }

    echo '<div class="titr"><h2>کارنامه‌های دانش آموزان</h2></div>';
    // بخش افزودن و جستجو 
    echo '<div class="top-bar">
        <div class="add_reportcart">
            <a href="?page=reportcard-admin&action=add" >➕ افزودن کارنامه جدید</a>
        </div>
        <form class="form_search" method="get">
            <input type="hidden" name="page" value="reportcard-admin">
            <input type="text" name="student_id" placeholder="جستجوی کد دانش‌آموز" value="' . (isset($_GET['student_id']) ? esc_attr($_GET['student_id']) : '') . '">
            <input type="submit" class="button_search" value="جستجو">
        </form>
    </div>';

    // فیلتر کردن نتایج بر اساس کد دانش‌آموز در جستجو
    $where_clause = "";
    if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
        $search_id = sanitize_text_field($_GET['student_id']);
        $where_clause = $wpdb->prepare("WHERE student_id LIKE %s", '%' . $search_id . '%');
    }

    // نمایش نتایج جستجو
    $results = $wpdb->get_results("SELECT * FROM $table_name $where_clause ORDER BY id DESC");

    // نمایش جدول همه کارنامه‌ها
    $total_reports = count($results);
    echo "<h2>لیست کارنامه‌های ثبت شده ({$total_reports} مورد)</h2>";
    if ($results) {
        echo "<table class='wp-list-table widefat fixed striped'>";
        echo "<thead><tr>
            <th>ردیف</th>
            <th>کد</th>
            <th>نام دانش‌آموز</th>
            <th>سطح</th>
            <th>معلم</th>
            <th>Absence</th>
            <th>Activity</th>
            <th>Midterm</th>
            <th>Final</th>
            <th>Total</th>
            <th>نمره سوپروایزر</th> 
            <th>تاریخ</th>
            <th>عملیات/وضعیت</th>
            <th>ویرایش/ حذف</th>
            <th>چاپ</th>
        </tr></thead><tbody>";
        $counter = 1;
        foreach ($results as $row) {
            $highlight = (empty($row->supervisor_score) || $row->supervisor_score == 0) ? "style='background-color:#ffaaaa'" : "";
            echo "<tr $highlight>
                <td>{$counter}</td>
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
                <td>"
                    . ($row->approved_by_admin ? '✅ تایید شده' : '❌ نیاز به تایید');
                if (!$row->approved_by_admin) {
                    echo "<form method='post' style='display: inline;'>
                        <input type='hidden' name='approve_report_id' value='{$row->id}'>
                        <input type='submit' class='button' value='تایید'>
                    </form>";
                }
                echo "</td>
                <td>
                    <a href='?page=reportcard-admin&action=edit&id={$row->id}'>✏️</a> | 
                    <a href='?page=reportcard-admin&action=delete&id={$row->id}' onclick=\"return confirm('آیا از حذف مطمئن هستید؟')\">🗑️</a>
                </td>
                <td><a href='" . site_url() . "/wp-content/plugins/teacher-report/reportcard-print.php?id={$row->id}' target='_blank'>🖨 چاپ</a></td>
            </tr>";
            $counter++;
        }
        echo "</tbody></table>";
    } else {
        echo "<p>هیچ کارنامه‌ای ثبت نشده است.</p>";
    }
}
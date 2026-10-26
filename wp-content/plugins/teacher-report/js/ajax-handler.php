<?php
// امنیت
if (!defined('ABSPATH')) exit;

// AJAX برای بررسی کد دانش‌آموز
add_action('wp_ajax_check_student_id', 'handle_check_student_id');
add_action('wp_ajax_nopriv_check_student_id', 'handle_check_student_id');

function handle_check_student_id() {
    $student_id = sanitize_text_field($_POST['student_id']);
    $user = get_user_by('login', $student_id);

    if ($user) {
        wp_send_json([
            'success' => true,
            'name' => $user->first_name . ' ' . $user->last_name
        ]);
    } else {
        wp_send_json(['success' => false]);
    }
}

// AJAX برای ثبت کارنامه
add_action('wp_ajax_submit_reportcard', 'handle_ajax_reportcard_submission');
add_action('wp_ajax_nopriv_submit_reportcard', 'handle_ajax_reportcard_submission');

function handle_ajax_reportcard_submission() {
    check_ajax_referer('reportcard_submit_action', 'reportcard_nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        wp_send_json_error(['message' => '❌ جدول کارنامه‌ها وجود ندارد.']);
    }

    $student_id = sanitize_text_field($_POST['student_id']);
    $user = get_user_by('login', $student_id);
    if (!$user) {
        wp_send_json_error(['message' => '❌ کد دانش‌آموز یافت نشد.']);
    }

    $absence = intval($_POST['absence']);
    $activity = intval($_POST['activity']);
    $midterm = intval($_POST['midterm']);
    $final = intval($_POST['final']);
    $total = $absence + $activity + $midterm + $final;

    $result = $wpdb->insert($table_name, array(
        'student_id'     => $student_id,
        'student_name'   => sanitize_text_field($_POST['student_name']),
        'student_level'  => sanitize_text_field($_POST['student_level']),
        'teacher_name'   => sanitize_text_field($_POST['teacher_name']),
        'absence'        => $absence,
        'activity'       => $activity,
        'midterm'        => $midterm,
        'final'          => $final,
        'total'          => $total,
        'created_at'     => current_time('mysql')
    ));

    if ($result) {
        wp_send_json_success(['message' => '✅ کارنامه با موفقیت ثبت شد.']);
    } else {
        wp_send_json_error(['message' => '❌ خطا در ثبت کارنامه: ' . esc_html($wpdb->last_error)]);
    }
}

// حذف کارنامه در داشبورد سوپروایزر AJAX
add_action('wp_ajax_delete_reportcard_ajax', function() {
    if (!current_user_can('supervisor')) {
        wp_send_json_error();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    $id = isset($_POST['report_id']) ? intval($_POST['report_id']) : 0;

    if ($id > 0) {
        $deleted = $wpdb->delete($table_name, ['id' => $id]);
        if ($deleted !== false) {
            wp_send_json_success();
        }
    }

    wp_send_json_error();
});

add_action('wp_ajax_get_edit_form_ajax', function() {
    if (!current_user_can('supervisor')) wp_send_json_error();

    global $wpdb;
    $id = intval($_POST['report_id']);
    $table = $wpdb->prefix . 'rcg_reports';
    $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id");

    if ($row) {
        ob_start(); ?>
        <form id="editReportForm">
            <input type="hidden" name="action" value="update_reportcard_ajax">
            <input type="hidden" name="report_id" value="<?= esc_attr($row->id) ?>">
            <label>Absence: <input type="number" name="absence" value="<?= esc_attr($row->absence) ?>"></label><br>
            <label>Activity: <input type="number" name="activity" value="<?= esc_attr($row->activity) ?>"></label><br>
            <label>Midterm: <input type="number" name="midterm" value="<?= esc_attr($row->midterm) ?>"></label><br>
            <label>Final: <input type="number" name="final" value="<?= esc_attr($row->final) ?>"></label><br>
            <label>نمره ناظر: <input type="number" name="supervisor_score" 0" max="100" value="<?= esc_attr($row->supervisor_score) ?>"></label><br>
            <button type="submit">Save</button>
        </form>
        <?php
        wp_send_json_success(ob_get_clean());
    }

    wp_send_json_error();
});

add_action('wp_ajax_update_reportcard_ajax', function() {
    if (!current_user_can('supervisor')) wp_send_json_error();

    global $wpdb;
    $table = $wpdb->prefix . 'rcg_reports';

    $id = intval($_POST['report_id']);
    $absence = intval($_POST['absence']);
    $activity = intval($_POST['activity']);
    $midterm = intval($_POST['midterm']);
    $final = intval($_POST['final']);
    $supervisor_score = intval($_POST['supervisor_score']);

    $total = $absence + $activity + $midterm + $final;

    $updated = $wpdb->update($table, [
        'absence' => $absence,
        'activity' => $activity,
        'midterm' => $midterm,
        'final' => $final,
        'total' => $total,
        'supervisor_score' => $supervisor_score
    ], ['id' => $id]);

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
});
// ادد کارنامه 
add_action('wp_ajax_get_add_form_ajax', function () {
    if (!current_user_can('supervisor')) wp_send_json_error();

    ob_start(); ?>
    <form id="addReportForm">
        <input type="hidden" name="action" value="add_reportcard_ajax">
		<h3>Add reportcard </h3>	
        <label>کد دانش‌آموز:
                <input type="text" name="student_id" id="student_id" required>
    			<span id="student_id_status" style="display:none; font-size:13px;"></span>
        </label><br>

        <label>نام و نام خانوادگی:
            <input type="text" name="student_name" id="student_name" required>
        </label><br>

        <label>پایه تحصیلی:
            <input type="text" name="student_level" required>
        </label><br>

        <label>نام دبیر:
            <input type="text" name="teacher_name" required>
        </label><br>

        <label>غیبت:
            <input type="number" name="absence" required>
        </label><br>

        <label>فعالیت:
            <input type="number" name="activity" required>
        </label><br>

        <label>میان‌ترم:
            <input type="number" name="midterm" required>
        </label><br>

        <label>پایان‌ترم:
            <input type="number" name="final" required>
        </label><br>

        <label>نمره ناظر:
            <input type="number" name="supervisor_score" required>
        </label><br>

        <button type="submit">ثبت کارنامه</button>
    </form>
    <?php

    wp_send_json_success(ob_get_clean());
});


add_action('wp_ajax_add_reportcard_ajax', function () {
    if (!current_user_can('supervisor') && !current_user_can('administrator')) {
        wp_send_json_error(['message' => 'شما دسترسی لازم را ندارید.']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'rcg_reports';

    $student_id     = sanitize_text_field($_POST['student_id']);
    $student_name   = sanitize_text_field($_POST['student_name']);
    $student_level  = sanitize_text_field($_POST['student_level']);
    $teacher_name   = sanitize_text_field($_POST['teacher_name']);
    $absence        = intval($_POST['absence']);
    $activity       = intval($_POST['activity']);
    $midterm        = intval($_POST['midterm']);
    $final          = intval($_POST['final']);
    $supervisor_score = intval($_POST['supervisor_score']);

    $total = $absence + $activity + $midterm + $final;

    $inserted = $wpdb->insert($table, [
        'student_id'        => $student_id,
        'student_name'      => $student_name,
        'student_level'     => $student_level,
        'teacher_name'      => $teacher_name,
        'absence'           => $absence,
        'activity'          => $activity,
        'midterm'           => $midterm,
        'final'             => $final,
        'total'             => $total,
        'supervisor_score'  => $supervisor_score,
        'created_at'        => current_time('mysql')
    ]);

    if ($inserted !== false) {
        wp_send_json_success(['message' => '✅ کارنامه با موفقیت ذخیره شد.']);
    } else {
        wp_send_json_error(['message' => '❌ خطا در ذخیره کارنامه.']);
    }
});
//حذف کارنامه قسمت معلم 
add_action('wp_ajax_delete_teacher_reportcard_ajax', function() {
    if (!current_user_can('teacher')) {
        wp_send_json_error(['message' => 'دسترسی ندارید']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    $id = isset($_POST['report_id']) ? intval($_POST['report_id']) : 0;

    if ($id > 0) {
        $deleted = $wpdb->delete($table_name, ['id' => $id]);
        if ($deleted !== false) {
            wp_send_json_success(['message' => 'حذف شد']);
        }
    }

    wp_send_json_error(['message' => 'خطا در حذف']);
});

add_action('wp_ajax_update_teacher_reportcard', function() {
    if (!current_user_can('teacher')) {
        wp_send_json_error(['msg' => 'دسترسی غیرمجاز']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'rcg_reports';
    $id = intval($_POST['id']);
    $absence = intval($_POST['absence']);
    $activity = intval($_POST['activity']);
    $midterm = intval($_POST['midterm']);
    $final = intval($_POST['final']);
    $total = $absence + $activity + $midterm + $final;

    $updated = $wpdb->update(
        $table,
        [
            'absence' => $absence,
            'activity' => $activity,
            'midterm' => $midterm,
            'final' => $final,
            'total' => $total,
        ],
        ['id' => $id],
        ['%d', '%d', '%d', '%d', '%d'],
        ['%d']
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['msg' => 'خطا در آپدیت']);
    }
});


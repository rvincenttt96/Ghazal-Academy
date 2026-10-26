<?php
// تعریف نقش دانش‌آموز
function rcg_add_student_role() {
    add_role(
        'student',
        'دانش‌آموز',
        [
            'read' => true,
        ]
    );
}

// تعریف نقش سوپروایزر
function rcg_add_supervisor_role() {
    add_role(
        'supervisor',
        'سوپروایزر',
        [
            'read' => true,
            'edit_posts' => true,
            'manage_options' => true,
            'list_users' => true,
            'edit_users' => true,
            'delete_users' => false, // به‌دلخواه می‌تونی تغییر بدی
        ]
    );
}

// حذف نقش‌ها در زمان غیرفعال‌سازی پلاگین
function rcg_remove_custom_roles() {
    remove_role('student');
    remove_role('supervisor');
}

// تنظیم خودکار نقش برای کاربر جدید (اختیاری فقط برای دانش‌آموز)
function rcg_set_default_role_student($user_id) {
    $user = new WP_User($user_id);
    $user->set_role('student');
}

// هوک‌های فعال‌سازی و غیرفعال‌سازی
register_activation_hook(__FILE__, function() {
    rcg_add_student_role();
    rcg_add_supervisor_role();
});
register_deactivation_hook(__FILE__, 'rcg_remove_custom_roles');

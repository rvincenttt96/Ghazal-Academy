<?php
/*
Plugin Name: Report Card ghazal
Description: افزونه ثبت و مدیریت کارنامه دانش‌آموزان آموزشگاه غزال
Version: 2.9.5
*/

// لود کردن اسکریپت‌ها
function reportcard_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script(
        'reportcard-ajax',
        plugin_dir_url(__FILE__) . 'js/reportcard-ajax.js',
        array('jquery'),
        '1.0',
        true
    );
    wp_localize_script(
        'reportcard-ajax',
        'reportcard_ajax',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );
}
add_action('wp_enqueue_scripts', 'reportcard_enqueue_scripts');

require_once plugin_dir_path(__FILE__) . 'js/ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'include/jdate.php';
register_activation_hook(__FILE__, 'reportcard_create_table');

function reportcard_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(50),
        student_name VARCHAR(100),
        student_level VARCHAR(50),
        teacher_name VARCHAR(100),
        absence INT,
        activity INT,
        midterm INT,
        final INT,
        total INT,
		  supervisor_score INT DEFAULT 0,  
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
// افزودن ستون is_seen به جدول اگر وجود نداشته باشد
function reportcard_add_is_seen_column_if_not_exists() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';
    $column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'is_seen'");
    if (empty($column)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN is_seen TINYINT DEFAULT 0");
    }
	$column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'approved_by_admin'");
    if (empty($column)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN approved_by_admin TINYINT DEFAULT 0");
    }
}
add_action('init', 'reportcard_add_is_seen_column_if_not_exists');

// نمایش فرم برای معلم
add_action('woocommerce_account_reportcard-teacher_endpoint', 'reportcard_form_for_teacher');
function reportcard_form_for_teacher() {
    if (!current_user_can('teacher')) {
        echo "<p>فقط معلمان می‌توانند این بخش را مشاهده کنند.</p>";
        return;
    }
    echo '<div class="teacher-form-wrapper">'; // ✅ اینجا اضافه کن
    // ارسال فرم
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reportcard'])) {
        handle_reportcard_submission();
    }
    $current_user = wp_get_current_user();
    ?>
    <form id="reportcard-form" method="post">
        <p><label>Student's Code: 
                <input type="text" name="student_id" id="student_id" required onblur="checkStudentId('student_id', 'student_id_status')">
            </label>
            <span id="student_id_status"></span>
		</p>
        <p><label>Full name:<input type="text" name="student_name" id="student_name"  readonly></label></p>
        <p><label>Level: <input type="text" name="student_level" id="student_level" required></label></p>
        <p><label>Teacher's name:<input type="text" name="teacher_name" id="teacher_name" value="<?php echo esc_attr($current_user->display_name); ?>" readonly></label></p>
        <p><label>Absence/Presence (از 0 تا 4): <input type="number" name="absence" id="absence" min="0" max="4" required></label></p>
        <p><label>Class Activity (از 0 تا 30): <input type="number" name="activity" id="activity" min="0" max="30" required></label></p>
        <p><label>Midterm (از 0 تا 16): <input type="number" name="midterm" id="midterm" min="0" max="16" required></label></p>
        <p><label>Final (از 0 تا 50): <input type="number" name="final" id="final" min="0" max="50" required></label></p>
		<?php wp_nonce_field('reportcard_submit_action', 'reportcard_nonce'); ?>
        <p><input type="submit" name="submit_reportcard" value="Register"></p>
		<div id="form-message" style="margin-top:10px;"></div>
		<?php wp_nonce_field('reportcard_submit_action', 'reportcard_nonce'); ?>
    </form>	
<hr>
    <?php
	$current_user = wp_get_current_user();
    $teacher_name = $current_user->display_name; // نام پروفایل معلم
    $user_id = get_current_user_id();
	global $wpdb;
	$table_name = $wpdb->prefix . 'rcg_reports';
	$results = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $table_name WHERE teacher_name = %s ORDER BY created_at DESC", $teacher_name),
    ARRAY_A
		);
    if (!empty($results)) {
        echo '<div style="border-top: 3px solid #888888; padding-top: 20px;"></div>';
	echo '<div class="teacher-table-wrapper">';
	echo '<table class="teacher-report-table">';
	echo '<div style="text-align: center;" > <h3>Submitted Report Cards</h3> </div>';
echo '<table class="teacher-report-table">';
echo '<thead>
    <tr>
        <th>Code</th>
        <th>Full name</th>
        <th>Level</th>
        <th>Teacher name</th>
        <th>Absence</th>
        <th>Activity</th>
        <th>Midterm</th>
        <th>Final</th>
        <th>Total</th>
        <th>Date</th>
		<th>Operation</th>
    </tr>
</thead>';
echo '<tbody>';
foreach ($results as $report) {
    echo '<tr>';
    echo '<td>' . esc_html($report['student_id']) . '</td>';
    echo '<td>' . esc_html($report['student_name']) . '</td>';
    echo '<td>' . esc_html($report['student_level']) . '</td>';
    echo '<td>' . esc_html($teacher_name) . '</td>';
    echo '<td>' . esc_html($report['absence']) . '</td>';
    echo '<td>' . esc_html($report['activity']) . '</td>';
    echo '<td>' . esc_html($report['midterm']) . '</td>';
    echo '<td>' . esc_html($report['final']) . '</td>';
    echo '<td>' . esc_html($report['total']) . '</td>';
    echo '<td>' . jdate('Y/m/d ', strtotime($report['created_at'])) . '</td>';
	echo '<td>
		<a href="#" class="edit-teacher-reportcard"
			data-id="' . esc_attr($report['id']) . '"
			data-absence="' . esc_attr($report['absence']) . '"
			data-activity="' . esc_attr($report['activity']) . '"
			data-midterm="' . esc_attr($report['midterm']) . '"
			data-final="' . esc_attr($report['final']) . '"
		>✏️</a>
		&nbsp;&nbsp;
		<a href="#" class="delete-teacher-reportcard"
			data-id="' . esc_attr($report['id']) . '"
		>🗑️</a>
	</td>';
    echo '</tr>';
}


echo '</tbody>';
echo '</table>';
		echo '</div>';
    } else {
        echo '<p>هیچ کارنامه‌ای ثبت نشده است.</p>';
    }
	echo '
<div id="Popup" class="edit-popup-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.5); z-index:9998;">
    <div class="edit-popup-content" style="background:#fff; padding:20px; border-radius:10px;
    box-shadow:0 0 15px rgba(0,0,0,0.3); max-width:400px; margin:10% auto; position:relative;">
        <span id="closePopup" class="close-popup" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">×</span>
        <form id="edit-form">
            <input type="hidden" id="edit-id">
            <p>Absence: <input type="number" id="edit-absence" min="0" max="4"></p>
            <p>Activity: <input type="number" id="edit-activity" min="0" max="30"></p>
            <p>Midterm: <input type="number" id="edit-midterm" min="0" max="16"></p>
            <p>Final: <input type="number" id="edit-final" min="0" max="50"></p>
            <div style="margin-top:15px; text-align:right;">
                <button type="button" id="submit-edit" style="background:#28a745; color:#fff; padding:5px 15px; border:none; border-radius:5px;">ذخیره</button>
                <button type="button" id="cancel-edit" style="background:#dc3545; color:#fff; padding:5px 10px; border:none; border-radius:5px;">لغو</button>
            </div>
        </form>
    </div>
</div>';
	?>
	
<?php
}

function handle_reportcard_submission() {
// چک کردن nonce
    if (!isset($_POST['reportcard_nonce']) || !wp_verify_nonce($_POST['reportcard_nonce'], 'reportcard_submit_action')) {
        echo '<div style="color:red;">❌ خطای امنیتی: درخواست نامعتبر است.</div>';
        return;
    }
	
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcg_reports';

    // بررسی وجود جدول
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo "<div style='color:red;'>❌ جدول کارنامه‌ها وجود ندارد.</div>";
        return;
    }
    // ✅ بررسی وجود کد دانش‌آموز در لیست کاربران
    $student_id = sanitize_text_field($_POST['student_id']);
    $user = get_user_by('login', $student_id);
    if (!$user) {
		echo '<div class="report-message-error">❌ کد دانش‌آموز یافت نشد.</div>';
        return;
    }

    // محاسبه نمره نهایی
    $absence = intval($_POST['absence']);
    $activity = intval($_POST['activity']);
    $midterm = intval($_POST['midterm']);
    $final = intval($_POST['final']);
    $total = $absence + $activity + $midterm + $final;

    // درج در دیتابیس
    $result = $wpdb->insert($table_name, array(
        'student_id' => sanitize_text_field($_POST['student_id']),
        'student_name' => sanitize_text_field($_POST['student_name']),
        'student_level' => sanitize_text_field($_POST['student_level']),
        'teacher_name' => sanitize_text_field($_POST['teacher_name']),
        'absence' => $absence,
        'activity' => $activity,
        'midterm' => $midterm,
        'final' => $final,
        'total' => $total,
        'created_at' => current_time('mysql')
    ));

    if ($result) {
		echo '<div class="report-message-success">✅ کارنامه با موفقیت ثبت شد.</div>';
    } else {
        echo "<div style='color:red;'>❌ خطا در ثبت کارنامه: " . esc_html($wpdb->last_error) . "</div>";
    }
}

// افزودن تب به پنل کاربری معلم
add_filter('woocommerce_account_menu_items', 'add_reportcard_menu_item');
function add_reportcard_menu_item($items) {
    if (current_user_can('teacher')) {
        $items['reportcard-teacher'] = 'ثبت کارنامه';
    }
    return $items;
}
add_action('wp_enqueue_scripts', 'rcg_enqueue_teacher_styles');
function rcg_enqueue_teacher_styles() {
    if (is_account_page() && is_user_logged_in() && current_user_can('teacher')) {
        wp_enqueue_style('teacher-profile-style', plugin_dir_url(__FILE__) . 'css/teacher-profile.css', array(), '1.0');
    }
}
add_action('init', function() {
    add_rewrite_endpoint('reportcard-teacher', EP_PAGES);
});
// بارگذاری فایل student-profile.php
include(plugin_dir_path(__FILE__) . 'student-profile.php');
// فراخوانی کد داشبود
require_once plugin_dir_path(__FILE__) . 'admin-dashboard.php';
// پروفایل سوپروایزر
require_once plugin_dir_path(__FILE__) . 'supervisor-dashboard.php';
// ساخت پروفایل دانش آموز
register_activation_hook(__FILE__, 'rcg_add_roles_on_activation');
function rcg_add_roles_on_activation() {
    rcg_add_student_role();
    rcg_add_supervisor_role();
}
require_once plugin_dir_path(__FILE__) . 'roles.php';
// // اکشن هنگام فعال‌سازی پلاگین
// register_activation_hook(__FILE__, 'rcg_add_student_role');
// add_action('admin_enqueue_scripts', function() {
//     if (/* شرط مخصوص صفحه معلم */) {
//         wp_enqueue_script('teacher-js', plugin_dir_url(__FILE__) . 'assets/js/teacher.js', ['jquery'], null, true);
//         wp_localize_script('teacher-js', 'teacher_ajax', [
//             'ajax_url' => admin_url('admin-ajax.php')
//         ]);
//     }
// });


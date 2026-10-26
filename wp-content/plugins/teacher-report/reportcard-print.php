<?php
require_once('../../../wp-load.php'); // آدرس دهی مناسب برای بارگیری وردپرس

// چک کردن نقش کاربر
if (!current_user_can('manage_options') && !current_user_can('student')) {
    wp_die('دسترسی غیرمجاز!');
}

global $wpdb;
$table_name = $wpdb->prefix . 'rcg_reports';

// اگر id کارنامه وجود نداشته باشد
if (!isset($_GET['id'])) {
    wp_die('کارنامه‌ای انتخاب نشده است.');
}

$id = intval($_GET['id']);
$report = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

if (!$report) {
    wp_die('کارنامه یافت نشد.');
}

// اگر کاربر دانش‌آموز است، فقط کارنامه خودش را بتواند مشاهده کند
if (current_user_can('student_role') && get_current_user_id() != $report->student_id) {
    wp_die('شما اجازه دسترسی به این کارنامه را ندارید.');
}
wp_enqueue_style('student-profile', plugin_dir_url(__FILE__) . 'css/reportcard-print.css');
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>چاپ کارنامه</title>
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'css/reportcard-print.css'; ?>?v=<?php echo time(); ?>">
</head>
<body>

<div class="report-card">
    <div class="logo">
	    <img src="img/logo.png" alt="">
	</div>
    <h1>Report Card</h1>
    <h2>Ghazal Institut</h2>

    <div class="info">
        <div><strong>کد دانش‌آموز:</strong> <?php echo esc_html($report->student_id); ?></div>
        <div><strong>نام و نام خانوادگی:</strong> <?php echo esc_html($report->student_name); ?></div>
        <div><strong>سطح:</strong> <?php echo esc_html($report->student_level); ?></div>
        <div><strong>نام معلم:</strong> <?php echo esc_html($report->teacher_name); ?></div>
    </div>

    <div class="table">
        <table style="width:100%">
            <thead>
                <tr>
					<th>GRADING SYSTEM</th>
                     <th>FINAL MARK</th>
                    <th>SUBJECT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>0 - 4</td>
                    <td><?php echo esc_html($report->absence); ?></td>
					<td>Absence/Presence</td>
                </tr>
                <tr>
                    <td>0 - 30</td>
                    <td><?php echo esc_html($report->activity); ?></td>
					<td>Class Activity</td>
                </tr>
                <tr>
                    <td>0 - 16</td>
                    <td><?php echo esc_html($report->midterm); ?></td>
                    <td>Midterm</td>
                </tr>
                <tr>
                    <td>0 - 50</td>
                    <td><?php echo esc_html($report->final); ?></td>
                    <td>Final</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="total">
        <strong>Supervisor Score:</strong> <?php echo esc_html($report->supervisor_score); ?>
    </div>

    <div class="total">
        <strong>Total Mark:</strong> <?php echo esc_html($report->total); ?>
    </div>
	
	<div class="no-print" style="text-align: center; margin-top: 30px;">
    <button onclick="window.print()">🖨 چاپ کارنامه</button>
	</div>
</div>



<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>


<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ================================
// بخش AJAX - باید اول اجرا بشه
// ================================
$raw_input = file_get_contents('php://input');
$input_data = null;

if ($raw_input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input_data = json_decode($raw_input, true);
} else {
    $input_data = $_POST;
}

// AJAX: دریافت داده‌ها
if (!empty($_GET['action']) && $_GET['action'] == 'get_data') {
    require_once '../../includes/db.php';
    require_once '../../includes/jdate.php';
    header('Content-Type: application/json');
    
    $class_id = (int)($_GET['class_id'] ?? 0);
    $jalali_date = $_GET['date'] ?? '';
    
    if (!$class_id || !$jalali_date) {
        echo json_encode(['success' => false, 'message' => 'داده نامعتبر']);
        exit;
    }

    $gregorian_date = jdate_to_gregorian($jalali_date);
    if (!$gregorian_date) {
        echo json_encode(['success' => false, 'message' => 'تاریخ نامعتبر']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT 
                s.id, 
                s.full_name, 
                s.username as student_code,
                COALESCE(h.status, 'not_submitted') as status
            FROM users s
            JOIN class_students cs ON s.id = cs.user_id
            LEFT JOIN homework_status h ON s.id = h.user_id AND h.class_id = ? AND h.homework_date = ?
            WHERE cs.class_id = ? AND s.role = 'student' AND s.is_deleted = 0
            ORDER BY s.full_name
        ");
        $stmt->execute([$class_id, $gregorian_date, $class_id]);
        $students = [];
        $homework = [];
        while ($row = $stmt->fetch()) {
            $students[] = [
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'student_code' => $row['student_code']
            ];
            $homework[$row['id']] = $row['status'];
        }

        echo json_encode(['success' => true, 'students' => $students, 'homework' => $homework]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'خطا']);
        exit;
    }
}

// AJAX: ذخیره ویرایش
if (!empty($input_data['action']) && $input_data['action'] == 'save_edit') {
    require_once '../../includes/db.php';
    header('Content-Type: application/json');
    
    $class_id = (int)($input_data['class_id'] ?? 0);
    $date = $input_data['date'] ?? '';
    $homework = $input_data['homework'] ?? [];

    if (!$class_id || !$date) {
        echo json_encode(['success' => false, 'message' => 'class_id یا تاریخ نامعتبر']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        foreach ($homework as $student_id => $status) {
            $student_id = (int)$student_id;
            if (!$student_id) continue;
            if (!in_array($status, ['complete', 'incomplete', 'not_submitted'])) {
                $status = 'not_submitted';
            }
            $stmt = $pdo->prepare("
                INSERT INTO homework_status (class_id, user_id, homework_date, status)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE status = ?
            ");
            $stmt->execute([$class_id, $student_id, $date, $status, $status]);
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'خطای دیتابیس']);
        exit;
    }
}

// AJAX: حذف تاریخ تکالیف
if (!empty($input_data['action']) && $input_data['action'] == 'delete_homework_date') {
    require_once '../../includes/db.php';
    require_once '../../includes/jdate.php';
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $class_id = (int)($input_data['class_id'] ?? 0);
        $jalali_date = trim($input_data['date'] ?? '');
        
        if (!$class_id || !$jalali_date) {
            throw new Exception('داده نامعتبر');
        }
        
        $gregorian_date = jdate_to_gregorian($jalali_date);
        if (!$gregorian_date) {
            throw new Exception('تاریخ شمسی نامعتبر');
        }
        
        $stmt = $pdo->prepare("DELETE FROM homework_status WHERE class_id = ? AND homework_date = ?");
        $result = $stmt->execute([$class_id, $gregorian_date]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'تاریخ تکالیف با موفقیت حذف شد.']);
        } else {
            throw new Exception('خطا در حذف از دیتابیس');
        }
        exit;
        
    } catch (Exception $e) {
        error_log("Delete Homework Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

$title = 'Assignment';
require_once 'layout.php';
require_once '../../includes/db.php';
require_once '../../includes/jdate.php';

$class_id = (int)($_GET['class_id'] ?? 0);

if (!$class_id) {
    header("Location: homework.php?error=کلاسی انتخاب نشده است.");
    exit;
}

$stmt = $pdo->prepare("SELECT title FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class_title = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.username as student_code
    FROM users u
    JOIN class_students cs ON u.id = cs.user_id
    WHERE cs.class_id = ? AND u.role = 'student' AND u.is_deleted = 0
    ORDER BY u.full_name
");
$stmt->execute([$class_id]);
$students = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT DISTINCT class_date 
    FROM attendance 
    WHERE class_id = ? 
    ORDER BY class_date DESC
");
$stmt->execute([$class_id]);
$attendance_dates = $stmt->fetchAll();

$today = date('Y-m-d');
?>

<link href="../../assets/css/teacher/homework.css?v=<?= time() ?>" rel="stylesheet">

<div class="modal fade" id="editHomeworkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="modal-date-display" class="mb-3"></p>
                <input type="hidden" id="edit-class-date" value="">
                <div class="table-responsive">
                    <table class="table table-bordered homework-table">
                        <thead class="table-light">
                            <tr>
                                <th>Learner's Name</th>
                                <th>ID</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="modal-students-body"></tbody>
                    </table>
                </div>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="delete-homework-btn">حذف تاریخ</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-info" id="save-homework-btn">Save</button>
			</div>
        </div>
    </div>
</div>

<div class="card homework-students-card">
    <div class="card-header text-white">
        <h5>Assignments: <?= htmlspecialchars($class_title) ?></h5>
    </div>
    <div class="card-body">
        
			<div class="mb-4 homework-dates">
				<h6 class="text-info text-white">Dates</h6>
				<?php if (!empty($attendance_dates)): ?>
					<div class="d-flex flex-wrap gap-2">
						<?php foreach ($attendance_dates as $d): ?>
							<button type="button" class="btn btn-sm btn-outline-secondary"
									data-bs-toggle="modal" data-bs-target="#editHomeworkModal"
									onclick="homework_setEditDate('<?= jdate_format($d['class_date'], 'Y/m/d') ?>')">
								<?= jdate_format($d['class_date'], 'Y/m/d') ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<p class="text-muted">No status admitted</p>
				<?php endif; ?>
			</div>

		
		<form method="POST" action="save-homework.php">
			<input type="hidden" name="class_id" value="<?= $class_id ?>">
				<div class="mb-3">
					<label class="form-label">Choose the session</label>
					<select name="homework_date" class="form-select" required>
						<option value="">...??Session</option>
						<?php if (!empty($attendance_dates)): ?>
							<?php foreach ($attendance_dates as $d): ?>
								<option value="<?= jdate_format($d['class_date'], 'Y/m/d') ?>">
									<?= jdate_format($d['class_date'], 'Y/m/d') ?>
								</option>
							<?php endforeach; ?>
						<?php else: ?>
							<option value="">جلسه‌ای برای ثبت تکلیف وجود ندارد</option>
						<?php endif; ?>
					</select>
				</div>
			<?php if (!empty($students)): ?>
				<div class="table-responsive">
					<table class="table table-bordered homework-table">
						<thead class="table-light">
							<tr>
								<th>Learner's Name</th>
								<th>ID</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($students as $s): ?>
							<tr>
								<td><?= htmlspecialchars($s['full_name']) ?></td>
								<td><?= htmlspecialchars($s['student_code']) ?></td>
								<td>
									<div class="d-flex gap-2">
										<button type="button" class="btn btn-homework btn-complete active"
												data-student-id="<?= $s['id'] ?>" 
												data-status="complete"
												onclick="homework_setMainStatus(<?= $s['id'] ?>, 'complete')">
											Completed
										</button>
										<button type="button" class="btn btn-homework btn-incomplete"
												data-student-id="<?= $s['id'] ?>" 
												data-status="incomplete"
												onclick="homework_setMainStatus(<?= $s['id'] ?>, 'incomplete')">
											Incomplete
										</button>
										<button type="button" class="btn btn-homework btn-not-submitted"
												data-student-id="<?= $s['id'] ?>" 
												data-status="not_submitted"
												onclick="homework_setMainStatus(<?= $s['id'] ?>, 'not_submitted')">
											Not presented 
										</button>
									</div>
									<input type="hidden" name="homework[<?= $s['id'] ?>]" value="complete">
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<button type="submit" class="btn homework-action-btn btn-primary mt-3">Confirm</button>
			<?php endif; ?>
			<a href="homework.php" class="btn homework-action-btn btn-secondary mt-3">Back</a>
		</form>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>

<script>

// تنظیم دکمه حذف بعد از باز شدن مودال
function homework_setEditDate(date) {
    document.getElementById('modal-date-display').textContent = 'تاریخ: ' + date;
    document.getElementById('edit-class-date').value = date;
    
    // فعال کردن دکمه حذف
    document.getElementById('delete-homework-btn').onclick = () => deleteHomeworkDate(date);
    
    fetch('?action=get_data&class_id=<?= $class_id ?>&date=' + encodeURIComponent(date))
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            let html = '';
            data.students.forEach(s => {
                const status = data.homework[s.id] || 'not_submitted';
                const completeActive = status === 'complete' ? 'active' : '';
                const incompleteActive = status === 'incomplete' ? 'active' : '';
                const notSubmittedActive = status === 'not_submitted' ? 'active' : '';
                
                html += `
                <tr>
                    <td>${s.full_name}</td>
                    <td>${s.student_code}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-homework btn-complete ${completeActive}"
                                    data-student="${s.id}" data-status="complete"
                                    onclick="homework_updateModalStatus('${s.id}', 'complete')">
                                کامل
                            </button>
                            <button type="button" class="btn btn-homework btn-incomplete ${incompleteActive}"
                                    data-student="${s.id}" data-status="incomplete"
                                    onclick="homework_updateModalStatus('${s.id}', 'incomplete')">
                                ناقص
                            </button>
                            <button type="button" class="btn btn-homework btn-not-submitted ${notSubmittedActive}"
                                    data-student="${s.id}" data-status="not_submitted"
                                    onclick="homework_updateModalStatus('${s.id}', 'not_submitted')">
                                عدم ارائه
                            </button>
                        </div>
                        <input type="hidden" id="homework-status-${s.id}" value="${status}">
                    </td>
                </tr>`;
            });
            document.getElementById('modal-students-body').innerHTML = html;
            document.getElementById('save-homework-btn').onclick = homework_saveEdit;
        }
    });
}

// حذف تاریخ تکالیف
function deleteHomeworkDate(date) {
    if (!confirm(`آیا مطمئن هستید که می‌خواهید تاریخ ${date} را حذف کنید؟\nاین عمل غیرقابل بازگشت است.`)) {
        return;
    }
    
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            action: 'delete_homework_date',
            class_id: <?= $class_id ?>,
            date: date
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editHomeworkModal')).hide();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در حذف تاریخ.');
    });
}


function homework_updateModalStatus(studentId, status) {
   
    document.getElementById('homework-status-' + studentId).value = status;
    
    
    document.querySelectorAll(`#modal-students-body button[data-student="${studentId}"]`).forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`#modal-students-body button[data-student="${studentId}"][data-status="${status}"]`).classList.add('active');
}


function homework_saveEdit() {
    const dateInput = document.getElementById('edit-class-date');
    if (!dateInput || !dateInput.value) {
        alert('تاریخ معتبر نیست.');
        return;
    }
    
    const date = dateInput.value;
    const homework = {};
    
    document.querySelectorAll('#modal-students-body input[id^="homework-status-"]').forEach(input => {
        const studentId = input.id.replace('homework-status-', '');
        homework[studentId] = input.value;
    });

    fetch('?action=save_edit', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'save_edit', class_id: <?= $class_id ?>, date, homework})
    })
    .then(response => {
        console.log('پاسخ سرور:', response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('تغییرات ذخیره شد.');
            bootstrap.Modal.getInstance(document.getElementById('editHomeworkModal')).hide();
        } else {
            alert('خطا: ' + (data.message || 'نا مشخص'));
        }
    })
    .catch(error => {
        console.error('خطا در درخواست:', error);
        alert('خطا در ارتباط با سرور.\nجزئیات در کنسول مرورگر.');
    });
}


document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('[onclick^="setMainStatus"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            const status = this.dataset.status;
            const hiddenInput = document.querySelector(`input[name="homework[${studentId}]"]`);
            hiddenInput.value = status;
            
            
            document.querySelectorAll(`button[data-student-id="${studentId}"]`).forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
});

function homework_setMainStatus(studentId, status) {
 
    const hiddenInput = document.querySelector(`input[name="homework[${studentId}]"]`);
    if (hiddenInput) {
        hiddenInput.value = status;
    }
    
    
    const buttons = document.querySelectorAll(`button[data-student-id="${studentId}"]`);
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });
    

    const targetBtn = document.querySelector(`button[data-student-id="${studentId}"][data-status="${status}"]`);
    if (targetBtn) {
        targetBtn.classList.add('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var datePicker = new MdPersianDateTimePicker('homework_date', {
        targetTextSelector: '#homework_date',
        englishNumber: false,
        dateFormat: 'yyyy/mm/dd'
    });
});

</script>


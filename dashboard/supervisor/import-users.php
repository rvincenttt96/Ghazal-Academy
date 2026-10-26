<?php
$title = 'Import Users from Excel';
require_once 'layout.php';
require_once '../../includes/db.php';
require_once '../../includes/simplexlsx.php';
use Shuchkin\SimpleXLSX;
require_role('supervisor');
?>

<div class="dashboard-header">
    <h1 class="mb-0">Import Users from Excel</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="process-import-users.php" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Select Excel File (.xlsx) *</label>
                    <input type="file" name="excel_file" class="form-control" accept=".xlsx" required>
                    <small class="text-muted">
                        ستون‌های مورد نیاز: full_name, username, role, password<br>
                        مقادیر role: فقط "teacher" یا "student"<br>
                        رمز عبور باید حداقل 6 کاراکتر باشد
                    </small>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Import Users</button>
                </div>
            </div>
        </form>
        
        <div class="mt-3">
            <a href="excel/sample-users-template.xlsx" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-download me-1"></i> Download Sample Template
            </a>
        </div>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>
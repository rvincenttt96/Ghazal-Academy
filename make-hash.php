<?php
if ($_POST['password'] ?? false) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    echo "<div style='direction:ltr; font-family:monospace; background:#f0f0f0; padding:15px; margin:20px;'>";
    echo "رمز عبور: " . htmlspecialchars($_POST['password']) . "<br>";
    echo "هش تولیدشده:<br>";
    echo "<strong>" . $hash . "</strong>";
    echo "</div>";
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تولید هش رمز عبور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    تولید هش رمز عبور
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">رمز عبور مورد نظر</label>
                            <input type="text" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">تولید هش</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
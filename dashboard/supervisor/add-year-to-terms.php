<?php
require_once '../../includes/db.php';

try {
    // بررسی وجود ستون year
    $stmt = $pdo->prepare("SHOW COLUMNS FROM terms LIKE 'year'");
    $stmt->execute();
    $exists = $stmt->fetch();
    
    if (!$exists) {
        // اضافه کردن ستون year
        $stmt = $pdo->prepare("ALTER TABLE terms ADD COLUMN year VARCHAR(4) NOT NULL DEFAULT '1404' AFTER title");
        $stmt->execute();
        
        // آپدیت ترم‌های موجود به سال 1404
        $stmt = $pdo->prepare("UPDATE terms SET year = '1404' WHERE year = '1404' OR year IS NULL OR year = ''");
        $stmt->execute();
        
        echo "✅ ستون 'year' با موفقیت اضافه شد.";
    } else {
        echo "✅ ستون 'year' قبلاً وجود دارد.";
    }
    
} catch (Exception $e) {
    echo "❌ خطا: " . $e->getMessage();
}
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * PHP Backend API for Ghazal Notification Panel
 * File: send-notification.php
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

require_once 'db.php';
require_once 'services.php';

$data = json_decode(file_get_contents('php://input'), true);
$message = trim($data['message'] ?? '');
$destinations = $data['destinations'] ?? [];
$targetClasses = $data['classes'] ?? [];
$targetStudents = $data['students'] ?? [];

if (empty($message)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "متن پیام خالی است."]);
    exit();
}

try {
    $pdo->beginTransaction();

    // ۱. ثبت لاگ اولیه پیام
    $stmtLog = $pdo->prepare("INSERT INTO message_logs (raw_message, destinations, status) VALUES (?, ?, 'pending')");
    $stmtLog->execute([$message, implode(',', $destinations)]);
    $messageLogId = $pdo->lastInsertId();

    // آرایه‌ای برای ذخیره جواب سرورهای ایتا و بله جهت دیباگ
    $apiResponses = [];

    // ۲. منطق ارسال پیامک (فقط اگر sms انتخاب شده باشد)
    if (in_array('sms', $destinations)) {
        foreach ($targetStudents as $stdKey) {
            $stmt = $pdo->prepare("SELECT s.id, s.name as sName, p.name as pName, p.phone FROM students s JOIN parents p ON s.parent_id = p.id WHERE s.student_key = ?");
            $stmt->execute([$stdKey]);
            $rec = $stmt->fetch();

            if ($rec) {
                $smsResult = sendFarazSMS($rec['phone'], "yCT20au05o", ["parent" => $rec['pName'] ?? 'والدین گرامی']);
                $pdo->prepare("INSERT INTO message_recipients (message_log_id, student_id) VALUES (?, ?)")
                    ->execute([$messageLogId, $rec['id']]);
            }
        }
    }

    // ۳. منطق ارسال به کانال رسمی بله
    if (in_array('bale_channel', $destinations)) {
        $baleChannelId = "@ghazallanguageacademy"; 
        $apiResponses['bale'] = sendBaleMessage($baleChannelId, $message);
    }

    // ۴. منطق ارسال به کانال رسمی ایتا
    if (in_array('eitaa_channel', $destinations)) {
        $eitaaChannelId = "10949564"; 
        $apiResponses['eitaa'] = sendEitaaMessage($eitaaChannelId, $message);
    }

    // ۵. منطق ارسال به گروه‌های کلاسی ایتا
    if (in_array('eitaa_groups', $destinations)) {
        foreach ($targetClasses as $clsVal) {
            $groupChatId = "";
            if ($clsVal === "الف") { $groupChatId = "class_a_group_id_in_eitaayar"; } 
            elseif ($clsVal === "ب") { $groupChatId = "class_b_group_id_in_eitaayar"; } 
            elseif ($clsVal === "ج") { $groupChatId = "class_c_group_id_in_eitaayar"; }
            
            if (!empty($groupChatId)) {
                $personalizedMessage = str_replace("{نام_کلاس}", "کلاس " . $clsVal, $message);
                $apiResponses['eitaa_group_' . $clsVal] = sendEitaaMessage($groupChatId, $personalizedMessage);
            }
        }
    }

    $pdo->commit();
    
    // چاپ موفقیت به همراه لاگ سرورهای ایرانی
    echo json_encode([
        "status" => "success", 
        "log_id" => $messageLogId,
        "debug_info" => $apiResponses // اضافه شدن دیباگر
    ]);
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit();
}
<?php
/**
 * PHP Backend API for Ghazal Notification Panel
 * File: services.php
 */

function sendFarazSMS($phone, $patternCode, $variables) {
    $apiKey = "PpgL4mm0OiqyOoDuh0GT6C9pTNI2elZzzrHxXwHrMxQMbNsrD1"; 
    $senderNumber = "90008361"; // خط اختصاصی شما تایید شد
    $url = "https://api.iranpayamak.com/ws/v1/sms/pattern";
    
    $data = [
        "code"          => $patternCode,
        "recipient"     => $phone,
        "attributes"    => $variables,
        "line_number"   => $senderNumber,
        "number_format" => "english" // این رو برگردوندیم چون API فراز گفت اجباریه
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Api-Key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ["http_code" => $httpCode, "response" => json_decode($response, true)];
}

function sendBaleMessage($chatId, $text) {
    // توکنی که از بات‌فادرِ بله گرفتی رو اینجا بین کوتیشن‌ها بذار
    $botToken = "2096578520:EHE65g6Rp64I43I82QrJbvtFgBxIml3b9tc"; 
    
    // آدرس وب‌سرویس رسمی بله
    $url = "https://tapi.bale.ai/bot" . $botToken . "/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $text
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ["http_code" => $httpCode, "response" => json_decode($response, true)];
}
function sendEitaaMessage($chatId, $text) {
    // توکنی که از سایت ایتاار گرفتی رو اینجا قرار بده
    $eitaaToken = "bot497926:c78048c0-7d15-4252-8c44-92bf9199cec6"; 
    
    // آدرس وب‌سرویس رسمی ایتاار
    $url = "https://eitaayar.ir/api/" . $eitaaToken . "/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $text
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ["http_code" => $httpCode, "response" => json_decode($response, true)];
}
?>
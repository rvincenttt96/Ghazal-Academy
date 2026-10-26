<?php
require_once 'jDatetime.class.php';

/**
 * تبدیل اعداد فارسی/عربی به انگلیسی
 */
function convertNumbers($input) {
    $farsi_array = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic_array = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $num_array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    $input = str_replace($farsi_array, $num_array, $input);
    $input = str_replace($arabic_array, $num_array, $input);
    
    return $input;
}

function jdate_format($date, $format = 'Y/m/d') {
    if (empty($date) || $date == '0000-00-00') {
        return '';
    }
    try {
        $jdate = new jDateTime();
        return $jdate->date($format, strtotime($date), true, true);
    } catch (Exception $e) {
        return $date;
    }
}

function jdate_to_gregorian($jdate) {
    if (empty($jdate) || !is_string($jdate)) {
        return date('Y-m-d');
    }
    
    // تبدیل اعداد فارسی/عربی به انگلیسی
    $jdate = convertNumbers($jdate);
    
    // پاک‌سازی فرمت
    $jdate = trim($jdate);
    $jdate = str_replace(['.', '-'], '/', $jdate);
    $parts = explode('/', $jdate);
    
    if (count($parts) !== 3) {
        return false; // ← تغییر: false برگردونه نه تاریخ امروز
    }
    
    $j_year = (int)$parts[0];
    $j_month = (int)$parts[1];
    $j_day = (int)$parts[2];
    
    if ($j_year < 1300 || $j_year > 1500 || $j_month < 1 || $j_month > 12 || $j_day < 1 || $j_day > 31) {
        return false; // ← تغییر
    }
    
    try {
        $jdate_obj = new jDateTime();
        $timestamp = $jdate_obj->mktime(0, 0, 0, $j_month, $j_day, $j_year);
        return date('Y-m-d', $timestamp);
    } catch (Exception $e) {
        return false; // ← تغییر
    }
}
// تابع jdate برای سازگاری با کدهای قبلی
function jdate($format, $timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    
    // اگر timestamp نامعتبر بود
    if ($timestamp <= 0) {
        return 'نامعتبر';
    }
    
    try {
        $jdate_obj = new jDateTime();
        return $jdate_obj->date($format, $timestamp, true, true);
    } catch (Exception $e) {
        return date($format, $timestamp);
    }
}
?>
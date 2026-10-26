<?php
// Simple jDate function for Persian date conversion
function jdate($format, $timestamp = null) {
    if (is_null($timestamp)) {
        $timestamp = time();
    }

    // تبدیل به تاریخ میلادی
    $date = date('Y-m-d H:i:s', $timestamp);
    list($year, $month, $day, $hour, $minute, $second) = explode('-', date('Y-m-d-H-i-s', $timestamp));

    // تبدیل به تاریخ شمسی
    $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    $gy = $year - 1600;
    $gm = $month - 1;
    $gd = $day - 1;

    $g_day_no = 365 * $gy + floor(($gy + 3) / 4) - floor(($gy + 99) / 100) + floor(($gy + 399) / 400);
    for ($i = 0; $i < $gm; ++$i) {
        $g_day_no += $g_days_in_month[$i];
    }
    if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
        $g_day_no++; // سال کبیسه
    }
    $g_day_no += $gd;

    $j_day_no = $g_day_no - 79;
    $j_np = floor($j_day_no / 12053);
    $j_day_no %= 12053;

    $jy = 979 + 33 * $j_np + 4 * floor($j_day_no / 1461);
    $j_day_no %= 1461;

    if ($j_day_no >= 366) {
        $jy += floor(($j_day_no - 1) / 365);
        $j_day_no = ($j_day_no - 1) % 365;
    }

    for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
        $j_day_no -= $j_days_in_month[$i];
    }
    $jm = $i + 1;
    $jd = $j_day_no + 1;

    // فرمت خروجی
    $output = '';
    $format = str_split($format);
    foreach ($format as $char) {
        switch ($char) {
            case 'Y': $output .= $jy; break;
            case 'm': $output .= sprintf('%02d', $jm); break;
            case 'd': $output .= sprintf('%02d', $jd); break;
            case 'H': $output .= sprintf('%02d', $hour); break;
            case 'i': $output .= sprintf('%02d', $minute); break;
            case 's': $output .= sprintf('%02d', $second); break;
            default: $output .= $char; break;
        }
    }
    return $output;
}
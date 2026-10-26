<?php
if (!function_exists('jdate_weekday')) {
    /**
     * jdate_weekday - Returns Persian weekday index (0=Shanbeh, ..., 6=Jom'e)
     * 
     * @param int|null $timestamp Unix timestamp (optional, defaults to now)
     * @return int 0-6 (0 = Saturday, 6 = Friday)
     */
    function jdate_weekday($timestamp = null) {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        // Get Gregorian date parts
        $date_info = getdate($timestamp);
        $g_y = $date_info['year'];
        $g_m = $date_info['mon'];
        $g_d = $date_info['mday'];
        
        // Days per month in Gregorian (non-leap)
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        
        // Check for leap year
        $kab = ($g_m <= 2) ? 0 : (
            (($g_y % 4 == 0) && ($g_y % 100 != 0)) || ($g_y % 400 == 0)
        );
        
        // Day of year (1-366)
        $day_of_year = $g_d + $g_d_m[$g_m - 1] + $kab;
        
        // Weekday in Gregorian (0=Sunday, ..., 6=Saturday)
        $greg_weekday = ($day_of_year % 7);
        
        // Convert to Persian weekday (0=Saturday, ..., 6=Friday)
        $persian_weekday = ($greg_weekday + 1) % 7;
        
        return $persian_weekday;
    }
}
?>
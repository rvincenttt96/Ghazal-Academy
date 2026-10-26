<?php
defined('ABSPATH') || exit ("no access");

$min_loader_version="12.0";
$min_php_version="7.4";
$max_php_version="8.1";
$ioncube_error_checker=[];

if (!extension_loaded('ionCube Loader')){
    $ioncube_error_checker[]=sprintf(' به تشخیص قالب شکرینو، روی هاست شما لودر ioncube نصب نیست. فقط کافیست به شرکت هاست خود اطلاع دهید تا آنرا برای شما درست کند تا نسخه کمتر از %s نباشد',$min_loader_version);
}elseif (!function_exists('ioncube_loader_version') || version_compare(ioncube_loader_version(),$min_loader_version,'<')){
    $ioncube_error_checker[]=sprintf(' به تشخیص قالب شکرینو، روی هاست شما لودر ioncube نصب نیست. فقط کافیست به شرکت هاست خود اطلاع دهید تا آنرا برای شما درست کند تا نسخه کمتر از %s نباشد',$min_loader_version);
}

if(!version_compare(phpversion(),$min_php_version,'>=')) {
    if (version_compare(phpversion(),$max_php_version,'>')) {
        $ioncube_error_checker[] = sprintf(
            'نسخه php هاست شما بالا است و باید حتما روی نسخه %s یا کمتر تنظیم شده باشد. فقط کافیست به شرکت هاست اطلاع دهید تا برای شما آن را درست کنند',
            $max_php_version
        );
    } else {
        $ioncube_error_checker[] = sprintf(
            'نسخه php هاست شما پایین است و باید حتما روی نسخه %s یا بیشتر تنظیم شده باشد. فقط کافیست به شرکت هاست اطلاع دهید تا برای شما آن را درست کنند',
            $min_php_version
        );
    }
}

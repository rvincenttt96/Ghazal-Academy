<?php
defined('ABSPATH') || exit;
require_once 'services.class.php';
class Panelet_SMS {
    public function __construct() {
        add_action('admin_init', [$this, 'merge_mobile_meta_digits']);
    }
    public function merge_mobile_meta_digits() {
        $users = get_users();
        foreach ($users as $user) {
            $digits_phone_no = $this->normalizeMobile(get_user_meta($user->ID, 'digits_phone_no', true));
            $digits_phone = $this->normalizeMobile(get_user_meta($user->ID, 'digits_phone', true));
            if ($digits_phone_no) {
                update_user_meta($user->ID, 'mobile_pnlt', $digits_phone_no);
            } elseif ($digits_phone) {
                update_user_meta($user->ID, 'mobile_pnlt', $digits_phone);
            } else {
                return false;
            }
        }
        return true;
    }
    public function normalizeMobile($mobile) {
        $persianNumerals = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latinNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $mobile = str_replace($persianNumerals, $latinNumerals, $mobile);
        $mobile = preg_replace('/\D/', '', $mobile);
        if (strpos($mobile, '98') === 0) {
            $mobile = substr($mobile, 2);
        }
        if (strpos($mobile, '0') === 0) {
            $mobile = substr($mobile, 1);
        }
        if (strlen($mobile) === 10) {
            return $mobile;
        } else {
            return 'error';
        }
    }
    public function generateRandomNumber() {
        $len =  5; //pnlt_option('otp_len');
        $len = max(4, min(8, $len));
        $minValue = pow(10, $len - 1);
        $maxValue = pow(10, $len) - 1;
        return mt_rand($minValue, $maxValue);
    }
    public function connection() {
        $data = array(
            'panel' => pnlt_option('panel-sms'),
            'user' => pnlt_option('username_sms'),
            'pass' => pnlt_option('password_sms'),
            'sender' => pnlt_option('sender_sms'),
            'api' => pnlt_option('api_sms'),
            'secret' => pnlt_option('secret_sms'),
            'templateId' => pnlt_option('templateid_sms'),
            'pattern' => pnlt_option('pattern_sms'),
            'variable' => str_replace('%', '', pnlt_option('variable_sms')),
        );
        return $data;
    }
    public function sendSmsPattern($to) {
        $conn = self::connection();
        $sms_panel = $conn['panel'];
        if($sms_panel == "farazsms" or $sms_panel == "maxsms" or $sms_panel == "modirpayamak" or $sms_panel == "panelsmspro" or $sms_panel == "rangine") {
            $return = Panelet_SMS_Panels::ippanel_pattern($conn['user'],$conn['pass'],$conn['sender'],$to,$conn['pattern'],$conn['variable']);
        } elseif($sms_panel == "kavehnegar") {
            $return = Panelet_SMS_Panels::kavehnegar_pattern($conn['api'],$to,$conn['pattern']);
        } elseif($sms_panel == "melipayamak"or $sms_panel == "farapayamak") {
            $return = Panelet_SMS_Panels::melipayamak_pattern($conn['user'],$conn['pass'],$to,$conn['pattern']);
        } elseif($sms_panel == "smsir") {
            $return = Panelet_SMS_Panels::smsir_pattern($conn['api'],$conn['secret'],$to,$conn['templateId'],$conn['pattern']);
        } else {
            $return = "پنل پیامکی انتخابی شما پشتیبانی نمیشود!";
        }
        return $return;
    }
}
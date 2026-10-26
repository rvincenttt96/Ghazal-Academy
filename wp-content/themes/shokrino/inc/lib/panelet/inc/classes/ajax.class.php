<?php
defined('ABSPATH') || exit;
class Panelet_AJAX {
    private $secretKey;
    public function __construct() {
        $this->secretKey = $this->generateSecretKey();
        add_action('wp_ajax_nopriv_ajax_login_register_form_pnlt', [$this, 'ajax_login_register']);
        add_action('wp_ajax_login_register_form_pnlt', [$this, 'ajax_login_register']);
        add_action('wp_ajax_nopriv_save_user_info_shkrn', [$this, 'ajax_save_info']);
        add_action('wp_ajax_save_user_info_shkrn', [$this, 'ajax_save_info']);
    }
    public function ajax_save_info() {
        if (!session_id()) {
            session_start();
        }
        $data = [];
        $message = "";
        $error = "";
        $status = "";

        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'pnlt-ajax-nonce')) {
            $user_id = get_current_user_id();
            if (!empty($_POST['first_name']) && !empty($_POST['last_name'])) {
                if (is_user_logged_in()) {
                    update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
                    update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
                    update_user_meta($user_id, 'filled-user-info-byno', "infofilled");
                    $current_user = wp_get_current_user();
                    $user_displayname = $current_user->display_name;
                    if ($user_displayname !== $_POST['first_name'].' '.$_POST['last_name']) {
                        wp_update_user( array( 'ID' => $user_id, 'display_name' => $_POST['first_name'].' '.$_POST['last_name'] ) );
                    }
                    $message = __('Your name updated successfully','shokrino');
                    $status = "success";
                } else {
                    $error = __('User not defined!','shokrino');
                }
            } else {
                $error = __('Fields are empty! Please fill them','shokrino');
            }
        } else {
            $error = __('Form inputs are not valid, reloading page...','shokrino');
            $data['action'] = "reload";
        }

        if (empty($status)) {
            $status = "failure";
            if (empty($error)) {
                $$error = __('Unknown Error!','shokrino');
            }
        }

        $response = [
            'success' => $status,
            'message' => $message,
            'error' => $error,
            'data' => $data
        ];
        wp_send_json($response);
        die();
    }
    public function ajax_login_register() {
        if (!session_id()) {
            session_start();
        }
        $data = [];
        $message = "";
        $error = "";
        $status = "";
        if(isset($_POST['woocommerce-login-nonce']) && wp_verify_nonce($_POST['woocommerce-login-nonce'], 'woocommerce-login') ) {
            if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'pnlt-ajax-nonce')) {
                $phone = sanitize_text_field(Panelet_SMS::normalizeMobile($_POST['mobile']));
                $step = !empty($_POST['step-login-pnlt']) ? sanitize_text_field($_POST['step-login-pnlt']) : 1;
                $step_verifiy = !empty($_POST['step-validate-pnlt']) ? sanitize_text_field($_POST['step-validate-pnlt']) : "@Bl!aN;;k#5$";
                $form_place = !empty($_POST['login-place-pnlt']) ? sanitize_text_field($_POST['login-place-pnlt']) : "panel";
                $sms_time = PNLT_SMSTIME;
                if ($phone == "error") {
                    $error = esc_html('شماره موبایل معتبر نیست', 'panelet');;
                } else {
                    if ($step == 1 && $step_verifiy == "@Bl!aN;;k#5$") {
                        $generatedcode = Panelet_SMS::sendSmsPattern($phone);
                        if ($generatedcode != false) {
                            $code = $this->encrypt($generatedcode);
                            $_SESSION['otpcode'] = $code;
                            $_SESSION['otpmobile'] = $phone;
                            $_SESSION['otptime'] = time();
                            $_SESSION['otpstatus'] = "sent";
                            $data['otp_status'] = "sent";
                            $data['step'] = 2;
                            $status = "success";
                            $message = "کد تایید پیامک شد...";
                        } else {
                            $error = "اشکال در سیستم پیامکی...";
                        }
                    } elseif ($step == 2 && $step_verifiy == "!@D53O%0" && $_SESSION['otpstatus'] == "sent") {
                        $otp = sanitize_text_field(esc_attr($_POST['otppnlt']));
                        $currect_otp = $_SESSION['otpcode'];
                        $time = esc_attr($_SESSION['otptime']);
                        $nowtime = time();
                        $mobile = $_SESSION['otpmobile'];
                        $nowmobile = Panelet_SMS::normalizeMobile(sanitize_text_field($_POST['mobile']));
                        if (!empty($otp)) {
                            if (hash_equals($currect_otp, $this->encrypt($otp))) {
                                if ($nowtime - $time < $sms_time) {
                                    if ($mobile == $nowmobile) {
                                        if (Panelet_USER::isMobileUnique($phone)) {
                                            // register panelet
                                            $default_username = Panelet_USER::generate_username($mobile);
                                            $default_password = wp_generate_password();
                                            $default_email = "";
                                            $user_id = wp_create_user($default_username, $default_password, $default_email);
                                            if (is_wp_error($user_id)) {
                                                $error = "خطا در هنگام ثبت نام کاربر! لطفا به مدیر سایت اطلاع دهید";
                                                $this->clearSession();
                                            } else {
                                                $last_login = current_time('Y-m-d H:i:s');
                                                update_user_meta($user_id, 'last_login', $last_login);
                                                update_user_meta($user_id, 'mobile_pnlt', $mobile);
                                                update_user_meta($user_id, 'billing_phone', "0" . $mobile);
                                                wp_set_auth_cookie($user_id, true);
                                                $_SESSION['last_login'] = $last_login;
                                                $_SESSION['user_id'] = $user_id;
                                                $this->clearSession();
                                                $status = 'success';
                                                if ($form_place == "checkout") {
                                                    $message = "ثبت نام انجام شد. بعد از بارگزاری مجدد فرم را تکمیل کنید";
                                                } else {
                                                    $message = "با موفقیت ثبت نام شدید! درحال ورود...";
                                                }
                                                $data['otp_status'] = "reload";
                                            }
                                        } else {
                                            // login panelet
                                            $args = array(
                                                'meta_key' => 'mobile_pnlt',
                                                'meta_value' => $phone,
                                            );
                                            $users = get_users($args);
                                            $userInfo = $users[0];
                                            $user_id = $userInfo->ID;
                                            if (user_can($userInfo->ID, 'manage_options')) {
                                                $error = "برای امنیت بیشتر ادمین ها از این بخش نباید وارد شوند! (لطفا به صفحه ورود ادمین ها مراجعه کنید)";
                                            } else {
                                                $last_login = current_time('Y-m-d H:i:s');
                                                update_user_meta($user_id, 'last_login', $last_login);
                                                wp_set_auth_cookie($user_id, true);
                                                $_SESSION['last_login'] = $last_login;
                                                $_SESSION['user_id'] = $user_id;
                                                $this->clearSession();
                                                $status = 'success';
                                                if ($form_place == "checkout") {
                                                    $message = "وارد شدید. بعد از بارگزاری مجدد فرم را تکمیل کنید";
                                                } else {
                                                    $message = "با موفقیت وارد شدید! درحال انتقال...";
                                                }
                                                $data['otp_status'] = "reload";
                                            }
                                        }
                                    } else {
                                        $error = "اطلاعات فرم صحیح نیست";
                                    }
                                } else {
                                    $error = "زمان ثبت کد به پایان رسید! لطفا مجددا تلاش کنید";
                                }
                            } else {
                                $error = "کد وارد شده صحیح نیست";
                            }
                        } else {
                            $error = "کد تاییدی وارد نکرده اید!";
                        }
                    } else {
                        $error = "اطلاعات فرم صحیح نیست. درحال بارگزاری مجدد...";
                        $data['action'] = "reload";
                    }
                }
            } else {
                $error = "اطلاعات فرم صحیح نیست. درحال بارگزاری مجدد...";
                $data['action'] = "reload";
            }
        } else {
            $error = "اطلاعات فرم صحیح نیست. درحال بارگزاری مجدد...";
            $data['action'] = "reload";
        }

        if (empty($status)) {
            $status = "failure";
            if (empty($error)) {
                $error = "خطای نامشخص!";
            }
        }

        $response = [
            'success' => $status,
            'message' => $message,
            'error' => $error,
            'data' => $data
        ];
        wp_send_json($response);
        die();
    }
    private function clearSession() {
        unset($_SESSION['otpcode']);
        unset($_SESSION['otpmobile']);
        unset($_SESSION['otptime']);
        unset($_SESSION['otp_status']);
    }
    private function generateSecretKey() {
        return "df21500c//56f5@a2-c8e1+df6be#+#ecef7abacd19722f5*99d09462-508f973d7*2e59db7d3794";
    }
    private function encrypt($code) {
        return hash_hmac('sha256', $code, $this->secretKey);
    }
}
new Panelet_AJAX;
<?php
defined('ABSPATH') || exit;
$show_form_lost = true;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["reset_password"])) {
        $user_login = sanitize_text_field($_POST["user_login"]);
        $user = get_user_by('email', $user_login);
        if ($user) {
            $reset_key = get_password_reset_key($user);
            if (!is_wp_error($reset_key)) {
                $reset_link = esc_url(add_query_arg(array('action' => 'rp', 'key' => $reset_key, 'login' => rawurlencode($user->user_login)), wp_login_url()));
                $subject = 'بازیابی رمزعبور';
                $message = "سلام,\n\n";
                $message .= "برای تغییر رمز خود روی لینک زیر کلیک کنید:\n";
                $message .= $reset_link . "\n\n";
                $message .= "اگر شما درخواست بازیابی رمز ارسال نکرده اید. این پیام را نادیده بگیرید.\n";
                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($user_login, $subject, $message, $headers);
                echo '<div class="success-text"style="
    display: flex;">';
                echo "ایمیل خود را برای دریافت لینک تعیین رمز جدید بررسی کنید.";
                echo '</div>';
$show_form_lost = false;
            } else {
                echo '<div class="error-text"style="
    display: flex;">';
                echo "حین تعیین رمز عبور خطایی رخ داده است.";
                echo '</div>';
            }
        } else {
            echo '<div class="error-text"style="
    display: flex;">';
            echo "کاربری با این ایمیل یافت نشد";
    		echo '</div>';
        }
    }
}

if ($show_form_lost) {
    do_action('woocommerce_before_lost_password_form');
    ?>
    <form method="post" class="woocommerce-ResetPassword lost_reset_password">
        <p><?php echo apply_filters('woocommerce_lost_password_message', esc_html__('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce')); ?></p>
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <label for="user_login"><?php esc_html_e('Username or email', 'woocommerce'); ?></label>
            <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required />
        </p>
        <div class="clear"></div>
        <?php do_action('woocommerce_lostpassword_form'); ?>
        <p class="woocommerce-form-row form-row">
            <input type="hidden" name="reset_password" value="true" />
            <button type="submit" class="woocommerce-Button button"><?php esc_html_e('Reset password', 'woocommerce'); ?></button>
        </p>
        <?php wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce'); ?>
    </form>
    <?php
    do_action('woocommerce_after_lost_password_form');
}
?>

<?php
defined('ABSPATH') || exit; ?>
<div class="pnlt-loading-popup">
    <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
        <path fill="currentColor" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
            <animateTransform
                    attributeName="transform"
                    attributeType="XML"
                    type="rotate"
                    dur="1s"
                    from="0 50 50"
                    to="360 50 50"
                    repeatCount="indefinite" />
        </path>
    </svg>
</div>
<div class="error-text"></div>
<div class="success-text"></div>
<div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-mobile-pnlt" id="pnlt-login">
    <label for="mobile"><?php esc_attr_e( 'شماره موبایل', 'panelet' ); ?> <span class="required">*</span></label>
    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="mobile" id="username" value="<?php echo ( ! empty( $_POST['mobile'] ) ) ? esc_attr( wp_unslash( $_POST['mobile'] ) ) : ''; ?>" required/>
</div>
<div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-box-pnlt otp-pnlt">
    <label for="otppnlt" class="otp-title-pnlt"><?php esc_attr_e( 'کد تایید', 'panelet' ); ?></label>
    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="otppnlt" id="otppnlt"/>
    <div class="box-timer-otp-pnlt">
        <div id="countdown-pnlt" class="timer-otp-pnlt">
            <div id='countdown-number-pnlt'></div>
            <svg id='countdown-svg-pnlt' width='30' height='30'>
                <circle id='countdown-circle-pnlt' r='12' cx='15' cy='15'></circle>
                <path id='countdown-cross-pnlt' d='M8 8 L22 22 M8 22 L22 8' stroke='red' stroke-width='2' fill='none' />
            </svg>
        </div>
    </div>
</div>
<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
<input type="hidden" class="step-login-pnlt" name="step-login-pnlt" value="1">
<input type="hidden" class="step-validate-pnlt" name="step-validate-pnlt" value="@Bl!aN;;k#5$">
<input type="hidden" class="login-place-pnlt" name="login-place-pnlt" value="<?php echo is_checkout() ? 'checkout' : 'panel'; ?>">
<input name="rememberme" type="hidden" id="rememberme" value="forever" />
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-btn-pnlt">
    <button id="btn-submit-pnlt" class="submit-btn-pnlt submit-btn woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" type="submit" name="login" value="<?php esc_attr_e( 'ثبت نام', 'panelet' ); ?>">
        <span class="text-submit">
            <?php esc_html_e( 'ورود یا ثبت نام', 'panelet' ); ?>
        </span>
    </button>
</p>
<?php
Panelet_CORE::load_form_scripts();

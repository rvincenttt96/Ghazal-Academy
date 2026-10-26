(function($){
    $(document).ready(function (){
        $('.dig_logof_reg_resend, .dig_logof_log_resend, .dig_wc_login_resendو .digits-form_resend_otp').after('<div class="dig_resendotp dig_logof_reg_resend dig_logof_log_resend dig_wc_login_resend dig_lo_resend_votp_btn" dis="1">ارسال مجدد رمز عبور به صورت صوتی</div>');
        var votp_update_time_button;

        var vot_presendTime = 59;

        $(document).on("click", ".dig_lo_resend_votp_btn", function () {
            var dbbtn = $(this);
            if (!$(this).hasClass("dig_resendotp_disabled")) {
                var loader = $(".dig_load_overlay");
                loader.show();
                $.ajax({
                    type: 'post',
                    url: dig_log_obj.ajax_url,
                    data: {
                        action: 'digits_resendotp_voice',
                        countrycode: dbbtn.attr("countrycode"),
                        mobileNo: dbbtn.attr("mob"),
                        csrf: dbbtn.attr("csrf"),
                        login: dbbtn.attr("dtype"),
                        whatsapp: 0,
                        voice: true
                    },
                    success: function (res) {
                        res = res.trim();
                        loader.hide();
                        if (res == 0) {
                            showDigErrorMessage(dig_log_obj.pleasetryagain);
                        } else if (res == -99) {
                            showDigErrorMessage(dig_log_obj.invalidcountrycode);
                        } else {
                            dbbtn.attr("dis", 1).addClass("dig_resendotp_disabled").show().text('کد صوتی ارسال شد');
                        }
                    }
                });
            }
        });
    });
})(jQuery);
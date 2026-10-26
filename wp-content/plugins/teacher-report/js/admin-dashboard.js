jQuery(document).ready(function ($) {
    $("#admin_student_id").on("change", function () {
        let student_id = $(this).val();
        if (student_id.length < 3) return; // جلوگیری از درخواست‌های غیرضروری

        $.ajax({
            type: "POST",
            url: reportcard_ajax.ajax_url,
            data: {
                action: "check_student_id",
                student_id: student_id,
            },
            beforeSend: function () {
                $("#student_error").text("🔄 در حال بررسی...").css("color", "#ffc107").show();
            },
            success: function (response) {
                if (response.success) {
                    $("#student_error").text("✅ کد دانش‌آموز معتبر است.").css("color", "green").show();
                } else {
                    $("#student_error").text("❌ کد دانش‌آموز یافت نشد!").css("color", "red").show();
                }
            },
            error: function () {
                $("#student_error").text("❌ خطایی در ارتباط با سرور رخ داده است!").css("color", "red").show();
            },
        });
    });
});

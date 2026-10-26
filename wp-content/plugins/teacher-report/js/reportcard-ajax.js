jQuery(document).ready(function ($) {
    $("#student_id").on("blur", function () {
        let student_id = $(this).val();
        if (student_id.length < 3) return;

        $.ajax({
            type: "POST",
            url: reportcard_ajax.ajax_url,
            data: {
                action: "check_student_id",
                student_id: student_id,
            },
            beforeSend: function () {
                $("#student_id_status").text("🔄 در حال بررسی...").css("color", "#ffc107").show();
            },
            success: function (response) {
                if (response.success) {
                    $("#student_id_status").text("✅ کد دانش‌آموز معتبر است.").css("color", "green").show();
                    $("#student_name").val(response.name); // 👈 پر کردن فیلد نام
                } else {
                    $("#student_id_status").text("❌ کد دانش‌آموز یافت نشد!").css("color", "red").show();
                    $("#student_name").val(""); // 👈 پاک کردن فیلد نام
                }
            },
            error: function () {
                $("#student_id_status").text("❌ خطایی در ارتباط با سرور رخ داده است!").css("color", "red").show();
                $("#student_name").val(""); // 👈 پاک کردن فیلد نام
            },
        });
    });
});

// ثبت فرم کارنامه با AJAX
$('#reportcard-form').on('submit', function (e) {
    e.preventDefault();

    var form = $(this);
    var formData = form.serialize(); // تمام فیلدها + nonce
    formData += '&action=submit_reportcard';

    var messageBox = $('#form-message');
    messageBox.html('در حال ارسال...').css('color', 'blue');

    // ذخیره مقادیر قبل از reset
    const student_id = $("#student_id").val();
    const student_name = $("#student_name").val();
    const student_level = $("#student_level").val();
    const teacher_name = $("#teacher_name").val();
    const absence = parseInt($("#absence").val()) || 0;
    const activity = parseInt($("#activity").val()) || 0;
    const midterm = parseInt($("#midterm").val()) || 0;
    const final = parseInt($("#final").val()) || 0;
    const total = absence + activity + midterm + final;

    $.post(reportcard_ajax.ajax_url, formData, function (response) {
        messageBox
            .hide()
            .html(response.data.message)
            .fadeIn(300)
            .delay(3000)
            .fadeOut(300);

        if (response.success) {
            // فرم رو پاک کن
            form.trigger('reset');

            // ردیف جدید رو اضافه کن
            const newRow = `
                <tr>
                    <td>${student_id}</td>
                    <td>${student_name}</td>
                    <td>${student_level}</td>
                    <td>${teacher_name}</td>
                    <td>${absence}</td>
                    <td>${activity}</td>
                    <td>${midterm}</td>
                    <td>${final}</td>
                    <td>${total}</td>
                    <td>${new Date().toLocaleDateString('fa-IR')}</td>
                </tr>
            `;
            $(".teacher-report-table tbody").prepend(newRow);
        }
    });
});
//حذف کارنامه
    $('.delete-teacher-reportcard').click(function(e) {
        e.preventDefault();
        const $row = $(this).closest('tr');
        const reportId = $(this).data('id');
        if (!confirm("آیا از حذف این کارنامه توسط معلم مطمئن هستید؟")) return;

        $.ajax({
            url: reportcard_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_teacher_reportcard_ajax',
                report_id: reportId
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() { $(this).remove(); });
                } else {
                    alert("خطا در حذف کارنامه توسط معلم");
                }
            }
        });
    });

	// باز کردن فرم ویرایش به صورت پاپ‌آپ
jQuery(document).ready(function($) {

    // باز کردن فرم ویرایش
    $(document).on('click', '.edit-teacher-reportcard', function(e) {
        e.preventDefault();
        const $btn = $(this);
        $('#edit-id').val($btn.data('id'));
        $('#edit-absence').val($btn.data('absence'));
        $('#edit-activity').val($btn.data('activity'));
        $('#edit-midterm').val($btn.data('midterm'));
        $('#edit-final').val($btn.data('final'));
        $('#Popup').fadeIn();
    });

    // بستن پاپ‌آپ
    $('#cancel-edit, #closePopup').on('click', function() {
        $('#Popup').fadeOut();
    });

    // ذخیره ویرایش
    $('#submit-edit').on('click', function() {
        const data = {
            action: 'update_teacher_reportcard',
            id: $('#edit-id').val(),
            absence: $('#edit-absence').val(),
            activity: $('#edit-activity').val(),
            midterm: $('#edit-midterm').val(),
            final: $('#edit-final').val(),
        };

        $.post(reportcard_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('✅ نمرات با موفقیت ویرایش شدند.');
                location.reload();
            } else {
                alert('❌ خطا در ویرایش نمرات.');
            }
        });
    });

});

jQuery(document).ready(function($) {
    // حذف کارنامه با AJAX
    $('.delete-reportcard').click(function(e) {
        e.preventDefault();
        const $row = $(this).closest('tr');
        const reportId = $(this).data('id');
        if (!confirm("آیا از حذف کارنامه مطمئن هستین؟")) return;

        $.ajax({
            url: supervisor_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_reportcard_ajax',
                report_id: reportId
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() { $(this).remove(); });
                } else {
                    alert("خطا در حذف کارنامه");
                }
            }
        });
    });


    // برای ویرایش
    $('.edit-btn').on('click', function(e) {
        e.preventDefault();
        const reportId = $(this).data('id');

        $('#Popup').css('display', 'flex');
        $('#popupFormContainer').html('در حال بارگذاری...');

        $.ajax({
            url: supervisor_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_edit_form_ajax',
                report_id: reportId
            },
            success: function(response) {
                if (response.success) {
                    $('#popupFormContainer').html(response.data);
                } else {
                    $('#popupFormContainer').html('❌ خطا در بارگذاری فرم ویرایش.');
                }
            }
        });
    });

    // برای افزودن
    $('#addReportBtn').on('click', function(e) {
        e.preventDefault();

        $('#Popup').css('display', 'flex');
        $('#popupFormContainer').html('در حال بارگذاری...');

        $.ajax({
            url: supervisor_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_add_form_ajax'
            },
            success: function(response) {
                if (response.success) {
                    $('#popupFormContainer').html(response.data);
                } else {
                    $('#popupFormContainer').html('❌ خطا در بارگذاری فرم افزودن.');
                }
            }
        });
    });

    // بستن پاپ‌آپ
    $('#closePopup').on('click', function() {
        $('#Popup').hide();
    });

    // سابمیت فرم ویرایش
    $(document).on('submit', '#editReportForm', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post(supervisor_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                alert('✅ ویرایش با موفقیت انجام شد.');
                location.reload();
            } else {
                alert('❌ خطا در ویرایش.');
            }
        });
    });

    // سابمیت فرم افزودن
    $(document).on('submit', '#addReportForm', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post(supervisor_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                alert('✅ کارنامه با موفقیت ثبت شد.');
                location.reload();
            } else {
                alert('❌ خطا در افزودن.');
            }
        });
    });

});

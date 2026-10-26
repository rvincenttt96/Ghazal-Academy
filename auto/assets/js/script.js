(function ($) {
    $(document).ready(function () {
        function showAlert(message, type) {
            var icon = '';
            var bg = '';
            if (type === 'warning') {
                icon = 'exclamation-triangle-fill';
                bg = 'alert-warning';
            }
            if (type === 'warning') {
                icon = 'exclamation-triangle-fill';
                bg = 'alert-warning';
            } else if (type === 'success') {
                icon = 'check-circle-fill';
                bg = 'alert-success';
            } else if (type === 'error') {
                icon = 'exclamation-triangle-fill';
                bg = 'alert-danger';
                /* do something */
            }
            var svg = '<svg class="bi flex-shrink-0 " width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#' + icon + '"/></svg>';
            var alertDiv = document.createElement('div');
            alertDiv.classList.add('alert', bg, 'd-flex');
            alertDiv.innerHTML = svg + '<div class="me-2"> ' + message + ' </div>';

            document.querySelector('.myalert').appendChild(alertDiv);

            setTimeout(function () {
                alertDiv.remove();
            }, 10000);
        }
        $('#submitBtn').click(function () {
            var userDomain = $('#domain').val();

            var submitButton = $('#submitBtn');
            var spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            submitButton.html('در حال بررسی' + spinner);


            var requestData = {
                domain: userDomain
            };

            $.ajax({
                type: "POST",
                url: "inc/validator/domainValidator.php",
                data: requestData,
                success: function (response) {
                    submitButton.html('بررسی خرید قالب');
                    switch (response.status) {
                        case "success":
                            showAlert('دامنه شما با موفقیت وریفای شد. در حال انتقال به مرحله بعد ...', 'success');
                            setTimeout(function () {
                                window.location.href = './steps/chooseTheme.php';
                            }, 1000);
                            break;
                        case "error":
                            showAlert('برای دامنه شما هیچ قالب معتبری یافت نشد. در صورتی که از خرید خود اطمینان دارید با پشتیبانی در ارتباط باشید', 'error');
                            break;
                        default:
                            showAlert('دامنه شما معتبر نیست', 'error');
                    }
                },
                error: function (res) {
                    console.log(res)
                    if (res.responseJSON && res.responseJSON.message){
                        showAlert(res.responseJSON.message, 'error');
                    }else if (res.responseJSON && res.responseJSON.data.message){
                        showAlert(res.responseJSON.data.message, 'error');
                    }else{
                        showAlert('ارسال درخواست شما با خطا مواجه شد.', 'error');
                    }
                    submitButton.html('بررسی خرید قالب');

                }
            });
        });

        var allow_install = false;
        function allowInstall() {
            if (allow_install) {
                showAlert('شما مجاز به نصب قالب هستید. برای ادامه روی دکمه شروع نصب کلیک کنید', 'success');
                $("#submitDb").remove();

                var newSubmitButton = $("<button>")
                    .addClass("btn btn-primary orange")
                    .text("شروع نصب")
                    .attr("type", "submit");

                $("form .form__footer .form__btns").append(newSubmitButton);
                $("form .form__footer .form__btns").parents('form').submit();
            }
        }

        var install_dir = window.location.pathname.split("/auto/steps/getDatabase.php")[0];
        $('#submitDb').click(function () {
            var dbName = $('#name').val();
            var dbUserName = $('#username').val();
            var dbPassword = $('#password').val();

            var submitButton = $('#submitDb');
            var spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            submitButton.html('در حال اتصال' + spinner);
            submitButton.prop('disabled', true);


            var requestData = {
                dbName: dbName,
                dbUserName: dbUserName,
                dbPassword: dbPassword
            };

            $.ajax({
                type: "POST",
                url: "../inc/validator/databaseValidator.php",
                data: requestData,
                success: function (response) {

                    switch (response) {
                        case "success":
                            showAlert('اتصال به دیتابیس با موفقیت انجام شد.', 'success');
                            allow_install = true;
                            console.log('validate database')
                            validateWordpress(submitButton);
                            break;

                        case "error":
                            submitButton.html('تلاش مجدد');
                            submitButton.prop('disabled', false);
                            showAlert('برای دامنه شما هیچ قالب معتبری یافت نشد. در صورتی که از خرید خود اطمینان دارید با پشتیبانی در ارتباط باشید', 'error');
                            break;
                        default:
                            submitButton.html('تلاش مجدد');
                            submitButton.prop('disabled', false);
                            showAlert('اطلاعات معتبر نیست', 'error');
                    }
                },
                error: function () {
                    showAlert('ارسال درخواست شما با خطا مواجه شد.', 'error');
                }
            });






        });

        function validateWordpress(submitButton){
            $.ajax({
                type: "POST",
                url: "../inc/validator/wordpressValidator.php",
                success: function (response) {
                    switch (response) {
                        case "success":
                            submitButton.html('ارسال اطلاعات');
                            console.log('allow install');
                            allowInstall()
                            break;
                        case "wordpress Detected!":
                            allow_install = false;
                            var alertMessage = `به نظر می‌رسد یک نسخه از وردپرس را در مسیر ${install_dir} نصب دارید. فایل‌های موجود در این مسیر را حذف کنید تا مجاز به نصب قالب باشید`;
                            showAlert(alertMessage, 'error');
                            submitButton.html('تلاش مجدد');
                            submitButton.prop('disabled', false);
                            break;
                        default:
                            submitButton.html('تلاش مجدد');
                            submitButton.prop('disabled', false);
                            showAlert('اطلاعات معتبر نیست', 'error');
                    }
                },
                error: function () {
                    showAlert('ارسال درخواست شما با خطا مواجه شد.', 'error');
                    submitButton.prop('disabled', false);
                }


            });
        }

        function setSelectedThemeId() {
            var selectedThemeId;

            var radioButtons = document.getElementsByName("theme");
            for (var i = 0; i < radioButtons.length; i++) {
                if (radioButtons[i].checked) {
                    selectedThemeId = radioButtons[i].id.replace("theme", "");
                    break;
                }
            }

            document.getElementById("selectedThemeId").value = selectedThemeId;

            var submitButton = document.querySelector("button[type='submit']");
            submitButton.disabled = selectedThemeId === undefined;
            if (selectedThemeId !== undefined) {
                submitButton.click();
            }
        }

        var radioButtons = document.getElementsByName("theme");
        for (var i = 0; i < radioButtons.length; i++) {
            radioButtons[i].addEventListener("change", setSelectedThemeId);
        }


        function setSelectedDemoId() {
            var selectedDemoId;


            var radioButtons = document.getElementsByName("Demo");
            for (var i = 0; i < radioButtons.length; i++) {
                if (radioButtons[i].checked) {
                    selectedDemoId = radioButtons[i].id.replace("Demo", "");
                    break;
                }
            }

            document.getElementById("selectedDemoId").value = selectedDemoId;

            var submitButton = document.querySelector("button[type='submit']");
            submitButton.disabled = selectedDemoId === undefined;
            if (selectedDemoId !== undefined) {
                submitButton.click();
            }
        }
        var radioButtons = document.getElementsByName("Demo");
        for (var i = 0; i < radioButtons.length; i++) {
            radioButtons[i].addEventListener("change", setSelectedDemoId);
        }



        $('#submitAdmin').click(function () {

            var newName = $('#name').val();
            var newUserName = $('#username').val();
            var newPassword = $('#password').val();
            var newEmail = $('#email').val();

            var dbName = $('#dbname').val();
            var dbUserName = $('#dbusername').val();
            var dbPassword = $('#dbpassword').val();
            var dbprefix = $('#dbprefix').val();


            var submitButton = $('#submitAdmin');
            var spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';


            submitButton.html('در حال ساخت اکانت' + spinner);
            submitButton.prop('disabled', true);


            var requestData = {
                newName: newName,
                newUserName: newUserName,
                newPassword: newPassword,
                newEmail: newEmail,
                dbname: dbName,
                dbusername: dbUserName,
                dbpassword: dbPassword,
                dbprefix: dbprefix
            };

            var install_dir = window.location.pathname.split("/auto/steps/createAdmin.php")[0];


            $.ajax({
                type: "POST",
                url: "../inc/validator/createAdmin.php",
                data: requestData,
                success: function (response) {
                    switch (response) {
                        case "success":
                            showAlert('ساخت اکانت با موفقیت انجام شد. سایت شما حاضره :)', 'success');

                            var duration = 500;
                            var end = Date.now() + duration;

                            (function frame() {
                                confetti({
                                    particleCount: 25,
                                    spread: 120,
                                    origin: {
                                        y: 0.6
                                    }
                                });

                                if (Date.now() < end) {
                                    requestAnimationFrame(frame);
                                }
                            }());

                            $("#submitAdmin").parents('form').slideUp()
                            $("#admin-url").slideDown()
                            break;

                        case "error":
                            submitButton.html('تلاش مجدد');
                            submitButton.prop('disabled', false);
                            showAlert('اتصال به دیتابیس انجام نشد!', 'error');
                            break;
                        default:
                            submitButton.html('تلاش مجدد');
                            submitButton.prop('disabled', false);
                            showAlert('اطلاعات معتبر نیست', 'error');

                    }
                },
                error: function () {
                    showAlert('ارسال درخواست شما با خطا مواجه شد.', 'error');
                    submitButton.prop('disabled', false);
                }
            });

        });

    });

})(jQuery);
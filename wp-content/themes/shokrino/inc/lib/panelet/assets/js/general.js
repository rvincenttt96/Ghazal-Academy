let loginForm = document.querySelector('.login');
let errorText = loginForm.querySelector('.error-text');
let successText = loginForm.querySelector('.success-text');
document.addEventListener('DOMContentLoaded', function() {
    let submitButton = loginForm.querySelector(".submit-btn");
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        loginForm.classList.add('loading');
        let formData = new FormData(loginForm);
        formData.append('action', 'ajax_login_register_form_pnlt');
        formData.append('nonce', login_script_data_panelet.nonce);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', login_script_data_panelet.ajax_url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            errorText.style.display = 'none';
            successText.style.display = 'none';
            if (xhr.status === 200) {
                loginForm.classList.remove('loading');
                let response = JSON.parse(xhr.responseText);
                errorText.style.display = 'none';
                successText.style.display = 'none';
                if (response.success === 'success') {
                    if(response.data.otp_status == "sent") {
                        startCountdownPnlt();
                        successText.textContent = response.message;
                        successText.style.display = 'block';
                        submitButton.querySelector('.text-submit').innerHTML = ('ثبت کد و ورود');
                        loginForm.querySelector(".step-login-pnlt").value = response.data.step;
                        loginForm.querySelector(".step-validate-pnlt").value = "!@D53O%0";
                        loginForm.querySelector(".otp-pnlt").style.display = "block";
                        loginForm.querySelector(".otp-mobile-pnlt").style.display = "none";
                        document.getElementById("otppnlt").focus();
                    } else if(response.data.otp_status == "reload") {
                        loginForm.classList.add('loading');
                        location.reload();
                    }
                } else if(response.data.action == "reload") {
                    errorText.textContent = response.error;
                    errorText.style.display = 'block';
                    loginForm.classList.add('loading');
                    location.reload();
                } else {
                    errorText.textContent = response.error;
                    errorText.style.display = 'block';
                }
            } else {
                errorText.style.display = 'block';
                errorText.textContent = "Error";
            }
        };
        xhr.onerror = function() {};
        xhr.send(formData);
    });
    document.querySelector('.step-login-pnlt').value = '';
    document.querySelector('.step-validate-pnlt').value = '';
    if (window.location.hash === '#pnlt-login') {
        window.scrollTo(0, window.scrollY - 50);
    }
});
function startCountdownPnlt() {
    var countdown = login_script_data_panelet.smstime;
    var countdownNumberEl = document.getElementById('countdown-number-pnlt');
    var countdownCircle = document.getElementById('countdown-circle-pnlt');
    var countdownCross = document.getElementById('countdown-cross-pnlt');
    countdownNumberEl.textContent = countdown;
    countdownCross.style.visibility = 'hidden';
    countdownCross.style.opacity = '0';
    var interval = setInterval(function() {
        countdown = --countdown <= -1 ? countdown : countdown;
        countdownNumberEl.textContent = countdown;
        countdownCircle.style.visibility = 'visible';
        countdownCircle.style.opacity = '1';
        countdownNumberEl.style.visibility = 'visible';
        if (countdown === 0) {
            countdownCircle.style.visibility = 'hidden';
            countdownCircle.style.opacity = '0';
            countdownNumberEl.style.visibility = 'hidden';
            countdownCross.style.visibility = 'visible';
            countdownCross.style.opacity = '1';
            document.getElementById('otppnlt').value = "";
            successText.style.display = 'none';
            errorText.textContent = "زمان ثبت کد به پایان رسید! لطفا مجددا تلاش کنید";
            errorText.style.display = 'block';
            clearInterval(interval);
            loginForm.querySelector(".step-login-pnlt").value = "1";
            loginForm.querySelector(".step-validate-pnlt").value = "@Bl!aN;;k#5$";
            loginForm.querySelector(".otp-pnlt").style.display = "none";
            loginForm.querySelector(".otp-mobile-pnlt").style.display = "block";
        }
    }, 1000);
}
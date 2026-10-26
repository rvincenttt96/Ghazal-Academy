function openMobileMenu() {
  document.querySelector(".side-box").style.transform="translateX(0)";
  document.querySelector(".blur-bg-side-menu-closer").style.visibility="visible";
}
function closeMobileMenu() {
  document.querySelector(".side-box").style.transform="translateX(300px)";
  document.querySelector(".blur-bg-side-menu-closer").style.visibility="hidden";
}

const formAvatar = document.getElementById("avatar_change");
if (formAvatar) {
    const imageInput = document.getElementById('file-profile-image');
    const imageBox = formAvatar.querySelector(".user-avatar");
    const options = document.querySelectorAll('.option');
    const imageDefaultUrl = document.getElementById('image-default-url');
    const imageType = document.getElementById('image-type');

    imageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        let fileReader = new FileReader();
        fileReader.readAsDataURL(file);
        fileReader.onload = function () {
            imageBox.setAttribute('src', fileReader.result);
            imageDefaultUrl.value = "";
            imageType.value = "uploaded";
        }
    });

    options.forEach(option => {
        option.addEventListener('click', () => {
            const img = option.querySelector('img');
            const imageUrl = img.getAttribute('src');
            imageBox.setAttribute('src', imageUrl);
            imageDefaultUrl.value = imageUrl.substring(imageUrl.length - 7);
            imageType.value = "default";
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const formAvatar = document.getElementById("avatar_change");
        const errorTextAvatar = formAvatar.querySelector(".error-text");
        const successTextAvatar = formAvatar.querySelector(".success-text");
        let submitButtonAvatar = formAvatar.querySelector(".btn-panel");

        formAvatar.addEventListener('submit', function (e) {
            e.preventDefault();
            document.body.classList.add('loading');

            let formData = new FormData(formAvatar);
            formData.append('action', 'ajax_avatar_panelet');

            let xhr = new XMLHttpRequest();
            xhr.open('POST', login_script_data_panelet.ajax_url);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    errorTextAvatar.style.display = "none";
                    successTextAvatar.style.display = "none";
                    let data = JSON.parse(xhr.responseText);
                    if (data.success === "success") {
                        errorTextAvatar.style.display = "none";
                        successTextAvatar.style.display = "block";
                        successTextAvatar.textContent = data.message;
                        location.reload();
                    } else {
                        errorTextAvatar.textContent = data.error;
                        errorTextAvatar.style.display = "block";
                    }
                }
                document.body.classList.remove('loading');
            };
            xhr.onerror = function () {
                document.body.classList.remove('loading');
            };
            xhr.send(formData);
        });
    });
}

const formTicket = document.getElementById("new_ticket_panelet");
if (formTicket) {
    const errorTextTicket = formTicket.querySelector(".error-text");
    const successTextTicket = formTicket.querySelector(".success-text");
    let submitButtonTicket = formTicket.querySelector(".btn-panel");
    formTicket.addEventListener('submit', function (e) {
        e.preventDefault();
        document.body.classList.add('loading');
        let formData = new FormData(formTicket);
        formData.append('action', 'new_ticket_panelet');
        let xhr = new XMLHttpRequest();
        xhr.open('POST', login_script_data_panelet.ajax_url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            if (xhr.status === 200) {
                errorTextTicket.style.display = "none";
                successTextTicket.style.display = "none";
                let data = JSON.parse(xhr.responseText);
                if (data.success) {
                    errorTextTicket.style.display = "none";
                    successTextTicket.style.display = "block";
                    successTextTicket.textContent = data.message;
                    if (data.data && data.data.ticket_url) {
                        const ticketUrl = data.data.ticket_url;
                        window.location.href = ticketUrl;
                    } else {
                        console.error("'ticket_url' is missing in the response data");
                    }
                } else {
                    console.error("Ticket creation failed");
                }
            }
            document.body.classList.remove('loading');
        };
        xhr.onerror = function () {
            document.body.classList.remove('loading');
        };
        xhr.send(formData);
    });

}

const formAddTicket = document.getElementById("add_ticket_panelet");
if (formAddTicket) {
    let submitButtonTicket = formAddTicket.querySelector(".btn-panel");
    const chatBoxPnlt = document.getElementById('chatBoxPnlt');
    const chatStatusPnlt = document.getElementById('chatStatusPnlt');
    const chatInputPnlt = document.getElementById('ticket_text');
    formAddTicket.addEventListener('submit', function (e) {
        e.preventDefault();
        document.body.classList.add('loading');
        let formData = new FormData(formAddTicket);
        formData.append('action', 'add_ticket_panelet');
        let xhr = new XMLHttpRequest();
        xhr.open('POST', login_script_data_panelet.ajax_url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    location.reload();
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    document.body.classList.remove('loading');
                }
            }
        };
        xhr.onerror = function () {
            document.body.classList.remove('loading');
        };
        xhr.send(formData);
    });
}
const formAdminTicket = document.getElementById("admin_ticket_panelet");
if (formAdminTicket) {
    let submitButtonTicket = formAdminTicket.querySelector(".btn-panel");
    const chatBoxPnlt = document.getElementById('chatBoxPnlt');
    const chatStatusPnlt = document.getElementById('chatStatusPnlt');
    const chatInputPnlt = document.getElementById('ticket_text');
    formAdminTicket.addEventListener('submit', function (e) {
        e.preventDefault();
        document.body.classList.add('loading');
        let formData = new FormData(formAdminTicket);
        formData.append('action', 'admin_ticket_panelet');
        let xhr = new XMLHttpRequest();
        xhr.open('POST', login_script_data_panelet.ajax_url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    location.reload();
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    document.body.classList.remove('loading');
                }
            }
        };
        xhr.onerror = function () {
            document.body.classList.remove('loading');
        };
        xhr.send(formData);
    });
}
let infoPopupForm = document.querySelector(".shkrn-info-popup-form");
if (infoPopupForm) {
    let errorText = infoPopupForm.querySelector('.error-text-shkrn');
    let successText = infoPopupForm.querySelector('.success-text-shkrn');
    document.addEventListener('DOMContentLoaded', function () {
        infoPopupForm.addEventListener('submit', function (e) {
            e.preventDefault();
            infoPopupForm.classList.add('loading');
            let firstName = document.getElementById('first_name').value;
            let lastName = document.getElementById('last_name').value;
            let data = new FormData();
            data.append('action', 'save_user_info_shkrn');
            data.append('nonce', login_script_data_panelet.nonce);
            data.append('first_name', firstName);
            data.append('last_name', lastName);
            fetch(login_script_data_panelet.ajax_url, {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(response => {
                    infoPopupForm.classList.remove('loading');
                    errorText.style.display = 'none';
                    successText.style.display = 'none';
                    if (response.success === 'success') {
                        successText.textContent = response.message;
                        successText.style.display = 'block';
                        location.reload();
                    } else if(response.data.action == "reload") {
                        errorText.textContent = response.error;
                        errorText.style.display = 'block';
                        showLoadingOverlayByno();
                        location.reload();
                    } else {
                        errorText.textContent = response.error;
                        errorText.style.display = 'block';
                    }
                });
        });
    });
}
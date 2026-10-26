<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['nickname'])) {
    header('Location: login.php');
    exit;
}

$current_sender_id = $_SESSION['user_id'];
$current_sender_name = $_SESSION['nickname']; 

$messages = [];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>چت روم جامع</title>
    <style>
        body { font-family: Tahoma, sans-serif; margin: 0; display: flex; flex-direction: column; height: 100vh; background-color: #f0f2f5; }
        #header { background-color: #3b5998; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        #logoutButton { background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 14px; }
        #chatArea { flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; background-color: #ffffff; }
        .message { margin-bottom: 15px; padding: 10px 15px; border-radius: 18px; max-width: 65%; min-width: 100px; line-height: 1.6; word-wrap: break-word; box-shadow: 0 1px 0.5px rgba(0,0,0,0.13); }
        .my-message { background-color: #dcf8c6; align-self: flex-end; text-align: left; border-bottom-right-radius: 2px; }
        .other-message { background-color: #ffffff; align-self: flex-start; text-align: right; border: 1px solid #e0e0e0; border-bottom-left-radius: 2px; }
        .system-message { background-color: #fff3cd; color: #856404; align-self: center; text-align: center; border-radius: 10px !important; max-width: 40%; }
        .message-sender { font-weight: bold; margin-bottom: 3px; font-size: 0.9em; }
        #messageInputContainer { padding: 10px; background-color: #f9f9f9; border-top: 1px solid #ddd; display: flex; }
        #messageForm { display: flex; width: 100%; }
        #messageContent { flex-grow: 1; padding: 10px 15px; border: 1px solid #ccc; border-radius: 25px; margin-left: 10px; font-size: 16px; }
        #sendMessageButton { background-color: #2ecc71; color: white; border: none; padding: 10px 25px; border-radius: 25px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; }
        #sendMessageButton:hover { background-color: #27ae60; }
    </style>
</head>
<body>

    <div id="header">
        <div>
            <h1> چت روم غزالی ها </h1>
            <span id="userNickname">نام شما: <?php echo htmlspecialchars($current_sender_name); ?></span>
        </div>
        <button id="logoutButton" onclick="logout()">خروج</button>
    </div>

    <div id="chatArea">
        <?php 
        echo '<div class="message system-message"><div class="message-sender"></div>سیستم: چت روم فعال شد.</div>';
        if ($current_sender_name == 'Admin User' && $current_sender_id == 1) {
            echo '<div class="message system-message"><div class="message-sender"></div>به سیستم خوش آمدید! این یک پیام نمایشی از سمت سرور است.</div>';
        }
        ?>
    </div>

    <div id="messageInputContainer">
        <form id="messageForm">
            <input type="text" id="messageContent" name="message_content" placeholder="پیام خود را بنویسید..." autocomplete="off">
            <button type="submit" id="sendMessageButton">ارسال</button>
        </form>
    </div>

    <input type="hidden" id="current-user-nickname" value="<?php echo htmlspecialchars($current_sender_name); ?>">
    <input type="hidden" id="current-user-id" value="<?php echo htmlspecialchars($current_sender_id); ?>">

    <script>
        const chatArea = document.getElementById('chatArea');
        const messageForm = document.getElementById('messageForm');
        const messageContent = document.getElementById('messageContent');
        const currentNickname = document.getElementById('current-user-nickname').value;
        const currentId = parseInt(document.getElementById('current-user-id').value);

        function scrollToBottom() {
            chatArea.scrollTop = chatArea.scrollHeight;
        }

function appendMessage(message) {
            const msgDiv = document.createElement('div');
            msgDiv.classList.add('message');
            
            let senderDisplay = '';
            let className;

            if (message.sender_id === 0) {
                
                className = 'system-message';
                senderDisplay = ''; 
            } else if (message.sender_id === currentId) {
                
                className = 'my-message';
                senderDisplay = currentNickname; 
            } else {
                
                className = 'other-message';
                senderDisplay = message.sender_name; 
            }
            
            msgDiv.classList.add(className);
            
            let htmlContent = '';
            if (message.sender_id !== 0) {
                htmlContent += `<div class="message-sender">${senderDisplay}</div>`;
            }
            
            htmlContent += message.content.replace(/\n/g, '<br>');
            
            msgDiv.innerHTML = htmlContent;
            chatArea.appendChild(msgDiv);
            scrollToBottom();
        }
        
        function loadMessages() {
            fetch('load_messages.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chatArea.innerHTML = `<?php 
                            echo '<div class="message system-message"><div class="message-sender"></div>سیستم: چت روم فعال شد.</div>';
                            if ($current_sender_name == 'Admin User' && $current_sender_id == 1) {
                                echo '<div class="message system-message"><div class="message-sender"></div>به سیستم خوش آمدید! این یک پیام نمایشی از سمت سرور است.</div>';
                            }
                        ?>`; // محتوای HTML اولیه را بازسازی می‌کنیم.

                        data.messages.forEach(message => {
                            appendMessage(message);
                        });
                        scrollToBottom(); 
                    } else {
                        console.error('خطا در بارگذاری تاریخچه چت:', data.error);
                    }
                })
                .catch(error => {
                    console.error('خطا در اتصال به سرور برای بارگذاری تاریخچه:', error);
                });
        }


        window.onload = function() {
            loadMessages(); 
            
            // 2. تنظیم بروزرسانی خودکار هر 3 ثانیه
            setInterval(loadMessages, 3000);
        };

        messageForm.onsubmit = async function(e) {
            e.preventDefault();
            
            const content = messageContent.value.trim();
            
            if (content === "") {
                return;
            }
            
            const optimisticMessage = {
                sender_id: currentId,
                sender_name: currentNickname,
                content: content
            };
            appendMessage(optimisticMessage);
            
            try {
                const response = await fetch('process_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `message_content=${encodeURIComponent(content)}`
                });

                const result = await response.json(); 

                if (result.success) {
                    console.log("پیام با موفقیت ارسال شد.");

                    // loadMessages(); 
                } else {
                    console.error("خطا در ارسال پیام:", result.error || "خطای ناشناخته");
                    chatArea.lastChild.remove(); // حذف پیام خوشبینانه
                    appendMessage({sender_id: 0, sender_name: 'System', content: 'خطا در ارسال پیام. لطفاً مجدداً تلاش کنید.'});
                }

            } catch (error) {
                console.error("خطا در ارتباط شبکه:", error);
                chatArea.lastChild.remove(); // حذف پیام خوشبینانه
                appendMessage({sender_id: 0, sender_name: 'System', content: 'خطای شبکه در ارسال پیام.'});
            }

            messageContent.value = '';
        };

        function logout() {
            fetch('logout.php', {
                method: 'POST'
            })
            .then(() => {
                window.location.href = 'login.php';
            })
            .catch(error => {
                console.error('Logout failed:', error);
                window.location.href = 'login.php';
            });
        }
    </script>
</body>
</html>

function loadMessages() {
    const senderId = document.getElementById('sender-id').value;
    const receiverId = document.getElementById('receiver-id').value;
    
    fetch(`get_messages.php?sender_id=${senderId}&receiver_id=${receiverId}`)
        .then(response => response.json())
        .then(data => {
            const messagesContainer = document.getElementById('messages');
            messagesContainer.innerHTML = ''; // پاکسازی قبل از نمایش مجدد

            if (data.length === 0) {
                messagesContainer.innerHTML = '<p>No messages found.</p>';
                return;
            }

            data.forEach(msg => {
                const msgDiv = document.createElement('div');
                
                msgDiv.innerHTML = `<strong>${msg.sender_name}:</strong> ${msg.message}`; 
                
                messagesContainer.appendChild(msgDiv);
            });
            messagesContainer.scrollTop = messagesContainer.scrollHeight; // اسکرول به پایین
        })
        .catch(error => console.error('Error loading messages:', error));
}

function sendMessage() {
    const messageText = document.getElementById('message_input').value;
    const senderId = document.getElementById('sender-id').value;
    const receiverId = document.getElementById('receiver-id').value;

    if (messageText.trim() === "") return;

    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sender_id: senderId,
            receiver_id: receiverId,
            message: messageText
        })
    })
    .then(response => response.text())
    .then(result => {
        console.log(result);
        document.getElementById('message_input').value = ''; // پاک کردن ورودی
        loadMessages(); // بارگذاری مجدد پیام‌ها
    })
    .catch(error => console.error('Error sending message:', error));
}

loadMessages();
setInterval(loadMessages, 2000);

<?php
$title = 'Messages';
require_once 'layout.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/messages/message-core.php';

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<title>Messages</title>
<link rel="stylesheet" href="../../assets/css/supervisor/supervisor-messages.css">
</head>

<body>

<ul class="nav nav-tabs mb-3" id="messageTabs">
<li class="nav-item">
<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#myChats">
My Chats
</button>
</li>
</ul>

<div class="tab-content">

<div class="tab-pane fade show active" id="myChats">

<div class="new-chat-box">

<select id="roleSelect" onchange="loadUsersByRole()">
<option value="">Select role</option>
<option value="student">Student</option>
<option value="supervisor">Supervisor</option>
</select>

<select id="userSelect">
<option value="">Select user</option>
</select>

<button onclick="startNewChat()">Start</button>

</div>

<div class="chat-wrappers">

<div class="conversation-list" id="conversationList"></div>

<div class="chat-area">

<div class="chat-header" id="chatHeader">
<button class="back-btn" onclick="backToList()">←</button>
<span>Select conversation</span>
</div>

<div class="messages" id="messages"></div>

<div class="send-box">
<input type="text" id="messageInput" placeholder="Type a message">
<button onclick="sendMessage()">Send</button>
</div>

</div>

</div>

</div>

</div>

<?php require_once 'layout-end.php'; ?>

<script>

let currentReceiver = null;
let currentConversation = null;
let myUserId = <?php echo $user_id ?>;

// ---------------------- LOAD USERS FOR NEW CHAT ----------------------

function loadUsersByRole() {

    let role = document.getElementById("roleSelect").value;

    fetch('../../includes/messages/message-api.php?action=get_teacher_available_users&role=' + role)

        .then(r => r.json())

        .then(data => {

            let html = '<option value="">Select user</option>';

            data.users.forEach(u => {
                html += `<option value="${u.id}">${u.full_name}</option>`;
            });

            document.getElementById("userSelect").innerHTML = html;
        });
}


// ---------------------- START NEW CHAT ----------------------

function startNewChat(){

    let userId = document.getElementById("userSelect").value;

    if(!userId){
        alert("Select a user first");
        return;
    }

    currentReceiver = userId;
    currentConversation = null;

    document.querySelector(".chat-wrappers").classList.add("chat-open");
    document.getElementById("messages").innerHTML="";
}


// ---------------------- LOAD ALL EXISTING CHATS ----------------------

function loadConversations(){

    fetch('../../includes/messages/message-api.php?action=get_conversations')

    .then(r=>r.json())

    .then(data=>{

        let html='';

        data.conversations.forEach(conv => {

            let lastMessage = conv.last_message ? conv.last_message : '';

            html += `
            <div class="conversation" onclick="openConversation(${conv.conversation_id},${conv.user_id})">
                <div class="chat-info">
                    <div>
                        <div class="chat-name">${conv.full_name}</div>
                        <div class="conv-last">${lastMessage}</div>
                    </div>
                </div>
            </div>
            `;
        });

        document.getElementById("conversationList").innerHTML=html;

    });

}


// ---------------------- OPEN A CONVERSATION ----------------------

function openConversation(conversationId,receiverId){

    currentConversation = conversationId;
    currentReceiver = receiverId;

    loadMessages();

    document.querySelector(".chat-wrappers").classList.add("chat-open");
}


// ---------------------- LOAD MESSAGES ----------------------

function loadMessages(){

    if(!currentConversation) return;

    fetch('../../includes/messages/message-api.php?action=get_messages&conversation_id='+currentConversation)

    .then(r=>r.json())

    .then(data=>{

        let html='';

        data.messages.forEach(m=>{
            let cls=m.sender_id==myUserId?'me':'';
            html+=`
            <div class="message ${cls}">
                <div class="bubble">${m.message}</div>
            </div>
            `;
        });

        let box=document.getElementById("messages");

        box.innerHTML=html;
        box.scrollTop=box.scrollHeight;

    });

}


// ---------------------- SEND MESSAGE ----------------------

function sendMessage(){

    let msg = document.getElementById("messageInput").value.trim();
    if(!msg) return;

    if(!currentReceiver){
        alert("Receiver not selected");
        return;
    }

    let formData = new FormData();

    formData.append("receiver_id", currentReceiver);
    formData.append("message", msg);

    fetch("../../includes/messages/message-api.php?action=send_message",{
        method:"POST",
        body:formData
    })

    .then(r=>r.json())

    .then(data=>{

        if(data.status==="success"){

            document.getElementById("messageInput").value="";

            if(!currentConversation){
                currentConversation = data.conversation_id;
            }

            loadMessages();
            loadConversations();

        }else{
            alert(data.message);
        }

    });

}


// ---------------------- UI BACK BUTTON ----------------------

function backToList(){
    document.querySelector(".chat-wrappers").classList.remove("chat-open");
}


// Load existing conversations
loadConversations();

</script>

</body>
</html>

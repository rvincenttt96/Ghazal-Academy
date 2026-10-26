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
<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#teacherMonitor">
Teachers Monitoring
</button>
</li>
</ul>
<div class="tab-content">

<div class="tab-pane fade show active" id="myChats">

<div class="new-chat-box">

<select id="roleSelect" onchange="loadUsersByRole()">

<option value="">Select role</option>
<option value="teacher">Teacher</option>
<option value="student">Student</option>

</select>

<select id="userSelect">
<option>Select user</option>
</select>

<button onclick="startNewChat()">Start</button>

</div>

<div class="chat-wrappers">

<div class="conversation-list" id="conversationList">
</div>

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
<div class="tab-pane fade" id="teacherMonitor">

<div class="card">

<div class="card-body">

<div class="row g-3">

<div class="col-md-3">
<label class="form-label">Select Teacher</label>
<select class="form-select" id="teacherSelect" onchange="loadTeacherChats(this.value)">
<option>Select Teacher</option>
</select>

</div>


<div class="col-md-3">
	<label class="form-label">Chats</label>
	<div id="teacherConversationList" class="list-group message-conversations"></div>

</div>


<div class="col-md-6">
<label class="form-label">Conversation</label>
<div id="monitorMessages" class="message-monitor-box"></div>

</div>

</div>

</div>

</div>
<?php require_once 'layout-end.php'; ?>
<script>
let currentReceiver = null;
let currentConversation = null;
let myUserId = <?php echo $user_id ?>;



function loadUsersByRole(){

let role=document.getElementById("roleSelect").value

fetch('../../includes/messages/message-api.php?action=get_users_by_role&role='+role)

.then(r=>r.json())

.then(data=>{

let html='<option value="">Select user</option>'

data.users.forEach(u=>{

html+=`<option value="${u.id}">${u.full_name}</option>`

})

document.getElementById("userSelect").innerHTML=html

})

}



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



function loadConversations(){

fetch('../../includes/messages/message-api.php?action=get_conversations')
.then(r=>r.json())
.then(data=>{

let html=''

data.conversations.forEach(conv => {

let lastMessage = conv.last_message ? conv.last_message : '';

let avatar = conv.avatar 
? conv.avatar 
: '../../uploads/avatars/daweid-icon-7797704_640.png';

html += `
<div class="conversation" onclick="openConversation(${conv.conversation_id},${conv.user_id})">

<div class="chat-info">

<div class="chat-avatar">
<img src="${avatar}">
</div>

<div>

<div class="chat-name">
${conv.full_name}
</div>

<div class="conv-last">
${lastMessage}
</div>

</div>

</div>

</div>
`;

});

document.getElementById("conversationList").innerHTML=html

})

}



function backToList(){

document.querySelector(".chat-wrappers").classList.remove("chat-open")

}



function openConversation(conversationId,receiverId){

currentConversation = conversationId;

currentReceiver = receiverId;

loadMessages();

document.querySelector(".chat-wrappers").classList.add("chat-open");

}



function loadMessages(){

if(!currentConversation) return

fetch('../../includes/messages/message-api.php?action=get_messages&conversation_id='+currentConversation)

.then(r=>r.json())

.then(data=>{

let html=''

data.messages.forEach(m=>{

let cls=m.sender_id==myUserId?'me':''

html+=`
<div class="message ${cls}">
<div class="bubble">${m.message}</div>
</div>
`

})

let box=document.getElementById("messages")

box.innerHTML=html

box.scrollTop=box.scrollHeight

})

}



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

console.log(data);

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



loadConversations();



document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {

tab.addEventListener('shown.bs.tab', function (event) {

let target = event.target.getAttribute("data-bs-target");

if(target === "#teacherMonitor"){
loadTeachers();
}

});

});



function loadTeachers(){

fetch('../../includes/messages/message-api.php?action=get_teachers')

.then(res=>res.json())

.then(data=>{

let html='<option value="">Select Teacher</option>';

data.teachers.forEach(t=>{

html+=`<option value="${t.id}">${t.full_name}</option>`;

});

document.getElementById("teacherSelect").innerHTML=html;

});

}



function loadTeacherChats(teacherId){

fetch('../../includes/messages/message-api.php?action=get_teacher_conversations&teacher_id='+teacherId)

.then(res=>res.json())

.then(data=>{

let list = document.getElementById("teacherConversationList");

list.innerHTML="";

if(data.conversations.length==0){

list.innerHTML="<div class='p-3'>No conversations</div>";

return;

}

data.conversations.forEach(c=>{

list.innerHTML += `
<a href="javascript:void(0)" class="list-group-item"
onclick="openTeacherConversation(${c.conversation_id})">

<b>${c.user_name}</b><br>
<small>${c.last_message ?? ''}</small>

</a>
`;

});

});

}



function openTeacherConversation(conversationId){

fetch('../../includes/messages/message-api.php?action=get_messages&conversation_id='+conversationId)

.then(res => res.json())

.then(data => {

const box = document.getElementById("monitorMessages");

box.innerHTML = "";

data.messages.forEach(msg => {

const div = document.createElement("div");

div.innerHTML = `
<b>${msg.full_name}</b><br>
${msg.message}
`;

box.appendChild(div);

});

});

}

</script>

</body>

</html>

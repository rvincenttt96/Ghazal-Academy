<?php

require_once __DIR__ . '/../db.php';

global $pdo;


/*
|--------------------------------------------------------------------------
| Create Private Conversation (Prevent Duplicate)
|--------------------------------------------------------------------------
*/

function createPrivateConversation($user1, $role1, $user2, $role2){

global $pdo;

/* check if conversation already exists */

$stmt = $pdo->prepare("
SELECT c.id
FROM conversations c
JOIN conversation_participants p1 ON p1.conversation_id = c.id
JOIN conversation_participants p2 ON p2.conversation_id = c.id
WHERE p1.user_id = ?
AND p2.user_id = ?
AND c.type='private'
LIMIT 1
");

$stmt->execute([$user1,$user2]);

$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if($existing){
return $existing['id'];
}


/* create conversation */

$pdo->prepare("
INSERT INTO conversations (type)
VALUES ('private')
")->execute();

$conversation_id = $pdo->lastInsertId();


/* add participants */

$stmt = $pdo->prepare("
INSERT INTO conversation_participants
(conversation_id,user_id,role)
VALUES (?,?,?)
");

$stmt->execute([$conversation_id,$user1,$role1]);
$stmt->execute([$conversation_id,$user2,$role2]);

return $conversation_id;

}



/*
|--------------------------------------------------------------------------
| Send Message
|--------------------------------------------------------------------------
*/

function sendMessage($conversation_id,$sender_id,$message){

global $pdo;

$stmt = $pdo->prepare("
INSERT INTO messages
(conversation_id,sender_id,message)
VALUES (?,?,?)
");

$stmt->execute([$conversation_id,$sender_id,$message]);

}



/*
|--------------------------------------------------------------------------
| Get Messages
|--------------------------------------------------------------------------
*/

function getMessages($conversation_id){

global $pdo;

$stmt = $pdo->prepare("
SELECT
m.*,
u.full_name,
u.avatar
FROM messages m
JOIN users u ON u.id = m.sender_id
WHERE m.conversation_id=?
ORDER BY m.created_at ASC
");

$stmt->execute([$conversation_id]);

return $stmt->fetchAll(PDO::FETCH_ASSOC);

}



/*
|--------------------------------------------------------------------------
| Get Conversations
|--------------------------------------------------------------------------
*/

function getUserConversations($user_id){

global $pdo;

$stmt = $pdo->prepare("
SELECT 
c.id as conversation_id,
u.id as user_id,
u.full_name,
u.avatar
FROM conversations c

JOIN conversation_participants cp1 
ON cp1.conversation_id = c.id

JOIN conversation_participants cp2 
ON cp2.conversation_id = c.id
AND cp2.user_id != cp1.user_id

JOIN users u 
ON u.id = cp2.user_id

WHERE cp1.user_id = ?
");

$stmt->execute([$user_id]);

return $stmt->fetchAll(PDO::FETCH_ASSOC);

}




/*
|--------------------------------------------------------------------------
| Get Last Message
|--------------------------------------------------------------------------
*/

function getLastMessage($conversation_id){

global $pdo;

$stmt = $pdo->prepare("
SELECT message,created_at
FROM messages
WHERE conversation_id=?
ORDER BY created_at DESC
LIMIT 1
");

$stmt->execute([$conversation_id]);

return $stmt->fetch(PDO::FETCH_ASSOC);

}



/*
|--------------------------------------------------------------------------
| Mark Messages Read
|--------------------------------------------------------------------------
*/

function markMessagesRead($conversation_id,$user_id){

global $pdo;

$stmt = $pdo->prepare("
UPDATE messages
SET is_read=1
WHERE conversation_id=?
AND sender_id != ?
");

$stmt->execute([$conversation_id,$user_id]);

}



/*
|--------------------------------------------------------------------------
| Count Unread
|--------------------------------------------------------------------------
*/

function countUnreadMessages($user_id){

global $pdo;

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM messages m
JOIN conversation_participants cp
ON cp.conversation_id=m.conversation_id
WHERE cp.user_id=?
AND m.sender_id != ?
AND m.is_read=0
");

$stmt->execute([$user_id,$user_id]);

return $stmt->fetchColumn();

}

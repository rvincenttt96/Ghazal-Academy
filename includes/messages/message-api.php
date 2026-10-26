<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/message-core.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit;
}

global $pdo;

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

/*
|--------------------------------------------------------------------------
| Get or Create Conversation
|--------------------------------------------------------------------------
*/
function getOrCreateConversation(PDO $pdo, $user1, $user2)
{

    if(!$user1 || !$user2 || $user2 == 'undefined'){
        throw new Exception("Invalid user id");
    }

    // check existing conversation
    $sql = "
        SELECT c.id
        FROM conversations c
        JOIN conversation_participants cp1 
            ON cp1.conversation_id = c.id
        JOIN conversation_participants cp2 
            ON cp2.conversation_id = c.id
        WHERE c.type='private'
        AND cp1.user_id = ?
        AND cp2.user_id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user1,$user2]);

    $conv = $stmt->fetch(PDO::FETCH_ASSOC);

    if($conv){
        return $conv['id'];
    }

    // create new conversation
    $pdo->prepare("
        INSERT INTO conversations (type,created_at)
        VALUES ('private',NOW())
    ")->execute();

    $conversationId = $pdo->lastInsertId();

    // insert participants (بدون role)
    $ins = $pdo->prepare("
        INSERT INTO conversation_participants 
        (conversation_id,user_id)
        VALUES (?,?)
    ");

    $ins->execute([$conversationId,$user1]);
    $ins->execute([$conversationId,$user2]);

    return $conversationId;
}



/*
|--------------------------------------------------------------------------
| API ACTIONS
|--------------------------------------------------------------------------
*/
switch ($action) {


/* SEND MESSAGE */
case 'send_message':

$receiver_id = $_POST['receiver_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if(!$receiver_id || $receiver_id == 'undefined'){
    echo json_encode([
        "status"=>"error",
        "message"=>"Receiver invalid"
    ]);
    exit;
}

if(!$message){
    echo json_encode([
        "status"=>"error",
        "message"=>"Message empty"
    ]);
    exit;
}

try{

$conversation_id = getOrCreateConversation($pdo,$user_id,$receiver_id);

$stmt = $pdo->prepare("
    INSERT INTO messages
    (conversation_id,sender_id,message,created_at)
    VALUES (?,?,?,NOW())
");

$stmt->execute([
    $conversation_id,
    $user_id,
    $message
]);

echo json_encode([
    "status"=>"success",
    "conversation_id"=>$conversation_id
]);

}catch(Exception $e){

echo json_encode([
    "status"=>"error",
    "message"=>$e->getMessage()
]);

}

break;



/* GET MESSAGES */
case 'get_messages':
    $conversation_id = $_GET['conversation_id'] ?? 0;
    if (!$conversation_id) {
        echo json_encode(["status"=>"error","message"=>"Conversation ID required"]);
        exit;
    }

    $messages = getMessages($conversation_id);
    markMessagesRead($conversation_id, $user_id);

    echo json_encode(["status"=>"success","messages"=>$messages]);
break;


/* GET USER CONVERSATIONS */
case 'get_conversations':

    $conversations = getUserConversations($user_id);

    foreach ($conversations as &$conv) {
        $last = getLastMessage($conv['conversation_id']);
        $conv['last_message'] = $last['message'] ?? '';
        $conv['last_time'] = $last['created_at'] ?? '';
    }

    echo json_encode([
        "status" => "success",
        "conversations" => $conversations
    ]);
break;


/* USERS BY ROLE */
case 'get_users_by_role':
    $role = $_GET['role'] ?? '';
    if (!in_array($role,['teacher','student'])) {
        echo json_encode(["status"=>"error","message"=>"Invalid role"]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT id, full_name
        FROM users
        WHERE role=? AND is_deleted=0
        ORDER BY full_name
    ");
    $stmt->execute([$role]);

    echo json_encode([
        "status"=>"success",
        "users"=>$stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
break;


/* GET TEACHERS */
case 'get_teachers':
    $stmt = $pdo->prepare("
        SELECT id, full_name
        FROM users
        WHERE role='teacher' AND is_deleted=0
        ORDER BY full_name
    ");
    $stmt->execute();

    echo json_encode([
        "status"=>"success",
        "teachers"=>$stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
break;


/* TEACHER MONITORING */
case 'get_teacher_conversations':

$teacher_id = intval($_GET['teacher_id'] ?? 0);

$sql = "
SELECT
c.id AS conversation_id,

u.id AS user_id,
u.full_name AS user_name,
u.role AS user_role,

m.message AS last_message,
m.created_at AS last_time

FROM conversations c

JOIN conversation_participants cp_teacher
ON cp_teacher.conversation_id = c.id
AND cp_teacher.user_id = :teacher1

JOIN users ut
ON ut.id = cp_teacher.user_id
AND ut.role = 'teacher'

JOIN conversation_participants cp_other
ON cp_other.conversation_id = c.id
AND cp_other.user_id != :teacher2

JOIN users u
ON u.id = cp_other.user_id

LEFT JOIN messages m
ON m.id = (
SELECT id
FROM messages
WHERE conversation_id = c.id
ORDER BY created_at DESC
LIMIT 1
)

ORDER BY m.created_at DESC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
'teacher1'=>$teacher_id,
'teacher2'=>$teacher_id
]);

echo json_encode([
"status"=>"success",
"conversations"=>$stmt->fetchAll(PDO::FETCH_ASSOC)
]);

break;




default:
    echo json_encode(["status"=>"error","message"=>"Invalid action"]);
break;
}

?>

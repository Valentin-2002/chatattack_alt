<?php

// Local DB

const hostname = 'localhost';
const username = 'root';
const password = '';
const dbname = 'm426_chatattack_db';

// Work PC

// const hostname = 'localhost';
// const username = 'root';
// const password = 'Valentin2020';
// const dbname = 'm426_chatattack_db';

try{

    $pdo = new PDO("mysql:host=" . hostname . ";dbname=" . dbname, username, password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e){

    die("ERROR: Could not connect. " . $e->getMessage());

}

function fetch_users($pdo) {

	$query = "SELECT * FROM user WHERE id != '".$_SESSION['id']."' AND id != 0";

	$statement = $pdo->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$output = '<ul>';

	foreach($result as $row)
	{
		if($row['id'] !== 0) {
			$output .= '
			<li>
				<a href="?uid=' . $row['id'] . '">' . $row['username'] . '</a>
			</li>
			';
		}
	}

	$output .= '</ul>';

	return $output;

}

function profile_is_public($pdo, $userId) {
	$query = "SELECT * FROM user WHERE id = " . $userId;
	$statement = $pdo->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();

	$outputArray = ['public' => $result[0]['type'] === 'public', 'username' => $result[0]['username']];
	
	return $outputArray;
}

function fetch_friend_requests($pdo, $userId) {
	$query = "SELECT * FROM relation WHERE status = 'requested' AND reciever = " . $userId;
	$statement = $pdo->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();

	$output = '<ul>';

	foreach($result as $row) {
		$output .= '<li>' . get_user_name($row['sender'], $pdo) . '<a href="handle_friend_request.php?action=accept&userId=' . $row['sender'] . '">Accept</a><a href="handle_friend_request.php?action=deny&userId=' . $row['sender'] . '">Deny</a></li>';
	}

	if(!$result) {
		echo '<p class="warning">No Friend Requests</p>';
	}

	return $output .= '</ul>';

}

function is_friend($pdo, $user1id, $user2id) {
	$query = "SELECT * FROM relation WHERE status = 'accepted' AND sender = :user1 AND reciever = :user2 UNION SELECT * FROM relation WHERE status = 'accepted' AND sender = :user2 AND reciever = :user1;";
	$statement = $pdo->prepare($query);
	$statement->execute(
		array(
			':user1' => $user1id, ':user2' => $user2id
		)
	);

	$result = $statement->fetchAll();

	return !(!$result);
}

function has_sent_friend_request ($pdo, $user1id, $user2id) {
	$query = "SELECT * FROM relation WHERE status = 'requested' AND sender = :user1 AND reciever = :user2 UNION SELECT * FROM relation WHERE status = 'requested' AND sender = :user2 AND reciever = :user1;";
	$statement = $pdo->prepare($query);
	$statement->execute(
		array(
			':user1' => $user1id, ':user2' => $user2id
		)
	);

	$result = $statement->fetchAll();

	return !(!$result);
}

function has_denied_friend_request ($pdo, $user1id, $user2id) {
	$query = "SELECT * FROM relation WHERE status = 'denied' AND sender = :user1 AND reciever = :user2 UNION SELECT * FROM relation WHERE status = 'denied' AND sender = :user2 AND reciever = :user1;";
	$statement = $pdo->prepare($query);
	$statement->execute(
		array(
			':user1' => $user1id, ':user2' => $user2id
		)
	);

	$result = $statement->fetchAll();

	return !(!$result);
}

function fetch_friends($pdo) {

	$query = "SELECT * FROM user WHERE id != '".$_SESSION['id']."' AND id != 0";

	$statement = $pdo->prepare($query);
	$statement->execute(
		array(
			':userId' => $_SESSION['id']
		)
	);

	$result = $statement->fetchAll();

	$output = '<ul>';

	foreach($result as $row)
	{
		if($row['id'] !== 0 && is_friend($pdo, $row['id'], $_SESSION['id'])) {
			$output .= '
			<li>
				<a href="?uid=' . $row['id'] . '">' . $row['username'] . '</a>
			</li>
			';
		}
	}

	$output .= '</ul>';

	return $output;
}

function fetch_friend_list($pdo) {

	$query = "SELECT * FROM user WHERE id != '".$_SESSION['id']."' AND id != 0";

	$statement = $pdo->prepare($query);
	$statement->execute(
		array(
			':userId' => $_SESSION['id']
		)
	);

	$result = $statement->fetchAll();

	$output = '<ul>';

	foreach($result as $row)
	{
		if($row['id'] !== 0 && is_friend($pdo, $row['id'], $_SESSION['id'])) {
			$output .= '
			<li><p>' . $row['username'] . '</p><a href="remove_friend.php?uid=' . $row['id'] . '">Remove</a></li>
			';
		}
	}

	$output .= '</ul>';

	return $output;
}
function fetch_chat($pdo, $from_user_id, $to_user_id)
{

	$public = profile_is_public($pdo, $to_user_id);

	if(!$public['public'] && !is_friend($pdo, $from_user_id, $to_user_id)) {

		if(has_sent_friend_request($pdo, $from_user_id, $to_user_id)) {
			$status = '<div class="private-chat-blocker"><p class="warning">You have requested ' . $public['username'] . ' as a Friend</p></div>';
		} else if(has_denied_friend_request($pdo, $from_user_id, $to_user_id)) {
			$status = '<div class="private-chat-blocker"><p class="warning">' . $public['username'] . ' has denied your Friend Request</p></div>';
		} else {
			$status = '<div class="private-chat-blocker"><p class="warning">' . $public['username'] . ' has a private Profile. You must be friends with ' . $public['username'] . ' to start a Chat with him!</p><a class="button" href="send_friend_request.php?uid=' . $to_user_id . '">Add Friend</a></div>';
		}

		return $status;
	}

	$query = "
	SELECT * FROM chat
	WHERE (from_user = '".$from_user_id."' 
	AND to_user = '".$to_user_id."') 
	OR (from_user = '".$to_user_id."' 
	AND to_user = '".$from_user_id."') 
	ORDER BY time DESC
	";
	$statement = $pdo->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$user_name = '';
		$chat_message = $row['msg'];
		if($row["from_user"] == $from_user_id)
		{
			$user_name = '<b>You</b>';
			$output .= '
			<li class="chatmessageown">
				<p>' . $chat_message . '
					<div>
						- <small><em>'.$row['time'].'</em></small>
					</div>
				</p>
			</li>
			';
			
		}
		else
		{
			$user_name = '<b>'.get_user_name($row['from_user'], $pdo).'</b>';
			$output .= '
			<li class="chatmessage">
				<p>' .$chat_message. '
					<div>
						- <small><em>'.$row['time'].'</em></small>
					</div>
				</p>
			</li>
			';
		}
	}
	$output .= '';
	// $output .= '</ul>';
	return $output;
}

function get_user($pdo, $userId) {

	$query = 'SELECT * FROM user WHERE user.id = "' . $userId . '" AND user.id != 0';

	$statement = $pdo->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	return $result['0'];

}

function get_user_name($user_id, $pdo)
{
	$query = "SELECT username FROM user WHERE id = $user_id";
	$statement = $pdo->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['username'];
	}
}

function save_profile_type($pdo, $userId, $type) {
	$query = 'UPDATE user SET user.type = "' . $type . '" WHERE user.id = ' . $userId;

	$statement = $pdo->prepare($query);

	$statement->execute();
}


function fetch_users_by_query($pdo, $searchQuery) {

	$query = 'SELECT * FROM user WHERE id != "' . $_SESSION['id'] . '" AND id != 0 AND username LIKE "' . $searchQuery . '"';

	$statement = $pdo->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	if(!$result) {
		$output = '<p id="mainSearchErrorMsg" class="warning">No Users with the Name "' . $searchQuery . '" found!</p>'; 
	} else {
		$output = '<ul class="users">';

		foreach($result as $row)
		{
			$output .= '
			<li>
				<a href="?uid=' . $row['id'] . '">' . $row['username'] . '</a>
			</li>
			';
		}

		$output .= '</ul>';
	}

	return $output;

}

?>
<?php

include('db-connection.php');


session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

$action = htmlspecialchars($_GET['action']);
$userId = htmlspecialchars($_GET['userId']);

if(isset($action)) {
	if($action === 'accept') {
		$query = "
		UPDATE relation SET status='accepted'
  		WHERE sender = :sender AND reciever = :reciever
		";
	} elseif ($action === 'deny') {
		$query = "
		UPDATE relation SET status='denied'
  		WHERE sender = :sender AND reciever = :reciever
		";
	}
}

$statement = $pdo->prepare($query);
$statement->execute(
	array(
		':sender' => $userId, ':reciever' => $_SESSION['id']
	)
);

header("location:profile.php");

?>
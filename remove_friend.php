<?php

include('db-connection.php');

session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

$userId = htmlspecialchars($_GET['uid']);

$query = "DELETE FROM relation WHERE sender=:user1 AND reciever=:user2; DELETE FROM relation WHERE sender=:user2 AND reciever=:user1";

$statement = $pdo->prepare($query);
$statement->execute(
	array(
		':user1' => $_SESSION['id'], ':user2' => $userId
	)
);

header("location:profile.php");

?>
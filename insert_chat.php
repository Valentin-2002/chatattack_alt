<link rel="stylesheet" href="style.css">

<?php

include('db-connection.php');

session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $from_user_id = $_SESSION['id'];
    $to_user_id = $_POST['uid'];
    $message = $_POST['message'];

    $sql = "INSERT INTO chat (from_user, to_user, msg) VALUES (:from_user_id, :to_user_id, :message)";

    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":from_user_id", $from_user_id);
        $stmt->bindParam(":to_user_id", $to_user_id);
        $stmt->bindParam(":message", $message);
        
        $stmt->execute();
    } 
    
    $sql = "UPDATE user SET last_activity = NOW() WHERE id=:id";

    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $from_user_id);
        
        $stmt->execute();
    } 

    unset($stmt);
    
    unset($pdo);  

    if($_POST['uid'] !== 0) {
        header("location: index.php?uid=" . $to_user_id);

    } else {
        header("location: index.php");
    }
}


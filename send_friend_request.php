<?php

include('db-connection.php');


session_start();

if(!isset($_SESSION['id']))
{
    header("location:login.php");
}

$userId = htmlspecialchars($_GET['uid']);

if(isset($userId)) {
    $query = "INSERT INTO relation (sender, reciever, status) VALUES (:sender, :reciever, 'requested')";
    $statement = $pdo->prepare($query);
    $statement->execute(
        array(
            ':sender' => $_SESSION['id'], ':reciever' => $userId
        )
    );
}


header("location:index.php?uid=" . $userId);

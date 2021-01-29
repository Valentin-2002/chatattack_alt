<?php

include('db-connection.php');

session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

$from_user_id = $_SESSION['id'];
$to_user_id = $_GET['to_user_id'];

try{

    $pdo = new PDO("mysql:host=" . hostname . ";dbname=" . dbname, username, password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e){

    die("ERROR: Could not connect. " . $e->getMessage());

}

$output = fetch_chat($pdo, $from_user_id, $to_user_id);

echo $output;

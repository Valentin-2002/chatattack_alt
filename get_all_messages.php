<?php
require_once "db-connection.php";

session_start();


if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];

        $query = "
        SELECT * FROM chat LEFT JOIN user ON from_user=user.id
        WHERE to_user = '".$userId."'
        ORDER BY time ASC
        ";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode($results);

        echo $json;
    }

}
<link rel="stylesheet" href="public/style.css">

<?php



session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

if(isset($_SESSION['role'])) {
	if($_SESSION['role'] == 1) {
		readfile("statics/adminnavigation.html");
	} else {
		header("location:index.php");
	}
}

if(isset($_GET["id"]) && !empty($_GET["id"])){
    require_once "db-connection.php";
    
    $sql = "DELETE FROM user WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $param_id);
        
        $param_id = trim($_GET["id"]);
        
        if($stmt->execute()){
            header("location: admin-menu.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    unset($stmt);
    
    unset($pdo);
} else{
    if(empty(trim($_GET["id"]))){
        header("location: error.php");
        exit();
    }
}
?>
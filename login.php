<link rel="stylesheet" href="public/style.css">

<?php

include('db-connection.php');

session_start();

$message = '';

if(isset($_SESSION['id']))
{
	header('location:index.php');
}

if(isset($_POST['login']))
{
	$query = "
		SELECT * FROM user 
  		WHERE username = :username
	";
	$statement = $pdo->prepare($query);
	$statement->execute(
		array(
			':username' => $_POST["username"]
		)
	);	
	$count = $statement->rowCount();
	if($count > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if(sha1($_POST['credential']) == $row['credential'])
			{
				$_SESSION['id'] = $row['id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['role'] = $row['role'];

				header('location:index.php');
			}
			else
			{
				$message = '<label>Wrong Password</label>';
			}
		}
	}
	else
	{
		$message = '<label>Username dont exist</label>';
	}
}


?>

<html>  
    <head>  
        <title>ChatAttack</title>  
    </head>  
    <body>  
		<div class="splash">
			<h1 class="splash-title">Chatattack</h1>
			<div class="splash-form-wrapper">
				<form class="loginform" method="post">
						<p class="warning"><?php echo $message; ?></p>
						<div>
							<input type="text" name="username" class="splash-text" placeholder="Username" maxlength="20"/>
						</div>
						<div>
							<input type="password" name="credential" class="splash-text" placeholder="Password" maxlength="20"/>
						</div>
						<div>
							<input type="submit" name="login" class="splash-submit" value="LOGIN" />
						</div>
						<a class="splash-secondary" href="register.php">REGISTER</a>
					</form>
			</div>
		</div>
		<footer class="footer">
			<p>2020 Chatattack Inc. A Project by Team Black Friday | TBZ Zürich, AP18b</p>
		</footer>
    </body>  
</html>
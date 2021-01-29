<link rel="stylesheet" href="public/style.css">

<?php

include('db-connection.php');

session_start();

$message = '';

if(isset($_SESSION['id']))
{
	header('location:index.php');
}

if(isset($_POST["register"]))
{

	$username = trim($_POST["username"]);
	$password = trim($_POST['credential']);
	$check_query = "
	SELECT * FROM user 
	WHERE username = :username
	";
	$statement = $pdo->prepare($check_query);
	$check_data = array(
		':username'		=>	$username
	);
	if($statement->execute($check_data))	
	{
		if($statement->rowCount() > 0)
		{
			$message .= '<label>Username is  already taken</label>';
		}
		else
		{
			if(empty($username))
			{
				$message .= '<label>Username is required</label>';
			}
			if(empty($password))
			{
				$message .= '<label>Password is required</label>';
			}
			else
			{
				if($password != $_POST['confirm_credential'])
				{
					$message .= '<label>Passwords dont match</label>';
				}
				else if(strlen($password >= 7)) {
					$message .= '<label>Minimum 8 Characters required</label>';
				}
			}
			if($message == '')
			{
				$data = array(
					':username'		=>	$username,
					':credential'		=>	sha1($password)
				);

				$query = "
				INSERT INTO user 
				(username, credential) 
				VALUES (:username, :credential)
				";
				$statement = $pdo->prepare($query);
				if($statement->execute($data))
				{
					$message = '<label>Registration Completed</label>';
				}
			}
		}
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
				<form class="registerform" method="post">
					<p class="warning"><?php echo $message; ?></p>
					<div>
						<input type="text" name="username" class="splash-text" placeholder="Username" maxlength="20"/>
					</div>
					<div>
						<input type="password" name="credential"  class="splash-text" placeholder="Password" maxlength="20"/>
					</div>
					<div>
						<input type="password" name="confirm_credential"  class="splash-text" placeholder ="Confirm Password" maxlength="20"/>
					</div>
					<div>
						<input type="submit" name="register" class="splash-submit" value="Register" />	
					</div>
					<a class="splash-secondary" href="login.php">LOGIN</a>
				</form>
			</div>
		</div>
		<footer class="footer">
			<p>2020 Chatattack Inc. A Project by Team Black Friday | TBZ Zürich, AP18b</p>
		</footer>
    </body>  
</html>

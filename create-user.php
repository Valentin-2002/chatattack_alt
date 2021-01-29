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


require_once "db-connection.php";
 
$message = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){


    $username = trim($_POST["username"]);
    $credential = trim($_POST['credential']);
    $credentialConfirm = trim($_POST['credential-confirm']);
    $type = htmlspecialchars($_POST['type']);
    $role = htmlspecialchars($_POST['role']);
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
			if(empty($credential))
			{
				$message .= '<label>Password is required</label>';
            }
            if(!empty($credential) && empty($credentialConfirm)) {
                $message .= '<label>Confirm your Password</label>';
            }
			else
			{
				if($credential !== $credentialConfirm)
				{
					$message .= '<label>Passwords dont match</label>';
				}
				else if(strlen($credential >= 7)) {
					$message .= '<label>Minimum 8 Characters required</label>';
				}
			}
			if($message == '')
			{
				$data = array(
					':username'		=>	$username,
                    ':credential'		=>	sha1($credential),
                    ':type' => $type,
                    ':role' => $role
				);

				$query = "
				INSERT INTO user (username, credential, type, role) VALUES (:username, :credential, :type, :role);
				";
				$statement = $pdo->prepare($query);
				if($statement->execute($data))
				{
					header('location: admin-menu.php');
				}
			}
        } 
    }

}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
</head>
<body>
    <div class="adminformsuperwrapper dynamic-bg">
        <div class="splash-form-wrapper">
            <form class="adminform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <p class="warning"><?= $message ?></p>
                <input hidden name="id" value="<?= $id ?>"></input>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-element" placeholder="New Username">
                </div>
                <div class="form-group>">
                    <label>Credential</label>
                    <input type="password" name="credential" class="form-element" placeholder="New Password"></input>
                    <input type="password" name="credential-confirm" class="form-element" placeholder="Confirm Password"></input>
                </div>
                <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-element">
                <option value="public">Public</option>
                <option value="private">Private</option>
                </select>
                </div>
                <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-element">
                <option value="1">Administrator</option>
                <option value="0">Default</option>
                </select>
                </div>
                <input type="submit" class="splash-submit" value="Edit User">
                <a href="admin-menu.php" class="splash-secondary">Cancel</a>
            </form>
        </div>
    </div>
    <footer class="footer">
		<p>Q4 2020 |  A Project by Team Black Friday | TBZ Zürich | AP18b</p>
	</footer>
</body>
</html>
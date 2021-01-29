
<?php

include('db-connection.php');


session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

if(isset($_POST['private'])) {
    if($_POST['private'] === 'on') {
        save_profile_type($pdo, $_SESSION['id'], 'private');
    } else if($_POST['private'] === 'off') {
        save_profile_type($pdo, $_SESSION['id'], 'public');
    }
}

$user = get_user($pdo, $_SESSION['id']);

?>


<html>  
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="public/style.css">
        <title>ChatAttack</title>  
    </head>  
    <body>  
        <header>
			<?php 
				if($_SESSION['role'] == 1) {
					include ("statics/adminnavigation.html");
				} else {
					include ("statics/defaultnavigation.html");
				}
			?>
		</header>
    </body>  
    <div class="dynamic-bg">
        <div class="profile">
            <h1>Profile of <?= $user['username'] ?></h1>
            <div class="profile-content">
                <h4>Profile Type</h4>
                <form id="profile-type-form" action="profile.php" method="POST">
                    <span class="profile-span-white">Private</span>

                    <label class="switch">
                        <?php
                            if($user['type'] === 'private') {
                                echo '<label id="profile-type-checkbox" class="form-switch" checked><input type="checkbox" checked><i></i></label>';
                                echo '<input type="text" name="private" value="off" hidden>';
                            } else {
                                echo '<label id="profile-type-checkbox" class="form-switch"><input type="checkbox"><i></i></label>';
                                echo '<input type="text" name="private" value="on" hidden>';
                            }
                        ?>
                        <span class="slider"></span>
                    </label>
                </form>
            </div>
            <div class="profile-content">
                <h4>Friend Requests</h4>
                <?= fetch_friend_requests($pdo, $_SESSION['id']) ?>
            </div>
            <div class="profile-content">
                <h4>Friends</h4>
                <?= fetch_friend_list($pdo) ?>
            </div>
        </div>
    </div>
	<footer class="footer">
		<p>Q4 2020 |  A Project by Team Black Friday | TBZ Zürich | AP18b</p>
	</footer>
</html>
<script src="public/client.js"></script>




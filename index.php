
<?php

include('db-connection.php');

session_start();

if(!isset($_SESSION['id']))
{
	header("location:login.php");
}

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
			<?php include ("statics/defaultnavigation.html"); ?>
		</header>
		<div id="home">
			<div id="userlist">
				<div class="search-input">
					<form action="index.php" method="GET">
						<input class="search-query" maxlength="24" type="text" name="q" placeholder="Search Users">
						<button class="search-submit" type="submit">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
								<path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
							</svg>
						</button>
					</form>
				</div>
				<?php
				
					if(!isset($_GET['q'])) {
						echo fetch_users($pdo);
					} else {
						echo fetch_users_by_query($pdo, htmlspecialchars($_GET['q']));
					}
				
				?>
			</div>
			<div class="chatbox">
				<img src="img/temp-img.jpg" alt="Temporary Image">
			</div>
		</div>
    </body>  
	<footer class="footer">
		<p>2020 Chatattack Inc. A Project by Team Black Friday | TBZ Zürich, AP18b</p>
	</footer>
</html>
<script src="public/client.js"></script>




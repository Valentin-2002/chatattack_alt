
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
			<?php 
				if($_SESSION['role'] == 1) {
					include ("statics/adminnavigation.html");
				} else {
					include ("statics/defaultnavigation.html");
				}
			?>
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
				<div class="toggle-group">
					<button id="all-users">All Users</button>
					<button id="friends">Friends</button>
				</div>
				<div class="user-link-wrapper active">
					<?php
					
						if(!isset($_GET['q'])) {
							echo fetch_users($pdo);
						} else {
							echo fetch_users_by_query($pdo, htmlspecialchars($_GET['q']));
						}
						
					?>
				</div>
				<div class="user-link-wrapper">
					<?php

						echo fetch_friends($pdo);

					?>
				</div>
			</div>
			<div id="chatWrapper">
				<div id="chatbox">
					<ul class="chathistory">

						<?php

							if(isset($_GET['uid'])) {
								echo fetch_chat($pdo, $_SESSION['id'], htmlspecialchars($_GET['uid']));
							} else {
								echo '<img src="img/temp-img.jpg" alt="Home Image">';
							}

						?>
					</ul>
				</div>
				<?php

					if(isset($_GET['uid']) && (is_friend($pdo, $_SESSION['id'], $_GET['uid']) || profile_is_public($pdo, $_GET['uid'])['public'])) {
						echo "<form id='chatform' action='insert_chat.php' method='POST'>
									<input id='messageinput' type='text' name='message' placeholder='Enter Message'></input>
									<input type='hidden' name='uid' value='" . htmlspecialchars($_GET['uid']) . "'></input>
									<button id='sendbutton' type='submit' name='submit'>
										<svg id='sendicon' enable-background='' height='24' viewBox='48 48' width='24' xmlns='http://www.w3.org/2000/svg'><path d='m8.75 17.612v4.638c0 .324.208.611.516.713.077.025.156.037.234.037.234 0 .46-.11.604-.306l2.713-3.692z'/><path d='m23.685.139c-.23-.163-.532-.185-.782-.054l-22.5 11.75c-.266.139-.423.423-.401.722.023.3.222.556.505.653l6.255 2.138 13.321-11.39-10.308 12.419 10.483 3.583c.078.026.16.04.242.04.136 0 .271-.037.39-.109.19-.116.319-.311.352-.53l2.75-18.5c.041-.28-.077-.558-.307-.722z'/></svg>
									</button>
							   </form>";
					}

				?>
			</div>
		</div>
    </body>  
	<footer class="footer">
		<p>Q4 2020 |  A Project by Team Black Friday | TBZ Zürich | AP18b</p>
	</footer>
	</html>

	<script src="public/client.js"></script>
	<script src="public/external/push.js"></script>
	
	<script>

	async function postData() {
		var url = 'http://localhost/fetch_user_chat_history.php?to_user_id=' + <?= htmlspecialchars($_GET['uid']) ?>;
		// Default options are marked with *
		const response = await fetch(url, {
			method: 'GET',
		});
		return response.text(); // parses JSON response into native JavaScript objects
	}

	const myInterval = setInterval(
		()=> { 
			postData()
			.then( data => 
				{
					var chatBox = document.querySelector('#chatbox > ul');
					chatBox.innerHTML = data;
				}
			)
		}, 
		1000
		);
	</script>

<script>
	
  // At last, if the user has denied notifications, and you
  // want to be respectful there is no need to bother them any more.

	document.addEventListener("DOMContentLoaded", function() {

		function notificationOutput(title, msg) {
			if (!("Notification" in window)) {
				alert("This browser does not support desktop notification");
			} else if (Notification.permission === "granted") {
				var notification = new Notification(title, {
					'body': msg
				});
				notification.onclick = function(event) {
					window.open('localhost', '_blank');
				}
			} else if (Notification.permission !== "denied") {
				Notification.requestPermission().then(function (permission) {
				if (permission === "granted") {
					var notification = new Notification("really");
				}
				});
			}
		}

		var count = Infinity;

		// Example POST method implementation:
		async function notificationPostData() {
			var url = 'http://localhost/get_all_messages.php';
			// Default options are marked with *
			const response = await fetch(url, {
				method: 'POST',
			});
			return response.json(); // parses JSON response into native JavaScript objects
		}

		const notificationInterval = setInterval(
			()=> { 
				notificationPostData()
				.then( data => 
					{
						var dataLength = data.length;
						
						if(dataLength > count) {
							var diff = dataLength - count;
							
							var notificationMessages = data.slice(count - dataLength);

							console.log(notificationMessages);

							for(var i = 0; i < notificationMessages.length; i++) {
								notificationOutput(notificationMessages[i].username, notificationMessages[i].msg);
							}
							
						}

						count = data.length;
					}
				)
			}, 
			1000
		);

	  });
	
</script>

<script>

	document.addEventListener("DOMContentLoaded", function() {
		
		var allBtn = document.querySelector('button#all-users');
		var friendsBtn = document.querySelector('button#friends');

		allBtn.classList.add('toggled');

		allBtn.addEventListener('click', () => {
			var userLists = document.querySelectorAll('div.user-link-wrapper');
			userLists[0].classList.add('active');
			userLists[1].classList.remove('active');

			var toggleButtons = document.querySelectorAll('div.toggle-group > button');
			toggleButtons[0].classList.add('toggled');
			toggleButtons[1].classList.remove('toggled');
		})

		friendsBtn.addEventListener('click', () => {
			var userLists = document.querySelectorAll('div.user-link-wrapper');
			userLists[1].classList.add('active');
			userLists[0].classList.remove('active');

			var toggleButtons = document.querySelectorAll('div.toggle-group > button');
			toggleButtons[1].classList.add('toggled');
			toggleButtons[0].classList.remove('toggled');
		})

	});

</script>



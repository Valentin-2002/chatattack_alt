<?php

// Local DB

const hostname = 'localhost';
const username = 'root';
const password = '';
const dbname = 'm426_chatattack_db';

try{

    $pdo = new PDO("mysql:host=" . hostname . ";dbname=" . dbname, username, password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e){

    die("ERROR: Could not connect. " . $e->getMessage());

}

function fetch_users($pdo) {

	$query = "SELECT * FROM user WHERE id != '".$_SESSION['id']."' AND id != 0";

	$statement = $pdo->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$output = '<ul>';

	foreach($result as $row)
	{
		$output .= '
		<li>
			<a href="#">'. $row['username'] .'</a>
		</li>
		';
	}

	$output .= '</ul>';

	return $output;

}

function fetch_users_by_query($pdo, $searchQuery) {

	$query = "SELECT * FROM user WHERE id != '".$_SESSION['id']."' AND id != 0 AND username LIKE '".$searchQuery."'";

	$statement = $pdo->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	if(!$result) {
		$output = '<p id="mainSearchErrorMsg" class="warning">No Users with the Name "' . $searchQuery . '" found!</p>'; 
	} else {
		$output = '<ul>';

		foreach($result as $row)
		{
			$output .= '
			<li>
				<a href="#">'. $row['username'] .'</a>
			</li>
			';
		}

		$output .= '</ul>';
	}

	return $output;

}


?>
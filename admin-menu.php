<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChatAttack</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
    <header>    
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
        ?>
    </header>
        <div class="adminpage dynamic-bg">
            <?php
            require_once "db-connection.php";
            if(isset($_GET['sort'])) {
                if($_GET['sort'] === 'timeasc') {
                    $sql = "SELECT * FROM user WHERE id != 0 ORDER BY last_activity ASC";
                } else if($_GET['sort'] === 'timedesc') {
                    $sql = "SELECT * FROM user WHERE id != 0 ORDER BY last_activity DESC";
                }
            } else {
                $sql = "SELECT * FROM user WHERE id != 0";
            }
            if($result = $pdo->query($sql)){
                if($result->rowCount() > 0){
                    echo "<table id='table'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th>#</th>";
                                echo "<th>Username</th>";
                                echo "<th>Role</th>";
                                echo "<th>Profile</th>";
                                echo "<th>Last Activity

                                    &nbsp<a class='button' href='admin-menu.php?sort=timeasc'>
                                    <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-arrow-up-square' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                                        <path fill-rule='evenodd' d='M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z'/>
                                        <path fill-rule='evenodd' d='M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z'/>
                                    </svg>
                                    </a>
                                    &nbsp<a class='button' href='admin-menu.php?sort=timedesc'>
                                        <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-arrow-down-square' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' d='M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z'/>
                                            <path fill-rule='evenodd' d='M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z'/>
                                        </svg>
                                    </a>

                                    </th>";
                                echo '<th>Action &nbsp; <a href="create-user.php" id="addnewuserbutton">Add New User</a></th>';
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($row = $result->fetch()){
                            echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['username'] . "</td>";
                                echo "<td>" . ($row['role'] == 1 ? 'Admin' : 'Default') . "</td>";
                                echo "<td>" . $row['type'] . "</td>";
                                echo "<td>" . $row['last_activity'] . "</td>";
                                echo "<td>";
                                    echo "<a class='actionbutton' href='edit-user.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'>Edit</a>";
                                    echo "<a class='actionbutton' href='delete-user.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'>Delete</a>";
                                echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";                            
                    echo "</table>";
                    unset($result);
                } else{
                    echo "<p class='lead'><em>No records were found.</em></p>";
                }
            } else{
                echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
            }
            
            unset($pdo);
            ?>
        </div>
        <footer class="footer">
            <p>Q4 2020 |  A Project by Team Black Friday | TBZ Zürich | AP18b</p>
        </footer>
</body>
</html>
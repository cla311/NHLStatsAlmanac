<?php 
require('../required/nav.php');
require('../required/functions.php');
session_start();

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    $email = $_SESSION['user_email'];
    $firstName = $_SESSION['firstName'];
    $username = $_SESSION['username'];
} else {
    $email = $_SESSION['user_email'] = [];
    $firstName = $_SESSION['firstName'] = [];
    $username = $_SESSION['username'] = [];
}

if (!empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
    $title = $_SESSION['team_title'];
    $fantasyTeamID = $_SESSION['fantasyTeamID'];
} else {
    $title = $_SESSION['team_title'] = [];
    $fantasyTeamID = $_SESSION['fantasyTeamID'] = [];
}

if (!empty($_SESSION['playerID']) && !empty($_SESSION['name'])) {
    $playerID = $_SESSION['playerID'];
    $playerName = $_SESSION['name'];
} else {
    $playerID = $_SESSION['playerID'] = [];
    $playerName = $_SESSION['name'] = [];
}


?>

<?php require_login(); // if not logged in, redirect to login page ?>

<?php
$teamID = trim($_GET['fantasyTeamID']);

// insert player into fantasy team table
if (!empty($_SESSION['playerID']) && !empty($_SESSION['name']))
{
    $sql_insert_player = "INSERT INTO $fantasyTeamID VALUES ($fantasyTeamID,$username,$playerID,$title)";
    $res = $db->query($sql_insert_player);
    
    // unset session values
    unset($_SESSION['playerID']);
    unset($_SESSION['name']);
}

echo "<h3>".$title."</h3>";

echo "<table border=\"solid\">";
echo "<tr>";
echo "<th>Players</th>";
echo "<th>Position</th>";
echo "</tr>";

echo "</table>";

echo "<a href=\"lookup.php\">Search Players</a>"; 
?>
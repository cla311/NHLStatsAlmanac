<?php 
require('../required/nav.php');
require('../required/functions.php');
session_start();

if (!empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
    $title = $_SESSION['team_title'];
    $fantasyTeamID = $_SESSION['fantasyTeamID'];
} else {
    $title = $_SESSION['team_title'] = [];
    $fantasyTeamID = $_SESSION['fantasyTeamID'] = [];
}
?>

<?php require_login(); // if not logged in, redirect to login page ?>

<?php
$teamID = trim($_GET['fantasyTeamID']);

echo "<h3>".$title."</h3>";

echo "<table border=\"solid\">";
echo "<tr>";
echo "<th>Players</th>";
echo "<th>Position</th>";
echo "</tr>";
echo "</table>";

echo "<a href=\"lookup.php\">Search Players</a>"; 
?>
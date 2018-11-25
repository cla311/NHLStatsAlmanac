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
    echo "yes<br />$playerName<br />";
} else {
    $playerID = $_SESSION['playerID'] = [];
    $playerName = $_SESSION['name'] = [];
    echo "nope";
}
?>

<?php require_login(); // if not logged in, redirect to login page   ?>

<?php

$teamID = trim($_GET['fantasyTeamID']);

// insert player into fantasy team table
<<<<<<< HEAD
if (!empty($_SESSION['playerID']) && !empty($_SESSION['name']))
{
    $sql_insert_player = "INSERT INTO $fantasyTeamID VALUES ('".$fantasyTeamID."','".$username."','".$playerID."','".$title."')";
=======
if (!empty($_SESSION['playerID']) && !empty($_SESSION['name'])) {
    $sql_insert_player = "INSERT INTO $fantasyTeamID VALUES ($fantasyTeamID,$username,$playerID,$title)";
>>>>>>> 4351fbdd835e449270218d35b00b855e77df664c
    echo $sql_insert_player;
    $res = $db->query($sql_insert_player);

    // unset session values
    unset($_SESSION['playerID']);
    unset($_SESSION['name']);
}

<<<<<<< HEAD
$query = "SELECT $fantasyTeamID.playerID, player.name, player.position FROM $fantasyTeamID INNER JOIN player ON $fantasyTeamID.playerID = player.playerID WHERE $fantasyTeamID.playerID = player.playerID";
echo $query;
$res = $db->query($query);

echo "<h3>".$title."</h3>";
=======
echo "<h3>" . $title . "</h3>";
>>>>>>> 4351fbdd835e449270218d35b00b855e77df664c

echo "<table border=\"solid\">";
echo "<tr>";
echo "<th>Players</th>";
echo "<th>Position</th>";
echo "</tr>";
<<<<<<< HEAD
while ($row = $res->fetch_row())
{
    echo "<tr>";
    echo "<td align=\"center\">";
    format_name_as_link($row[0],$row[1],"details.php");
    echo "</td>";
    echo "<td align=\"center\">";
    echo $row[2];
    echo "</td>";
    echo "</tr>";
}
=======
>>>>>>> 4351fbdd835e449270218d35b00b855e77df664c
echo "</table>";

echo "<a href=\"lookup.php\">Search Players</a>";
?>
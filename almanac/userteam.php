<?php

session_start();
require('../required/nav.php');
require('../required/functions.php');

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

<?php require_login(); // if not logged in, redirect to login page   ?>

<?php

$teamID = trim($_GET['fantasyTeamID']);

$team_title = array();
array_push($team_title, $teamID);
$team_title = explode("_", $teamID);

$_SESSION['fantasyTeamID'] = $teamID;
$_SESSION['team_title'] = $team_title[0];

// insert player into fantasy team table
if (!empty($_SESSION['playerID']) && !empty($_SESSION['name']) && !empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
    if ((strpos($_SESSION['username'], $team_title[1])) !== false) {
        $sql_insert_player = "INSERT INTO $fantasyTeamID VALUES ('" . $teamID . "','" . $username . "','" . $playerID . "','" . $title . "')";
        echo $sql_insert_player;
        $res = $db->query($sql_insert_player);

        // unset session values
        unset($_SESSION['playerID']);
        unset($_SESSION['name']);
    } else {
        redirect_to("fantasy.php");
    }
}

$query = "SELECT $teamID.playerID, player.name, player.position FROM $teamID INNER JOIN player ON $teamID.playerID = player.playerID WHERE $teamID.playerID = player.playerID";
// echo $query;
$res = $db->query($query);

echo "<div class=\"body\">";
echo "<div class=\"content\">";
echo "<h3 class=\"fantasy-title\">" . $team_title[0] . "</h3>";
echo "<p class=\"author\">Created by " . $team_title[1] . "</p>";

echo "<table class=\"fantasy-roster\">";
echo "<tr>";
echo "<th>Players</th>";
echo "<th>Position</th>";
echo "</tr>";
while ($row = $res->fetch_row()) {
    echo "<tr>";
    echo "<td>";
    format_name_as_link($row[0], $row[1], "details.php");
    echo "</td>";
    echo "<td align=\"center\">";
    echo $row[2];
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br />";
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    if (strpos($_SESSION['username'], $team_title[1]) !== false) {
        echo "<div class=\"center-block\">";
        echo "<p><a class=\"add\" href=\"lookup.php\">Search Players</a></p>";
        echo "</div>";
    }
}
echo "</div>";
echo "</div>";
?>
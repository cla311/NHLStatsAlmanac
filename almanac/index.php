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

// display fantasy teams
$sql_display_fantasy = "SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = 'nhl_stats'
AND TABLE_NAME != 'goalie_stats' AND TABLE_NAME != 'members' AND TABLE_NAME != 'player'
AND TABLE_NAME != 'roster' AND TABLE_NAME != 'stats' AND TABLE_NAME != 'team' AND TABLE_NAME != 'watchlist'
AND TABLE_NAME != 'team_watchlist'";
$sql_display_fantasy .= " LIMIT 10";
$res = $db->query($sql_display_fantasy);

echo "<div class=\"grid\">";
echo "<div class=\"grid-col-1of2\">";
echo "<h3>Member Fantasy Teams</h3>";
echo "<ul>";
while ($row = $res->fetch_row()) {
    $sql_display = "SELECT DISTINCT team_title FROM $row[0]";
    $result = $db->query($sql_display);
    while ($subRow = $result->fetch_row()) {
        echo "<li>";
        echo "<a href=\"userteam.php?fantasyTeamID=$row[0]\">$subRow[0]</a>";
        echo "</li>";
    }
}
echo "</ul>";
echo "</div>";

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    $sql_watchlist = "SELECT watchlist.playerID, player.name FROM watchlist JOIN player ON watchlist.playerID = player.playerID WHERE username = '$username'";
    $list = $db->query($sql_watchlist);

    $sql_team_watchlist = "SELECT team_watchlist.teamID, team.team_name FROM team_watchlist JOIN team ON team_watchlist.teamID = team.teamID WHERE username = '$username'";
    $listTeam = $db->query($sql_team_watchlist);
    echo mysqli_error($db);

    echo "<div class=\"grid-col-1of2\">";
        echo "<h3>Favourites</h3>";
        echo "<label class='watchlist'>Players</label>";
        echo "<ul>";
        while ($row = $list->fetch_row()) {
            echo "<li class='list-item'>";
            format_name_as_link($row[0], $row[1], "details.php"); // link shows product name, but is identified by it's product code
            echo "</li>\n";
        }
        mysqli_free_result($list);
        echo "</ul><br />";
        echo "<label class='watchlist'>Teams</label>";
        echo "<ul>";
        while ($row = $listTeam->fetch_row()) {
            echo "<li class='list-item'>";
            format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link shows product name, but is identified by it's product code
            echo "</li>\n";
        }
        mysqli_free_result($listTeam);
        echo "</ul>";
    echo "</div>";
}
echo "</div>";

$db->close();
?>
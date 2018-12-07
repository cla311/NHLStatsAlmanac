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

// get fantasy teams
$sql_display_fantasy = "SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS "
        . "WHERE COLUMN_NAME IN ('team_title','team_author') AND TABLE_SCHEMA='nhl_stats'";
$res = $db->query($sql_display_fantasy);

echo "<div class=\"grid\">";
echo "<div class=\"grid-col-1of2\">";
// check if user is signed in or not
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {

    // get fantasy belonging to the current member
    echo "<h3>My Fantasy Teams</h3>";
    echo "<ul>";
    while ($row = $res->fetch_row()) {
        $sql_display = "SELECT DISTINCT team_title, fantasyTeamID FROM $row[0] "
                . "WHERE team_author='" . $_SESSION['username'] . "'";
        $my_result = $db->query($sql_display);
        while ($subRow = $my_result->fetch_row()) {

            $team_creator = array();
            array_push($team_creator, $subRow[1]);
            $team_creator = explode("_", $subRow[1]);
            if ($team_creator[1] == $_SESSION['username']) {
                echo "<li>";
                echo "<a href=\"userteam.php?fantasyTeamID=$subRow[1]\">$subRow[0]</a>";
                echo "</li>";
            }
        }
    }
    mysqli_free_result($my_result);
    echo "</ul><br/>";
    echo "<h3>Member Fantasy Teams</h3>";
    echo "<ul>";
    $res->data_seek(0);
    while ($row = $res->fetch_row()) {
        // get the other fantasy teams that don't belong to the current user
        $sql_display = "SELECT DISTINCT team_title, fantasyTeamID FROM $row[0] "
                . "WHERE team_author!='" . $_SESSION['username'] . "'";
        $result = $db->query($sql_display);
        while ($subRow = $result->fetch_row()) {
            echo "<li>";
            echo "<a href=\"userteam.php?fantasyTeamID=$subRow[1]\">$subRow[0]</a>";
            echo "</li>";
        }
    }
    mysqli_free_result($result);
    echo "</ul>";
} else { // if user isn't signed in, just show any fantasy teams
    echo "<h3>Member Fantasy Teams</h3>";
    echo "<ul>";
    while ($row = $res->fetch_row()) {
        $sql_display = "SELECT DISTINCT team_title, fantasyTeamID FROM $row[0]";
        $result = $db->query($sql_display);
        while ($subRow = $result->fetch_row()) {
            echo "<li>";
            echo "<a href=\"userteam.php?fantasyTeamID=$subRow[1]\">$subRow[0]</a>";
            echo "</li>";
        }
    }
    mysqli_free_result($result);
    echo "</ul>";
}
mysqli_free_result($res);
echo "</div>";

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    // show favourites list for players
    $sql_watchlist = "SELECT * FROM (SELECT watchlist.playerID, player.name FROM "
            . "watchlist JOIN player ON watchlist.playerID = player.playerID "
            . "WHERE username = '$username' ORDER BY rand() LIMIT 10) T1 ORDER BY name";
    $list = $db->query($sql_watchlist);

    // show favourites list for teams
    $sql_team_watchlist = "SELECT * FROM (SELECT team_watchlist.teamID, "
            . "team.team_name FROM team_watchlist JOIN team ON "
            . "team_watchlist.teamID = team.teamID WHERE username = '$username' "
            . "ORDER BY rand() LIMIT 5) T1 ORDER BY team_name";
    $listTeam = $db->query($sql_team_watchlist);

    echo "<div class=\"grid-col-1of2\">";
    echo "<h3>Favourites</h3>";
} else { // otherwise, show 10 random players and 5 random teams
    $sql_watchlist = "SELECT * FROM (SELECT playerID, name FROM player ORDER BY "
            . "rand() LIMIT 10) T1 ORDER BY name";
    $list = $db->query($sql_watchlist);

    $sql_team_watchlist = "SELECT * FROM (SELECT teamID, team_name FROM team "
            . "ORDER BY rand() LIMIT 5) T1 ORDER BY team_name";
    $listTeam = $db->query($sql_team_watchlist);

    echo "<div class=\"grid-col-1of2\">";
    echo "<h3>Featured</h3>";
}

echo "<label class='watchlist'>Players</label>";
echo "<ul>";
while ($row = $list->fetch_row()) {
    echo "<li class='list-item'>";
    format_name_as_link($row[0], $row[1], "details.php"); // link to player info page with playerID
    echo "</li>\n";
}
mysqli_free_result($list);
echo "</ul><br />";
echo "<label class='watchlist'>Teams</label>";
echo "<ul>";
while ($row = $listTeam->fetch_row()) {
    echo "<li class='list-item'>";
    format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link to team info page with teamID
    echo "</li>\n";
}
mysqli_free_result($listTeam);
echo "</ul>";
echo "</div>";
echo "</div>";

$db->close();
?>
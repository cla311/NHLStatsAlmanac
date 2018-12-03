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

$id = trim($_GET['teamID']);
$_SESSION['teamID'] = $id;

echo "<br /><br />";

$url = $nhlAPI . '/api/v1/teams/' . $id . "?expand=team.stats";
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
$team_stats = curl_exec($ch);
curl_close($ch);
$teamStats_array = json_decode($team_stats, true);

foreach ($teamStats_array["teams"] as $stats) {
    $teamID = $id;
    $played = $stats["teamStats"][0]["splits"][0]["stat"]["gamesPlayed"];
    $wins = $stats["teamStats"][0]["splits"][0]["stat"]["wins"];
    $losses = $stats["teamStats"][0]["splits"][0]["stat"]["losses"];
    $ot = $stats["teamStats"][0]["splits"][0]["stat"]["ot"];
    $points = $stats["teamStats"][0]["splits"][0]["stat"]["pts"];
    $goalsForGame = $stats["teamStats"][0]["splits"][0]["stat"]["goalsPerGame"];
    $goalsAgainstGame = $stats["teamStats"][0]["splits"][0]["stat"]["goalsAgainstPerGame"];
    $ppPercent = $stats["teamStats"][0]["splits"][0]["stat"]["powerPlayPercentage"];
    $pkPercent = $stats["teamStats"][0]["splits"][0]["stat"]["penaltyKillPercentage"];
    $shotsForGame = round($stats["teamStats"][0]["splits"][0]["stat"]["shotsPerGame"], 1);
    $shotsAgainstGame = round($stats["teamStats"][0]["splits"][0]["stat"]["shotsAllowed"], 1);
    $faceoff = $stats["teamStats"][0]["splits"][0]["stat"]["faceOffWinPercentage"];
}

$query = "UPDATE team SET games_played=?, wins=?, losses=?, ot_losses=?, "
        . "points=?, goals_for_per_game=?, goals_against_per_game=?, "
        . "power_play_percent=?, penalty_kill_percent=?, shots_for_per_game=?, "
        . "shots_against_per_game=?, faceoff_win_percent=? WHERE teamID=?";
$stmt = $db->prepare($query);
$stmt->bind_param('iiiiidddddddi', $played, $wins, $losses, $ot, $points,
        $goalsForGame, $goalsAgainstGame, $ppPercent, $pkPercent, $shotsForGame,
        $shotsAgainstGame, $faceoff, $teamID);
$stmt->execute();
$stmt->close();

$query = "SELECT team_name, city, arena, conference, division FROM team WHERE teamID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($team_name1, $city1, $arena1, $conference1, $division1);

if ($stmt->fetch()) {
    echo "<div class=\"center-title\">";
    echo "<h2 class=\"nhl-team\">$team_name1</h2>";
    echo "</div>";

    // display information in a table
    echo "<table class=\"team-details\">";
    echo "<tr>";
    echo "<td align=\"center\">" . $city1 . "</td>";
    echo "<td align=\"center\">" . $arena1 . "</td>";
    echo "<td align=\"center\">" . $conference1 . " Conference</td>";
    echo "<td class=\"far-right\" align=\"center\">" . $division1 . " Division</td>";
    echo "</tr>";
    echo "</table>";
}

$stmt->free_result();

$query_pos = "SELECT position FROM player INNER JOIN team on player.teamID = team.teamID WHERE playerID = $id";
$res_pos = $db->query($query_pos);
$row = $res_pos->fetch_row();
$player_position = "";
$player_position = $row[0];
$res_pos->free_result();

echo "<br /><br />";
echo "<br /><br />";

echo "<div class=\"show-team-stats\">";

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
$query_watchlist = "SELECT COUNT(1) FROM team_watchlist WHERE username='" . $_SESSION['username']
        . "' AND teamID='" . $_SESSION['teamID'] . "'";
    $res_list = $db->query($query_watchlist);
    $list_row = $res_list->fetch_row();

    echo "<div class=\"center\">";
    if ($list_row[0] >= 1) {
        echo "<a class=\"add-link\" href=\"removewatchlist_team.php\">Remove from Favourites</a>";
    } else {
        echo "<a class=\"add-link\" href=\"addtowatchlist_team.php\">Add to Favourites</a>";
    }
    $res_list->free_result();
    echo "</div>";
}

echo "<h3 class=\"current-team-stats\">Team Stats</h3>";

$query = "SELECT games_played, wins, losses, ot_losses, points, goals_for_per_game, goals_against_per_game, power_play_percent, penalty_kill_percent, shots_for_per_game, shots_against_per_game, faceoff_win_percent FROM team WHERE teamID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($games_played1, $wins1, $losses1, $ot_losses1, $points1, $goals_for_per_game1, $goals_against_per_game1, $power_play_percent1, $penalty_kill_percent1, $shots_for_per_game1, $shots_against_per_game1, $faceoff_win_percent1);

echo "<table class=\"show-team-stats\">";
echo "<tr>";
echo "<th>Games Played</th>";
echo "<th>Wins</th>";
echo "<th>Losses</th>";
echo "<th>OT Losses</th>";
echo "<th>Points</th>";
echo "<th>Goals per Game</th>";
echo "<th>Goals Allowed per Game</th>";
echo "<th>Powerplay Efficiency</th>";
echo "<th>Penalty Kill Efficiency</th>";
echo "<th>Shots per Game</th>";
echo "<th>Shots Allwed per Game</th>";
echo "<th>Faceoff Efficiency</th>";
echo "</tr>";

if ($stmt->fetch()) {
    echo "<td align=\"center\">" . $games_played1 . "</td>";
    echo "<td align=\"center\">" . $wins1 . "</td>";
    echo "<td align=\"center\">" . $losses1 . "</td>";
    echo "<td align=\"center\">" . $ot_losses1 . "</td>";
    echo "<td align=\"center\">" . $points1 . "</td>";
    echo "<td align=\"center\">" . $goals_for_per_game1 . "</td>";
    echo "<td align=\"center\">" . $goals_against_per_game1 . "</td>";
    echo "<td align=\"center\">" . $power_play_percent1 . "%</td>";
    echo "<td align=\"center\">" . $penalty_kill_percent1 . "%</td>";
    echo "<td align=\"center\">" . $shots_for_per_game1 . "</td>";
    echo "<td align=\"center\">" . $shots_against_per_game1 . "</td>";
    echo "<td align=\"center\">" . $faceoff_win_percent1 . "%</td>";
}
echo "</tr>";
echo "</table>";
echo "</div>";
$stmt->free_result();

$query = "SELECT player.playerID, player.name, player.position FROM player INNER JOIN team ON player.teamID = team.teamID WHERE player.teamID = $id ORDER BY player.name";
$res = $db->query($query);

echo "<div class=\"team-stats\">";
echo "<h3 class=\"current-team-stats\">Roster</h3>";

echo "<table class=\"show-team-stats\" id=\"bottom-space\">";
echo "<tr>";
echo "<th>Players</th>";
echo "<th>Position</th>";
echo "</tr>";
while ($row = $res->fetch_row()) {
    echo "<tr>";
    echo "<td align=\"center\">";
    format_name_as_link($row[0], $row[1], "details.php");
    echo "</td>";
    echo "<td align=\"center\">";
    echo $row[2];
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

$db->close();
?>
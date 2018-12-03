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

$id = trim($_GET['playerID']);

$query_pos = "SELECT position FROM player INNER JOIN team on player.teamID = team.teamID WHERE playerID = $id";
$res_pos = $db->query($query_pos);
$row = $res_pos->fetch_row();
$player_position = "";
$player_position = $row[0];
$res_pos->free_result();

echo "<br /><br />";

// update player data and stats
$queryPlayer = "INSERT INTO stats VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) "
        . "ON DUPLICATE KEY UPDATE games_played=?, goals=?, assists=?, "
        . "penalty_minutes=?, power_play_goals=?, power_play_points=?, "
        . "short_handed_goals=?, short_handed_points=?, game_winning_goals=?, "
        . "plus_minus=?, shots=?, overtime_goals=?";
$stmtPlayer = $db->prepare($queryPlayer);
$stmtPlayer->bind_param("sisiiiiiiiiiiiiiiiiiiiiiiii", $statID, $playerID, $season,
        $played, $goals, $assists, $pim, $ppGoals, $ppPoints, $shGoals,
        $shPoints, $gwGoals, $plusMinus, $shots, $otGoals, $played, $goals,
        $assists, $pim, $ppGoals, $ppPoints, $shGoals, $shPoints, $gwGoals,
        $plusMinus, $shots, $otGoals);

$queryGoalie = "INSERT INTO goalie_stats VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "
        . "?, ?, ?, ?) ON DUPLICATE KEY UPDATE games_played=?, starts=?, wins=?, "
        . "losses=?, ot_losses=?, shots_against=?, saves=?, goals_against=?, "
        . "save_percent=?, goals_against_average=?, shutouts=?";

$stmtGoalie = $db->prepare($queryGoalie);
$stmtGoalie->bind_param("sisiiiiiiiiiiiiiiiiiiiiii", $statID,
        $playerID, $season, $played, $starts, $wins, $losses, $ot, $shotsAgainst,
        $saves, $goalsAgainst, $savePercent, $goalsAgainstAverage, $shutouts,
        $played, $starts, $wins, $losses, $ot, $shotsAgainst, $saves,
        $goalsAgainst, $savePercent, $goalsAgainstAverage, $shutouts);

$url = $nhlAPI . '/api/v1/people/' . $id;
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
$currPlayer = curl_exec($ch);
curl_close($ch);
$player_array = json_decode($currPlayer, true);
foreach ($player_array["people"] as $person) {
    $playerID = $id;
    $teamID = $person["currentTeam"]["id"];
    $photo = "https://nhl.bamcontent.com/images/headshots/current/168x168/" . $id . ".jpg";

    if (!isset($person["primaryNumber"])) {
        $number = "NULL";
    } else {
        $number = $person["primaryNumber"];
    }

    $playerName = mysqli_real_escape_string($db, $person["fullName"]);
    $weight = $person["weight"];

    $original = ["'", "\""];
    $replace = [" feet", " inches"];
    $height = str_replace($original, $replace, $person["height"]);

    $nationality = $person["nationality"];
    $age = $person["currentAge"];
    $birthDate = $person["birthDate"];
    $position = $person["primaryPosition"]["abbreviation"];

    $url = $nhlAPI . '/api/v1/people/' . $id . "/stats?stats=statsSingleSeason&season=20182019";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $playerStatsSeason = curl_exec($ch);
    curl_close($ch);
    $playerStats_array = json_decode($playerStatsSeason, true);
    foreach ($playerStats_array["stats"][0]["splits"] as $seasonStats) {
        switch ($player_position) {
            case ('G'):
                $statID = $id . "-" . $seasonStats["season"];
                $playerID = $id;
                $season = $seasonStats["season"];
                $played = $seasonStats["stat"]["games"];
                $starts = $seasonStats["stat"]["gamesStarted"];
                $wins = $seasonStats["stat"]["wins"];
                $losses = $seasonStats["stat"]["losses"];
                $ot = $seasonStats["stat"]["ot"];
                $shotsAgainst = $seasonStats["stat"]["shotsAgainst"];
                $saves = $seasonStats["stat"]["saves"];
                $goalsAgainst = $seasonStats["stat"]["goalsAgainst"];
                $savePercent = $seasonStats["stat"]["savePercentage"];
                $goalsAgainstAverage = $seasonStats["stat"]["goalAgainstAverage"];
                $shutouts = $seasonStats["stat"]["shutouts"];

                $stmtGoalie->execute();
                break;
            default:
                $statID = $id . "-" . $seasonStats["season"];
                $playerID = $id;
                $season = $seasonStats["season"];
                $played = $seasonStats["stat"]["games"];
                $goals = $seasonStats["stat"]["goals"];
                $assists = $seasonStats["stat"]["assists"];
                $pim = $seasonStats["stat"]["penaltyMinutes"];
                $ppGoals = $seasonStats["stat"]["powerPlayGoals"];
                $ppPoints = $seasonStats["stat"]["powerPlayPoints"];
                $shGoals = $seasonStats["stat"]["shortHandedGoals"];
                $shPoints = $seasonStats["stat"]["shortHandedPoints"];
                $gwGoals = $seasonStats["stat"]["gameWinningGoals"];
                $plusMinus = $seasonStats["stat"]["plusMinus"];
                $shots = $seasonStats["stat"]["shots"];
                $otGoals = $seasonStats["stat"]["overTimeGoals"];

                $stmtPlayer->execute();
                break;
        }
    }
    unset($seasonStats);

    $url = $nhlAPI . '/api/v1/people/' . $id . "/stats?stats=careerRegularSeason";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $playerStatsCareer = curl_exec($ch);
    curl_close($ch);
    $careerStats_array = json_decode($playerStatsSeason, true);
    foreach ($careerStats_array["stats"][0]["splits"] as $careerStats) {
        switch ($player_position) {
            case ('G'):
                $statID = $id . "-Career";
                $playerID = $id;
                $season = $careerStats["season"];
                $played = $careerStats["stat"]["games"];
                $starts = $careerStats["stat"]["gamesStarted"];
                $wins = $careerStats["stat"]["wins"];
                $losses = $careerStats["stat"]["losses"];
                $ot = $careerStats["stat"]["ot"];
                $shotsAgainst = $careerStats["stat"]["shotsAgainst"];
                $saves = $careerStats["stat"]["saves"];
                $goalsAgainst = $careerStats["stat"]["goalsAgainst"];
                $savePercent = $careerStats["stat"]["savePercentage"];
                $goalsAgainstAverage = $careerStats["stat"]["goalAgainstAverage"];
                $shutouts = $careerStats["stat"]["shutouts"];

                $stmtGoalie->execute();
                break;
            default:
                $statID = $id . "-Career";
                $playerID = $id;
                $season = $careerStats["season"];
                $played = $careerStats["stat"]["games"];
                $goals = $careerStats["stat"]["goals"];
                $assists = $careerStats["stat"]["assists"];
                $pim = $careerStats["stat"]["penaltyMinutes"];
                $ppGoals = $careerStats["stat"]["powerPlayGoals"];
                $ppPoints = $careerStats["stat"]["powerPlayPoints"];
                $shGoals = $careerStats["stat"]["shortHandedGoals"];
                $shPoints = $careerStats["stat"]["shortHandedPoints"];
                $gwGoals = $careerStats["stat"]["gameWinningGoals"];
                $plusMinus = $careerStats["stat"]["plusMinus"];
                $shots = $careerStats["stat"]["shots"];
                $otGoals = $careerStats["stat"]["overTimeGoals"];

                $stmtPlayer->execute();
                break;
        }
    }
    unset($careerStats);
}
unset($person);
$stmtPlayer->close();
$stmtGoalie->close();

$query = "UPDATE player SET teamID=?, photo=?, number=?, name=?, weight=?, "
        . "height=?, age=?, position=? WHERE playerID=?";
$stmt = $db->prepare($query);
$stmt->bind_param('isisisisi', $teamID, $photo, $number, $playerName, $weight, $height, $age, $position, $id);
$stmt->execute();

// display player image
$query = "SELECT photo FROM player INNER JOIN team ON player.teamID = team.teamID WHERE playerID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($photo1);

echo "<div class=\"body\">";
echo "<div class=\"content\">";
if ($stmt->fetch()) {
    echo "<img class=\"player-picture\" src=\"$photo1\" alt=\"Player Photo\">";
}
$stmt->free_result();

$query = "SELECT name, team.team_name, weight, height, nationality, age, position FROM player INNER JOIN team ON player.teamID = team.teamID WHERE playerID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($name1, $team_name1, $weight1, $height1, $nationality1, $age1, $position1);

// display information in a table
echo "<table class=\"player-details\">";
// echo "<tr>";
// echo "<th>Name</th>";
// echo "<th>Team</th>";
// echo "<th>Weight</th>";
// echo "<th>Height</th>";
// echo "<th>Nationality</th>";
// echo "<th>Age</th>";
// echo "<th>Position</th>";
// echo "</tr>";

if ($stmt->fetch()) {
    echo "<td align=\"center\">" . $name1 . "</td>";
    echo "<td align=\"center\">" . $team_name1 . "</td>";
    echo "<td align=\"center\">" . $weight1 . " lbs.</td>";
    echo "<td align=\"center\">" . $height1 . "</td>";
    echo "<td align=\"center\">" . $nationality1 . "</td>";
    echo "<td align=\"center\">" . $age1 . "</td>";
    echo "<td class=\"far-right\" align=\"center\">" . $position1 . "</td>";
}

get_player($id, $name1);

echo "</tr>";
echo "</table>";
$stmt->free_result();

echo "<div class=\"stats\">";
echo "<h3 class=\"player-stats\">Player Stats</h3>";
switch ($player_position) {
    case ('G'):
        $query = "SELECT season, games_played, starts, wins, losses, ot_losses, shots_against, saves, goals_against, save_percent, goals_against_average, shutouts FROM goalie_stats INNER JOIN player ON goalie_stats.playerID = player.playerID WHERE goalie_stats.playerID = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($season1, $games_played1, $starts1, $wins1, $losses1, $ot_losses1, $shots_against1, $saves1, $goals_against1, $save_percent1, $goals_against_average1, $shutouts1);

        echo "<table class=\"show-stats\">";
        echo "<tr>";
        echo "<th>Season</th>";
        echo "<th>Games Played</th>";
        echo "<th>Starts</th>";
        echo "<th>Wins</th>";
        echo "<th>Losses</th>";
        echo "<th>Overtime Losses</th>";
        echo "<th>Shots Against</th>";
        echo "<th>Saves</th>";
        echo "<th>Goals Against</th>";
        echo "<th>Save Percentage</th>";
        echo "<th>Average Goals Against</th>";
        echo "<th>Shutouts</th>";
        echo "</tr>";

        while ($stmt->fetch()) {
            echo "<tr>";
            if ($season1 != "NHL Career") {
                echo "<td align=\"center\">" . substr_replace($season1, "-", 4, 0) . "</td>";
            } else {
                echo "<td align=\"center\">" . $season1 . "</td>";
            }
            echo "<td align=\"center\">" . $games_played1 . "</td>";
            echo "<td align=\"center\">" . $starts1 . "</td>";
            echo "<td align=\"center\">" . $wins1 . "</td>";
            echo "<td align=\"center\">" . $losses1 . "</td>";
            echo "<td align=\"center\">" . $ot_losses1 . "</td>";
            echo "<td align=\"center\">" . $shots_against1 . "</td>";
            echo "<td align=\"center\">" . $saves1 . "</td>";
            echo "<td align=\"center\">" . $goals_against1 . "</td>";
            echo "<td align=\"center\">" . $save_percent1 . "</td>";
            echo "<td align=\"center\">" . $goals_against_average1 . "</td>";
            echo "<td align=\"center\">" . $shutouts1 . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        $stmt->free_result();
        break;

    default :
        $query = "SELECT season, games_played, goals, assists, penalty_minutes, power_play_goals, power_play_points, short_handed_goals, short_handed_points, game_winning_goals, plus_minus, shots, overtime_goals FROM stats INNER JOIN player ON stats.playerID = player.playerID WHERE stats.playerID = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($season1, $games_played1, $goals1, $assists1, $penalty_minutes1, $power_play_goals1, $power_play_points1, $short_handed_goals1, $short_handed_points1, $game_winning_goals1, $plus_minus1, $shots1, $overtime_goals1);

        echo "<table class=\"show-stats\">";
        echo "<tr>";
        echo "<th>Season</th>";
        echo "<th>Games Played</th>";
        echo "<th>Goals</th>";
        echo "<th>Assists</th>";
        echo "<th>Penalty Minutes</th>";
        echo "<th>Power Play Goals</th>";
        echo "<th>Power Play Points</th>";
        echo "<th>Short Handed Goals</th>";
        echo "<th>Short Handed Points</th>";
        echo "<th>Game Winning Goal</th>";
        echo "<th>Plus/Minus</th>";
        echo "<th>Shots</th>";
        echo "<th>Overtime Goals</th>";
        echo "</tr>";

        while ($stmt->fetch()) {
            echo "<tr>";
            if ($season1 != "NHL Career") {
                echo "<td class=\"y-axis\" align=\"center\">" . substr_replace($season1, "-", 4, 0) . "</td>";
            } else {
                echo "<td class=\"y-axis\" align=\"center\">" . $season1 . "</td>";
            }
            echo "<td align=\"center\">" . $games_played1 . "</td>";
            echo "<td align=\"center\">" . $goals1 . "</td>";
            echo "<td align=\"center\">" . $assists1 . "</td>";
            echo "<td align=\"center\">" . $penalty_minutes1 . "</td>";
            echo "<td align=\"center\">" . $power_play_goals1 . "</td>";
            echo "<td align=\"center\">" . $power_play_points1 . "</td>";
            echo "<td align=\"center\">" . $short_handed_goals1 . "</td>";
            echo "<td align=\"center\">" . $short_handed_points1 . "</td>";
            echo "<td align=\"center\">" . $game_winning_goals1 . "</td>";
            echo "<td align=\"center\">" . $plus_minus1 . "</td>";
            echo "<td align=\"center\">" . $shots1 . "</td>";
            echo "<td align=\"center\">" . $overtime_goals1 . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        $stmt->free_result();
        break;
}
// echo "</div>";

get_player($id, $name1);

if (!empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
    $break = array();
    array_push($break, $_SESSION['fantasyTeamID']);
    $break = explode("_", $_SESSION['fantasyTeamID']);
}

echo "<br /><br />";
if (!empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID']) && $_SESSION['username'] == $break[1]) {
    echo "<div class=\"grid\">";
    echo "<div class=\"grid-form-1of3\">";
    echo "<a class=\"add-link\" href=\"userteam.php?fantasyTeamID=$fantasyTeamID\">Add to $title</a>";
    echo "</div>";
}

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    $sql_display_fantasy = "SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS "
            . "WHERE COLUMN_NAME IN ('team_title','team_author') AND TABLE_SCHEMA='nhl_stats'";
    $res = $db->query($sql_display_fantasy);

    echo "<div class=\"grid-form-1of3\">";
    echo "<div class=\"dropdown\">";
    echo "<button onclick='myFunction()' class='dropbtn'>Add to Fantasy Team</button>";
    echo "<div id='teams' class='dropdown-content'>";
    while ($row = $res->fetch_row()) {
        $sql_display = "SELECT DISTINCT team_title, fantasyTeamID FROM $row[0] "
                . "WHERE team_author='" . $_SESSION['username'] . "' AND "
                . "fantasyTeamID!='$fantasyTeamID'";
        $my_result = $db->query($sql_display);
        while ($subRow = $my_result->fetch_row()) {
            echo "<a class = \"add-link\" href=\"userteam.php?fantasyTeamID=$subRow[1]\">$subRow[0]</a>";
        }
    }
    mysqli_free_result($my_result);
    echo "</div>";
    echo "</div>";
    echo "</div>";


    $query_watchlist = "SELECT COUNT(1) FROM watchlist WHERE username='" . $_SESSION['username']
            . "' AND playerID='" . $_SESSION['playerID'] . "'";

    $res_list = $db->query($query_watchlist);
    $list_row = $res_list->fetch_row();

    echo "<div class=\"grid-form-1of3\">";
    if ($list_row[0] >= 1) {
        echo "<a class=\"add-link\" href=\"removewatchlist.php\">Remove from Favourites</a>";
    } else {
        echo "<a class=\"add-link\" href=\"addtowatchlist.php\">Add to Favourites</a>";
    }
    $res_list->free_result();
    echo "</div>";
} else {
    echo "<div class=\"grid-form-1of3\">";
    echo "<a class=\"add-link\" href=\"login.php\">Add to Favourites</a>";
    echo "</div>";
}

echo "</div>";
$db->close();
?>

<script>
    /* When the user clicks on the button,
     toggle between hiding and showing the dropdown content */
    function myFunction() {
        document.getElementById("teams").classList.toggle("show");
    }

// Close the dropdown if the user clicks outside of it
    window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {

            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>
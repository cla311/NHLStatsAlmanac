<style>
    .page {
        padding:7px 8px;
        border:1px solid #ccc;
        color:#333;
        font-weight:bold;
        font-family: Helvetica, Arial, sans-serif;
    }

    .toPage {
        margin-top: 1rem;
        font-family: Helvetica, Arial, sans-serif;
    }

    .toPage input{
        margin-left: 10px;
    }
</style>
<?php
session_start();
require('../required/functions.php');

$record_per_page = 25;
$page = '';
if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}

$start_from = ($page - 1) * $record_per_page;

$query_str = "SELECT DISTINCT ";

if (isset($_POST['team_name']) && $_POST ['team_name'] != "") {
    $team = $_POST['team_name'];
    $query_teamID = "SELECT teamID FROM team WHERE team_name = '" . $team . "'";

    $res = $db->query($query_teamID);
    $teamID = "";
    $row = $res->fetch_row();
    $teamID = $row[0];
    $res->free_result();

    echo "<br />";
    echo "Returning players belonging to the " . $team;

// get name to display link, use teamID to find players in database
    $query_str .= "player.playerID, name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE team.teamID = $teamID";
} else if (isset($_POST['city']) && $_POST ['city'] != "") {
    $city = $_POST['city'];
    echo "<br />";
    echo "Returning players belonging to the city of " . $city;

// get name to display link, use city name to find players in database
    $query_str .= "player.playerID, name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE team.city = '" . $city . "'";
} else if (isset($_POST['name']) && $_POST ['name'] != "") {
    $name = $_POST['name'];
    echo "<br />";
    echo "Returning players with name containing: '" . $name . "'";

    $query_str .= "player.playerID, player.name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE name LIKE '%" . $name . "%'";
} else {
    echo "<br />";
    echo "Returning all players";

    $query_str .= "player.playerID, player.name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE name LIKE '%%'";
}

if (isset($_POST['goals'])) {
    if (empty($_POST['minGoalAmount'])) {
        $enteredGoals = 0;
    } else {
        $enteredGoals = $_POST['minGoalAmount'];
    }
    $query_str .= " AND stats.goals > '" . $enteredGoals . "'";
}
if (isset($_POST['assists'])) {
    if (empty($_POST['minAssistAmount'])) {
        $enteredAssists = 0;
    } else {
        $enteredAssists = $_POST['minAssistAmount'];
    }
    $query_str .= " AND stats.assists > '" . $enteredAssists . "'";
}
if (isset($_POST['shots'])) {
    if (empty($_POST['minShotAmount'])) {
        $enteredShots = 0;
    } else {
        $enteredShots = $_POST['shots'];
    }
    $query_str .= " AND stats.shots > '" . $enteredShots . "'";
}
if (isset($_POST['gameWinningGoals'])) {
    if (empty($_POST['minGWGAmount'])) {
        $enteredGWG = 0;
    } else {
        $enteredGWG = $_POST['minGWGAmount'];
    }
    $query_str .= " AND stats.game_winning_goals > '" . $enteredGWG . "'";
}
if (isset($_POST['penaltyMinutes'])) {
    if (empty($_POST['minPenaltyAmount'])) {
        $enteredPenaltyMinutes = 0;
    } else {
        $enteredPenaltyMinutes = $_POST['minPenaltyAmount'];
    }
    $query_str .= " AND stats.penalty_minutes > '" . $enteredPenaltyMinutes . "'";
}
if (isset($_POST['gamesPlayed'])) {
    if (empty($_POST['minGamesAmount'])) {
        $enteredGames = 0;
    } else {
        $enteredGames = $_POST['minGamesAmount'];
    }
    $query_str .= " AND stats.games_played > '" . $enteredGames . "'";
}
if (isset($_POST['saves'])) {
    if (empty($_POST['minSavesAmount'])) {
        $enteredSaves = 0;
    } else {
        $enteredSaves = $_POST['minSavesAmount'];
    }
    $query_str .= " AND goalie_stats.saves > '" . $enteredSaves . "'";
}
if (isset($_POST['wins'])) {
    if (empty($_POST['minWinsAmount'])) {
        $enteredWins = 0;
    } else {
        $enteredWins = $_POST['minWinsAmount'];
    }
    $query_str .= " AND goalie_stats.wins > '" . $enteredWins . "'";
}
if (isset($_POST['losses'])) {
    if (empty($_POST['minLossAmount'])) {
        $enteredLosses = 0;
    } else {
        $enteredLosses = $_POST['minLossAmount'];
    }
    $query_str .= " AND goalie_stats.losses > '" . $enteredLosses . "'";
}

$query_str .= " ORDER BY name";

$get_players = $query_str . " LIMIT $start_from, $record_per_page";

$res_players = $db->query($get_players);

echo "<br /><br />";
echo "<ul>";
while ($row = $res_players->fetch_row()) {
    echo "<li>";
    format_name_as_link($row[0], $row[1], "details.php");
    echo "</li>\n";
};
echo "</ul><br />";

$res = $db->query($query_str);
$rowCount = $res->num_rows;

if ($rowCount > $record_per_page) {
    $total_pages = ceil($rowCount / $record_per_page);
    $start_loop = $page;
    $difference = $total_pages - $page;
    if ($difference <= 4) {
        $start_loop = $total_pages - 4;
    }
    $end_loop = $start_loop + 4;
    if ($page > 1) {
        echo "<a class='page' href='searchPlayer.php?page=1'>First</a>";
        echo "<a class='page' href='searchPlayer.php?page=" . ($page - 1) . "'><<</a>";
    }
    for ($i = $start_loop; $i <= $end_loop; $i++) {
        echo "<a class='page' href='searchPlayer.php?page=" . $i . "'>" . $i . "</a>";
    }
    if ($page < $end_loop) {
        echo "<a class='page' href='searchPlayer.php?page=" . ($page + 1) . "'>>></a>";
        echo "<a class='page' href='searchPlayer.php?page=" . $total_pages . "'>Last</a>";
    }

    echo "<form class='toPage' name='goToPage'>";
    echo "<select id='pages' name='pages'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<option value=$i ";
        if ($page == $i) {
            echo "selected";
        }
        echo ">$i</option>";
    }
    echo "</select>";
    echo "<input type='submit' name='submit'>";
    echo "</form>";
}

$res->free_result();
$db->close();
?>
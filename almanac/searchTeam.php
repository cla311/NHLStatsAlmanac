<?php

session_start();
require('../required/functions.php');

$query_str = "SELECT DISTINCT ";

if (isset($_POST['team_title']) && $_POST['team_title'] != "") {
    $team = $_POST['team_title'];
    $query_teamID = "SELECT teamID FROM team WHERE team_name = '" . $team . "'";
    $res = $db->query($query_teamID);
    $teamID = "";
    $row = $res->fetch_row();
    $teamID = $row[0];
    $res->free_result();

    echo "<br />";
    echo "Returning teams belonging to '" . $team . "' ";

// get name to display link, use teamID to find players in database
    $query_str .= "teamID, team_name FROM team WHERE teamID = $teamID";
} else if (isset($_POST['team_city']) && $_POST['team_city'] != "") {
    $city = $_POST['team_city'];
    echo "<br />";
    echo "Returning players belonging to the city of " . $city;

// get name to display link, use city name to find players in database
    $query_str .= "teamID, team_name FROM team WHERE team.city = '" . $city . "'";
} else if (isset($_POST['team_name_title']) && $_POST['team_name_title'] != "") {
    $name = $_POST['team_name_title'];
    echo "<br />";
    echo "Returning teams with name containing: '" . $name . "'";

    $query_str .= "teamID, team_name FROM team WHERE team_name LIKE '%" . $name . "%'";
} else {
    echo "<br />";
    echo "Returning all teams";

    $query_str .= "teamID, team_name FROM team WHERE team_name LIKE '%%'";
}

if (isset($_POST['team_wins'])) {
    if (empty($_POST['team_minWinAmount'])) {
        $enteredWins = 0;
    } else {
        $enteredWins = $_POST['team_minWinAmount'];
    }
    $query_str .= " AND wins > '" . $enteredWins . "'";
}
if (isset($_POST['team_losses'])) {
    if (empty($_POST['team_minLossAmount'])) {
        $enteredLosses = 0;
    } else {
        $enteredLosses = $_POST['team_minLossAmount'];
    }
    $query_str .= " AND losses > '" . $enteredLosses . "'";
}
if (isset($_POST['team_otloss'])) {
    if (empty($_POST['team_minOTLossAmount'])) {
        $enteredOTLosses = 0;
    } else {
        $enteredOTLosses = $_POST['team_minOTLossAmount'];
    }
    $query_str .= " AND ot_losses > '" . $enteredOTLosses . "'";
}
if (isset($_POST['team_points'])) {
    if (empty($_POST['team_minPointsAmount'])) {
        $enteredPoints = 0;
    } else {
        $enteredPoints = $_POST['team_minPointsAmount'];
    }
    $query_str .= " AND points > '" . $enteredPoints . "'";
}
if (isset($_POST['team_goalsPerGame'])) {
    if (empty($_POST['team_minGoalPerAmount'])) {
        $enteredGoalsPerGame = 0;
    } else {
        $enteredGoalsPerGame = $_POST['team_minGoalPerAmount'];
    }
    $query_str .= " AND goals_for_per_game > '" . $enteredGoalsPerGame . "'";
}
if (isset($_POST['team_goalsAllowed'])) {
    if (empty($_POST['team_minGoalsAllowedAmount'])) {
        $enteredGoalsAllowedPerGame = 0;
    } else {
        $enteredGoalsAllowedPerGame = $_POST['team_minGoalsAllowedAmount'];
    }
    $query_str .= " AND goals_against_per_game > '" . $enteredGoalsAllowedPerGame . "'";
}
if (isset($_POST['team_shotsPerGame'])) {
    if (empty($_POST['team_minShotsPerAmount'])) {
        $enteredShotsPerGame = 0;
    } else {
        $enteredShotsPerGame = $_POST['team_minShotsPerAmount'];
    }
    $query_str .= " AND shots_for_per_game > '" . $enteredShotsPerGame . "'";
}
if (isset($_POST['team_shotsAllowedPerGame'])) {
    if (empty($_POST['team_minShotsAllowedAmount'])) {
        $enteredShotsAllowedPerGame = 0;
    } else {
        $enteredShotsAllowedPerGame = $_POST['team_minShotsAllowedAmount'];
    }
    $query_str .= " AND shots_against_per_game > '" . $enteredShotsAllowedPerGame . "'";
}
if (isset($_POST['team_faceoffPercent'])) {
    if (empty($_POST['team_minFaceoffAmount'])) {
        $enteredFaceoffWin = 0;
    } else {
        $enteredFaceoffWin = $_POST['team_minFaceoffAmount'];
    }
    $query_str .= " AND faceoff_win_percent > '" . $enteredFaceoffWin . "'";
}

$query_str .= " ORDER BY team_name";
$res = $db->query($query_str);
echo mysqli_error($db);
echo "<br /><br />";
echo "<ul>";
while ($row = $res->fetch_row()) {
    echo "<li>";
    format_name_as_link_team($row[0], $row[1], "teamdetails.php");
    echo "</li>\n";
};
echo "</ul>";

$res->free_result();
$db->close();
?>
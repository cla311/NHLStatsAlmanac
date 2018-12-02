<?php

session_start();
require('../required/nav.php');
require('../required/functions.php');

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    $email = $_SESSION['user_email'];
    $firstName = $_SESSION['firstName'];
    $username = $_SESSION['username'];
    echo "1";
} else {
    $email = $_SESSION['user_email'] = [];
    $firstName = $_SESSION['firstName'] = [];
    $username = $_SESSION['username'] = [];
}

if (!empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
    $title = $_SESSION['team_title'];
    $fantasyTeamID = $_SESSION['fantasyTeamID'];
    echo "2";
} else {
    $title = $_SESSION['team_title'] = [];
    $fantasyTeamID = $_SESSION['fantasyTeamID'] = [];
}

if (!empty($_SESSION['playerID']) && !empty($_SESSION['name'])) {
    $playerID = $_SESSION['playerID'];
    $playerName = $_SESSION['name'];
    echo "3";
} else {
    $playerID = $_SESSION['playerID'] = [];
    $playerName = $_SESSION['name'] = [];
}
?>

<<<<<<< HEAD
<<<<<<< HEAD
<?php // require_login(); // if not logged in, redirect to login page  ?>
=======
<?php require_login(); // if not logged in, redirect to login page   ?>
>>>>>>> 5894542464eb510e7fb2b824a0caac155b18dc96
=======
<?php require_login(); // if not logged in, redirect to login page   ?>
>>>>>>> 5894542464eb510e7fb2b824a0caac155b18dc96

<?php

$teamID = trim($_GET['fantasyTeamID']);

$team_title = array();
array_push($team_title, $teamID);
$team_title = explode("_", $teamID);

$_SESSION['fantasyTeamID'] = $teamID;
$_SESSION['team_title'] = $team_title[0];

// insert player into fantasy team table
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    if (!empty($_SESSION['playerID']) && !empty($_SESSION['name']) && !empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
        if ((strpos($_SESSION['username'], $team_title[1])) !== false) {
            $sql_insert_player = "INSERT INTO $fantasyTeamID VALUES ('" . $teamID . "','" . $username . "','" . $playerID . "','" . $title[0] . "')";
            $res = $db->query($sql_insert_player);

            // unset session values
            unset($_SESSION['playerID']);
            unset($_SESSION['name']);
            echo "player added to fantasy team";
        } else {
            // redirect_to("fantasy.php");
        }
    }
}

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    if ((strpos($_SESSION['username'], $team_title[1])) !== false) {
        //plalyer removal
        $current_link = "userteam.php?fantasyTeamID=" . $team_title[0] . "_" . $team_title[1]; // get current link

        $query = "SELECT $teamID.playerID, player.name, player.position FROM $teamID INNER JOIN player ON $teamID.playerID = player.playerID WHERE $teamID.playerID = player.playerID";
        // echo $query;
        $res = $db->query($query);

        echo "<div class=\"body\">";
        echo "<div class=\"content\">";
        echo "<h3 class=\"fantasy-title\">" . $team_title[0] . "</h3>";
        echo "<p class=\"author\">Created by " . $team_title[1] . "</p>";

        echo "<form action=\"$current_link\" method=\"post\">";
        echo "<table class=\"fantasy-roster\">";
        echo "<tr>";
        echo "<th>Players</th>";
        echo "<th>Position</th>";
        echo "</tr>";
        while ($row = $res->fetch_row()) {
            echo "<tr>";
            echo "<td>";
            echo "<input type=\"checkbox\" name=\"selected[]\" value=\"$row[0]\" />";
            echo " ";
            format_name_as_link($row[0], $row[1], "details.php");
            echo "</td>";
            echo "<td align=\"center\">";
            echo $row[2];
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div class=\"center-button\">";
        echo "<br />";
        echo "<input type=\"submit\" name=\"delete\" value=\"Remove Players\">";
        echo "</div>";
        echo "</form>";
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
    } else {
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
    }
} else {
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
}

// if players were removed
if (isset($_POST['delete'])) {
    echo "<meta http-equiv='refresh' content='0'>"; // refresh page with updated database table

    if(empty($_POST['selected'])) {
        // No items checked
        // don't do anything
    }
    else {
        foreach($_POST['selected'] as $player) { // remove selected players from table
            // delete the item with the id $player
            $sql_delete_players = "DELETE FROM " . $team_title[0] . "_" . $team_title[1] . " WHERE playerID = $player";
            $res_delete = $db->query($sql_delete_players);
            echo $sql_delete_players;
        }
    }
}

$db->close();
?>
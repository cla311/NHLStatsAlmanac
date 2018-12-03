<?php
session_start();
require('../required/nav.php');
require('../required/functions.php');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '960M');

$team = "";
$city = "";
$name = "";

if (!isset($_POST['submit']) && !isset($_POST['search'])) {
    $query = "INSERT IGNORE INTO player VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iisisississ", $playerID, $teamID, $photo,
            $number, $playerName, $weight, $height, $nationality, $age,
            $birthDate, $position);

    $teams = file_get_contents($nhlAPI . '/api/v1/teams');
    $teams_array = json_decode($teams, true);
    $team_ids = array();
    foreach ($teams_array["teams"] as $row) {
        array_push($team_ids, $row['id']);
    }
    unset($row);

    foreach ($team_ids as $ids) {
        $url = $nhlAPI . '/api/v1/teams/' . $ids . "/roster";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $team_roster = curl_exec($ch);
        curl_close($ch);
        $teamRoster_array = json_decode($team_roster, true);

        foreach ($teamRoster_array["roster"] as $player) {
            $url = $nhlAPI . '/api/v1/people/' . $player["person"]["id"];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $currPlayer = curl_exec($ch);
            curl_close($ch);
            $player_array = json_decode($currPlayer, true);
            foreach ($player_array["people"] as $person) {
                $playerID = $person["id"];
                $teamID = $ids;
                $photo = "https://nhl.bamcontent.com/images/headshots/current/168x168/" . $person["id"] . ".jpg";

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

                if (!isset($person["nationality"])) {
                    $nationality = "N/A";
                } else {
                    $nationality = $person["nationality"];
                }

                $age = $person["currentAge"];
                $birthDate = $person["birthDate"];
                $position = $person["primaryPosition"]["abbreviation"];

                $stmt->execute();
            }
        }
    }
    $stmt->close();
}
?>

<body>
    <?php
    if (isset($_SESSION["addPlayer"])) {
        echo "<div class=\"center-block-p\">";
        echo "<p class=\"added-player\">" . $_SESSION['addPlayer'] . "</p>";
        echo "</div>";
    }
    ?>
    <h1 class="search">Database Search</h1>

    <div class="grid">
        <div class="grid-col-1of2">
            <form name="player" action="lookup.php" method="post">

                <h3>Select Player Parameters</h3>

                <p>By Team:</p>

                <?php
                // sql for filling drop down with order numbers from database
                $sql = "SELECT team_name FROM team ORDER BY team_name";
                $teamDropDownResult = mysqli_query($db, $sql);

                // drop down with order numbers from database
                echo "<select name =\"team_name\">";

                // first value of drop down is empty
                echo "<option value=\"\"></option>";

                // fill drop down with order numbers from database
                while ($row = mysqli_fetch_array($teamDropDownResult)) {
                    ?>
                    <option value="<?php echo $row['team_name']; ?>" <?php if (isset($_POST['team_name']) && $_POST ['team_name'] != "" && $_POST ['team_name'] == $row ['team_name']) echo " selected"; ?> > <?php echo $row['team_name'] ?> </option>;
                    <?php
                }
                echo "</select>";

                mysqli_free_result($teamDropDownResult);
                ?>

                <br /><br />
                <p>OR</p>
                <br />

                <p>By City:</p>

                <?php
// sql for filling drop down with city from database
                $sql = "SELECT DISTINCT city FROM team ORDER BY city";
                $cityDropDownResult = mysqli_query($db, $sql);

                // drop down with city from database
                echo "<select name =\"city\">";

// first value of drop down is empty
                echo "<option value=\"\"></option>";

// fill drop down with city from database
                while ($row = mysqli_fetch_array($cityDropDownResult)) {
                    ?>
                    <option value="<?php echo $row['city']; ?>" <?php if (isset($_POST['city']) && $_POST ['city'] != "" && $_POST ['city'] == $row ['city']) echo " selected"; ?> > <?php echo $row['city'] ?> </option>;
                    <?php
                }
                echo "</select>";

                mysqli_free_result($cityDropDownResult);
                ?>

                <br /><br />
                <p>OR</p>
                <br />

                <p>By Player:</p>
                Search: <input type="text" name="name" value="<?php if (isset($_POST['name'])) echo htmlentities($_POST['name']); ?>" />
                </td>

                <td>
                    <?php
                    $enteredGoals = 0;
                    $enteredAssists = 0;
                    $enteredShots = 0;
                    $enteredGWG = 0;
                    $enteredPenaltyMinutes = 0;
                    $enteredGames = 0;
                    $enteredSaves = 0;
                    $enteredWins = 0;
                    $enteredLosses = 0;
                    ?>

                    <h4>Secondary Filters</h4>
                    <input type="checkbox" name="goals" value="goalNum" <?php if (isset($_POST['goals'])) echo "checked=\"checked\""; ?> />
                    Goals Greater than: <input type="number" name="minGoalAmount" value="<?php
                    if (isset($_POST['minGoalAmount'])) {
                        echo htmlentities($_POST['minGoalAmount']);
                        $enteredGoals = $_POST['minGoalAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="assists" value="assistNum" <?php if (isset($_POST['assists'])) echo "checked=\"checked\""; ?> />
                    Assists Greater than: <input type="number" name="minAssistAmount" value="<?php
                    if (isset($_POST['minAssistAmount'])) {
                        echo htmlentities($_POST['minAssistAmount']);
                        $enteredAssists = $_POST['minAssistAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="shots" value="shotNum" <?php if (isset($_POST['shots'])) echo "checked=\"checked\""; ?> />
                    Shots Greater than: <input type="number" name="minShotAmount" value="<?php
                    if (isset($_POST['minShotAmount'])) {
                        echo htmlentities($_POST['minShotAmount']);
                        $enteredShots = $_POST['minShotAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="gameWinningGoals" value="gameWinningNum" <?php if (isset($_POST['gameWinningGoals'])) echo "checked=\"checked\""; ?> />
                    Game Winning Goals greater than: <input type="number" name="minGWGAmount" value="<?php
                    if (isset($_POST['minGWGAmount'])) {
                        echo htmlentities($_POST['minGWGAmount']);
                        $enteredGWG = $_POST['minGWGAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="penaltyMinutes" value="penaltyNum" <?php if (isset($_POST['penaltyMinutes'])) echo "checked=\"checked\""; ?> />
                    Penalty Minutes greater than: <input type="number" name="minPenaltyAmount" value="<?php
                    if (isset($_POST['minPenaltyAmount'])) {
                        echo htmlentities($_POST['minPenaltyAmount']);
                        $enteredPenaltyMinutes = $_POST['minPenaltyAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="gamesPlayed" value="gamesNum" <?php if (isset($_POST['gamesPlayed'])) echo "checked=\"checked\""; ?> />
                    Games Played greater than: <input type="number" name="minGamesAmount" value="<?php
                    if (isset($_POST['minGamesAmount'])) {
                        echo htmlentities($_POST['minGamesAmount']);
                        $enteredGames = $_POST['minGamesAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="saves" value="savesNum" <?php if (isset($_POST['saves'])) echo "checked=\"checked\""; ?> />
                    Saves greater than: <input type="number" name="minSavesAmount" value="<?php
                    if (isset($_POST['minSavesAmount'])) {
                        echo htmlentities($_POST['minSavesAmount']);
                        $enteredSaves = $_POST['minSavesAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="wins" value="winsNum" <?php if (isset($_POST['wins'])) echo "checked=\"checked\""; ?> />
                    Wins greater than: <input type="number" name="minWinsAmount" value="<?php
                    if (isset($_POST['minWinsAmount'])) {
                        echo htmlentities($_POST['minWinsAmount']);
                        $enteredWins = $_POST['minWinsAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="losses" value="lossesNum" <?php if (isset($_POST['losses'])) echo "checked=\"checked\""; ?> />
                    Losses greater than: <input type="number" name="minLossAmount" value="<?php
                    if (isset($_POST['minLossAmount'])) {
                        echo htmlentities($_POST['minLossAmount']);
                        $enteredLosses = $_POST['minLossAmount'];
                    }
                    ?>"/><br>
                    <br>

                    <input type="submit" name="submit">
            </form>

            <?php
            if (isset($_POST['submit'])) {
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
                    if (isset($_POST['goals'])) {
                        if (empty($_POST['minGoalAmount'])) {
                            $enteredGoals = 0;
                        }
                        $query_str .= " AND stats.goals > " . $enteredGoals;
                    }
                    if (isset($_POST['assists'])) {
                        if (empty($_POST['minAssistAmount'])) {
                            $enteredAssists = 0;
                        }
                        $query_str .= " AND stats.assists > " . $enteredAssists;
                    }
                    if (isset($_POST['shots'])) {
                        if (empty($_POST['minShotAmount'])) {
                            $enteredShots = 0;
                        }
                        $query_str .= " AND stats.shots > " . $enteredShots;
                    }
                    if (isset($_POST['gameWinningGoals'])) {
                        if (empty($_POST['minGWGAmount'])) {
                            $enteredGWG = 0;
                        }
                        $query_str .= " AND stats.game_winning_goals > " . $enteredGWG;
                    }
                    if (isset($_POST['penaltyMinutes'])) {
                        if (empty($_POST['minPenaltyAmount'])) {
                            $enteredPenaltyMinutes = 0;
                        }
                        $query_str .= " AND stats.penalty_minutes > " . $enteredPenaltyMinutes;
                    }
                    if (isset($_POST['gamesPlayed'])) {
                        if (empty($_POST['minGamesAmount'])) {
                            $enteredGames = 0;
                        }
                        $query_str .= " AND stats.games_played > " . $enteredGames;
                    }
                    if (isset($_POST['saves'])) {
                        if (empty($_POST['minSavesAmount'])) {
                            $enteredSaves = 0;
                        }
                        $query_str .= " AND goalie_stats.saves > " . $enteredSaves;
                    }
                    if (isset($_POST['wins'])) {
                        if (empty($_POST['minWinsAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND goalie_stats.wins > " . $enteredWins;
                    }
                    if (isset($_POST['losses'])) {
                        if (empty($_POST['minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND goalie_stats.losses > " . $enteredLosses;
                    }

                    $query_str .= " ORDER BY name";

                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link($row[0], $row[1], "details.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                } else if (isset($_POST['city']) && $_POST ['city'] != "") {
                    $city = $_POST['city'];
                    echo "<br />";
                    echo "Returning players belonging to the city of " . $city;

// get name to display link, use city name to find players in database
                    $query_str .= "player.playerID, name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE team.city = '" . $city . "'";
                    if (isset($_POST['goals'])) {
                        if (empty($_POST['minGoalAmount'])) {
                            $enteredGoals = 0;
                        }
                        $query_str .= " AND stats.goals > " . $enteredGoals;
                    }
                    if (isset($_POST['assists'])) {
                        if (empty($_POST['minAssistAmount'])) {
                            $enteredAssists = 0;
                        }
                        $query_str .= " AND stats.assists > " . $enteredAssists;
                    }
                    if (isset($_POST['shots'])) {
                        if (empty($_POST['minShotAmount'])) {
                            $enteredShots = 0;
                        }
                        $query_str .= " AND stats.shots > " . $enteredShots;
                    }
                    if (isset($_POST['gameWinningGoals'])) {
                        if (empty($_POST['minGWGAmount'])) {
                            $enteredGWG = 0;
                        }
                        $query_str .= " AND stats.game_winning_goals > " . $enteredGWG;
                    }
                    if (isset($_POST['penaltyMinutes'])) {
                        if (empty($_POST['minPenaltyAmount'])) {
                            $enteredPenaltyMinutes = 0;
                        }
                        $query_str .= " AND stats.penalty_minutes > " . $enteredPenaltyMinutes;
                    }
                    if (isset($_POST['gamesPlayed'])) {
                        if (empty($_POST['minGamesAmount'])) {
                            $enteredGames = 0;
                        }
                        $query_str .= " AND stats.games_played > " . $enteredGames;
                    }
                    if (isset($_POST['saves'])) {
                        if (empty($_POST['minSavesAmount'])) {
                            $enteredSaves = 0;
                        }
                        $query_str .= " AND goalie_stats.saves > " . $enteredSaves;
                    }
                    if (isset($_POST['wins'])) {
                        if (empty($_POST['minWinsAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND goalie_stats.wins > " . $enteredWins;
                    }
                    if (isset($_POST['losses'])) {
                        if (empty($_POST['minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND goalie_stats.losses > " . $enteredLosses;
                    }

                    $query_str .= " ORDER BY name";

                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link($row[0], $row[1], "details.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                } else if (isset($_POST['name']) && $_POST ['name'] != "") {
                    $name = $_POST['name'];
                    echo "<br />";
                    echo "Returning players with name containing: '" . $name . "'";

                    $query_str .= "player.playerID, player.name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE name LIKE '%" . $name . "%'";
                    if (isset($_POST['goals'])) {
                        if (empty($_POST['minGoalAmount'])) {
                            $enteredGoals = 0;
                        }
                        $query_str .= " AND stats.goals > " . $enteredGoals;
                    }
                    if (isset($_POST['assists'])) {
                        if (empty($_POST['minAssistAmount'])) {
                            $enteredAssists = 0;
                        }
                        $query_str .= " AND stats.assists > " . $enteredAssists;
                    }
                    if (isset($_POST['shots'])) {
                        if (empty($_POST['minShotAmount'])) {
                            $enteredShots = 0;
                        }
                        $query_str .= " AND stats.shots > " . $enteredShots;
                    }
                    if (isset($_POST['gameWinningGoals'])) {
                        if (empty($_POST['minGWGAmount'])) {
                            $enteredGWG = 0;
                        }
                        $query_str .= " AND stats.game_winning_goals > " . $enteredGWG;
                    }
                    if (isset($_POST['penaltyMinutes'])) {
                        if (empty($_POST['minPenaltyAmount'])) {
                            $enteredPenaltyMinutes = 0;
                        }
                        $query_str .= " AND stats.penalty_minutes > " . $enteredPenaltyMinutes;
                    }
                    if (isset($_POST['gamesPlayed'])) {
                        if (empty($_POST['minGamesAmount'])) {
                            $enteredGames = 0;
                        }
                        $query_str .= " AND stats.games_played > " . $enteredGames;
                    }
                    if (isset($_POST['saves'])) {
                        if (empty($_POST['minSavesAmount'])) {
                            $enteredSaves = 0;
                        }
                        $query_str .= " AND goalie_stats.saves > " . $enteredSaves;
                    }
                    if (isset($_POST['wins'])) {
                        if (empty($_POST['minWinsAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND goalie_stats.wins > " . $enteredWins;
                    }
                    if (isset($_POST['losses'])) {
                        if (empty($_POST['minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND goalie_stats.losses > " . $enteredLosses;
                    }

                    $query_str .= " ORDER BY name";

                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link($row[0], $row[1], "details.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                } else {
                    $query_str .= "player.playerID, player.name FROM player LEFT JOIN team ON player.teamID = team.teamID LEFT JOIN stats ON player.playerID = stats.playerID LEFT JOIN goalie_stats ON goalie_stats.playerID = player.playerID WHERE name LIKE '%%'";
                    if (isset($_POST['goals'])) {
                        if (empty($_POST['minGoalAmount'])) {
                            $enteredGoals = 0;
                        }
                        $query_str .= " AND stats.goals > " . $enteredGoals;
                    }
                    if (isset($_POST['assists'])) {
                        if (empty($_POST['minAssistAmount'])) {
                            $enteredAssists = 0;
                        }
                        $query_str .= " AND stats.assists > " . $enteredAssists;
                    }
                    if (isset($_POST['shots'])) {
                        if (empty($_POST['minShotAmount'])) {
                            $enteredShots = 0;
                        }
                        $query_str .= " AND stats.shots > " . $enteredShots;
                    }
                    if (isset($_POST['gameWinningGoals'])) {
                        if (empty($_POST['minGWGAmount'])) {
                            $enteredGWG = 0;
                        }
                        $query_str .= " AND stats.game_winning_goals > " . $enteredGWG;
                    }
                    if (isset($_POST['penaltyMinutes'])) {
                        if (empty($_POST['minPenaltyAmount'])) {
                            $enteredPenaltyMinutes = 0;
                        }
                        $query_str .= " AND stats.penalty_minutes > " . $enteredPenaltyMinutes;
                    }
                    if (isset($_POST['gamesPlayed'])) {
                        if (empty($_POST['minGamesAmount'])) {
                            $enteredGames = 0;
                        }
                        $query_str .= " AND stats.games_played > " . $enteredGames;
                    }
                    if (isset($_POST['saves'])) {
                        if (empty($_POST['minSavesAmount'])) {
                            $enteredSaves = 0;
                        }
                        $query_str .= " AND goalie_stats.saves > " . $enteredSaves;
                    }
                    if (isset($_POST['wins'])) {
                        if (empty($_POST['minWinsAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND goalie_stats.wins > " . $enteredWins;
                    }
                    if (isset($_POST['losses'])) {
                        if (empty($_POST['minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND goalie_stats.losses > " . $enteredLosses;
                    }

                    $query_str .= " ORDER BY name";

                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link($row[0], $row[1], "details.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                }
            }
            ?>
        </div>

        <!-- search for teams -->
        <div class="grid-col-1of2">
            <form name="team" action="lookup.php" method="post">
                <h3>Select Team Parameters</h3>

                <p>By Team:</p>

                <?php
// sql for filling drop down with order numbers from database
                $sql = "SELECT team_name FROM team ORDER BY team_name";
                $teamDropDownResult = mysqli_query($db, $sql);

// drop down with order numbers from database
                echo "<select name=\"team_title\">";

// first value of drop down is empty
                echo "<option value=\"\"></option>";

// fill drop down with teams from database
                while ($row = mysqli_fetch_array($teamDropDownResult)) {
                    ?>
                    <option value="<?php echo $row['team_name']; ?>" <?php if (isset($_POST['team_title']) && $_POST['team_title'] != "" && $_POST ['team_title'] == $row['team_name']) echo " selected"; ?> > <?php echo $row['team_name'] ?> </option>;
                    <?php
                }
                echo "</select>";

                mysqli_free_result($teamDropDownResult);
                ?>
                <br /><br />
                <p>OR</p>
                <br />

                <p>By City:</p>

                <?php
// sql for filling drop down with city from database
                $sql = "SELECT DISTINCT city FROM team ORDER BY city";
                $cityDropDownResult = mysqli_query($db, $sql);

// drop down with city from database
                echo "<select name=\"team_city\">";

// first value of drop down is empty
                echo "<option value=\"\"></option>";

// fill drop down with city from database
                while ($row = mysqli_fetch_array($cityDropDownResult)) {
                    ?>
                    <option value="<?php echo $row['city']; ?>" <?php if (isset($_POST['team_city']) && $_POST['team_city'] != "" && $_POST ['team_city'] == $row['city']) echo " selected"; ?> > <?php echo $row['city'] ?> </option>;
                    <?php
                }
                echo "</select>";

                mysqli_free_result($cityDropDownResult);
                ?>

                <br /><br />
                <p>OR</p>
                <br />

                <p>By Name:</p>
                Search: <input type="text" name="team_name_title" value="<?php if (isset($_POST['team_name_title'])) echo htmlentities($_POST['team_name_title']); ?>" />
                </td>

                <td>
                    <?php
                    $enteredWins = 0;
                    $enteredLosses = 0;
                    $enteredOTLosses = 0;
                    $enteredPoints = 0;
                    $enteredGoalsPerGame = 0;
                    $enteredGoalsAllowedPerGame = 0;
                    $enteredShotsPerGame = 0;
                    $enteredShotsAllowedPerGame = 0;
                    $enteredFaceoffWin = 0;
                    ?>

                    <h4>Secondary Filters</h4>
                    <input type="checkbox" name="team_wins" value="winNum" <?php if (isset($_POST['team_wins'])) echo "checked=\"checked\""; ?> />
                    Wins Greater than: <input type="number" name="team_minWinAmount" value="<?php
                    if (isset($_POST['team_minWinAmount'])) {
                        echo htmlentities($_POST['team_minWinAmount']);
                        $enteredWins = $_POST['team_minWinAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_losses" value="lossNum" <?php if (isset($_POST['team_losses'])) echo "checked=\"checked\""; ?> />
                    Losses Greater than: <input type="number" name="team_minLossAmount" value="<?php
                    if (isset($_POST['team_minLossAmount'])) {
                        echo htmlentities($_POST['team_minLossAmount']);
                        $enteredLosses = $_POST['team_minLossAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_otloss" value="otLossNum" <?php if (isset($_POST['team_otloss'])) echo "checked=\"checked\""; ?> />
                    OT Losses Greater than: <input type="number" name="team_minOTLossAmount" value="<?php
                    if (isset($_POST['team_minOTLossAmount'])) {
                        echo htmlentities($_POST['team_minOTLossAmount']);
                        $enteredOTLosses = $_POST['team_minOTLossAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_points" value="gamePointsNum" <?php if (isset($_POST['team_points'])) echo "checked=\"checked\""; ?> />
                    Points greater than: <input type="number" name="team_minPointsAmount" value="<?php
                    if (isset($_POST['team_minPointsAmount'])) {
                        echo htmlentities($_POST['team_minPointsAmount']);
                        $enteredPoints = $_POST['team_minPointsAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_goalsPerGame" value="goalsPerNum" <?php if (isset($_POST['team_goalsPerGame'])) echo "checked=\"checked\""; ?> />
                    Goals per Game greater than: <input type="number" name="team_minGoalPerAmount" value="<?php
                    if (isset($_POST['team_minGoalPerAmount'])) {
                        echo htmlentities($_POST['team_minGoalPerAmount']);
                        $enteredGoalsPerGame = $_POST['team_minGoalPerAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_goalsAllowed" value="goalsAllowedNum" <?php if (isset($_POST['team_goalsAllowed'])) echo "checked=\"checked\""; ?> />
                    Goals Allowed per Game greater than: <input type="number" name="team_minGoalsAllowedAmount" value="<?php
                    if (isset($_POST['team_minGoalsAllowedAmount'])) {
                        echo htmlentities($_POST['team_minGoalsAllowedAmount']);
                        $enteredGoalsAllowedPerGame = $_POST['team_minGoalsAllowedAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_shotsPerGame" value="shotsPerNum" <?php if (isset($_POST['team_shotsPerGame'])) echo "checked=\"checked\""; ?> />
                    Shots per Game greater than: <input type="number" name="team_minShotsPerAmount" value="<?php
                    if (isset($_POST['team_minShotsPerAmount'])) {
                        echo htmlentities($_POST['team_minShotsPerAmount']);
                        $enteredShotsPerGame = $_POST['team_minShotsPerAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_shotsAllowedPerGame" value="shotsAllowedNum" <?php if (isset($_POST['team_shotsAllowedPerGame'])) echo "checked=\"checked\""; ?> />
                    Shots Allowed per Game greater than: <input type="number" name="team_minShotsAllowedAmount" value="<?php
                    if (isset($_POST['team_minShotsAllowedAmount'])) {
                        echo htmlentities($_POST['team_minShotsAllowedAmount']);
                        $enteredShotsAllowedPerGame = $_POST['team_minShotsAllowedAmount'];
                    }
                    ?>"/><br>

                    <input type="checkbox" name="team_faceoffPercent" value="faceoffNum" <?php if (isset($_POST['team_faceoffPercent'])) echo "checked=\"checked\""; ?> />
                    Faceoff Win Percentage greater than: <input type="number" name="team_minFaceoffAmount" value="<?php
                    if (isset($_POST['team_minFaceoffAmount'])) {
                        echo htmlentities($_POST['team_minFaceoffAmount']);
                        $enteredFaceoffWin = $_POST['team_minFaceoffAmount'];
                    }
                    ?>"/><br>
                    <br>

                    <input type="submit" name="search">
            </form>

            <?php
            if (isset($_POST['search'])) {
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
                    if (isset($_POST['team_wins'])) {
                        if (empty($_POST['team_minWinAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND wins > " . $enteredWins;
                    }
                    if (isset($_POST['team_losses'])) {
                        if (empty($_POST['team_minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND losses > " . $enteredLosses;
                    }
                    if (isset($_POST['team_otloss'])) {
                        if (empty($_POST['team_minOTLossAmount'])) {
                            $enteredOTLosses = 0;
                        }
                        $query_str .= " AND ot_losses > " . $enteredOTLosses;
                    }
                    if (isset($_POST['team_points'])) {
                        if (empty($_POST['team_minPointsAmount'])) {
                            $enteredPoints = 0;
                        }
                        $query_str .= " AND points > " . $enteredPoints;
                    }
                    if (isset($_POST['team_goalsPerGame'])) {
                        if (empty($_POST['team_minGoalPerAmount'])) {
                            $enteredGoalsPerGame = 0;
                        }
                        $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
                    }
                    if (isset($_POST['team_goalsAllowed'])) {
                        if (empty($_POST['team_minGoalsAllowedAmount'])) {
                            $enteredGoalsAllowedPerGame = 0;
                        }
                        $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
                    }
                    if (isset($_POST['team_shotsPerGame'])) {
                        if (empty($_POST['team_minShotsPerAmount'])) {
                            $enteredShotsPerGame = 0;
                        }
                        $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
                    }
                    if (isset($_POST['team_shotsAllowedPerGame'])) {
                        if (empty($_POST['team_minShotsAllowedAmount'])) {
                            $enteredShotsAllowedPerGame = 0;
                        }
                        $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
                    }
                    if (isset($_POST['team_faceoffPercent'])) {
                        if (empty($_POST['team_minFaceoffAmount'])) {
                            $enteredFaceoffWin = 0;
                        }
                        $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
                    }

                    $query_str .= " ORDER BY team_name";
                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link_team($row[0], $row[1], "teamdetails.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                } else if (isset($_POST['team_city']) && $_POST['team_city'] != "") {
                    $city = $_POST['team_city'];
                    echo "<br />";
                    echo "Returning players belonging to the city of " . $city;

// get name to display link, use city name to find players in database
                    $query_str .= "teamID, team_name FROM team WHERE team.city = '" . $city . "'";
                    if (isset($_POST['team_wins'])) {
                        if (empty($_POST['team_minWinAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND wins > " . $enteredWins;
                    }
                    if (isset($_POST['team_losses'])) {
                        if (empty($_POST['team_minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND losses > " . $enteredLosses;
                    }
                    if (isset($_POST['team_otloss'])) {
                        if (empty($_POST['team_minOTLossAmount'])) {
                            $enteredOTLosses = 0;
                        }
                        $query_str .= " AND ot_losses > " . $enteredOTLosses;
                    }
                    if (isset($_POST['team_points'])) {
                        if (empty($_POST['team_minPointsAmount'])) {
                            $enteredPoints = 0;
                        }
                        $query_str .= " AND points > " . $enteredPoints;
                    }
                    if (isset($_POST['team_goalsPerGame'])) {
                        if (empty($_POST['team_minGoalPerAmount'])) {
                            $enteredGoalsPerGame = 0;
                        }
                        $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
                    }
                    if (isset($_POST['team_goalsAllowed'])) {
                        if (empty($_POST['team_minGoalsAllowedAmount'])) {
                            $enteredGoalsAllowedPerGame = 0;
                        }
                        $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
                    }
                    if (isset($_POST['team_shotsPerGame'])) {
                        if (empty($_POST['team_minShotsPerAmount'])) {
                            $enteredShotsPerGame = 0;
                        }
                        $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
                    }
                    if (isset($_POST['team_shotsAllowedPerGame'])) {
                        if (empty($_POST['team_minShotsAllowedAmount'])) {
                            $enteredShotsAllowedPerGame = 0;
                        }
                        $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
                    }
                    if (isset($_POST['team_faceoffPercent'])) {
                        if (empty($_POST['team_minFaceoffAmount'])) {
                            $enteredFaceoffWin = 0;
                        }
                        $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
                    }

                    $query_str .= " ORDER BY team_name";
                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link_team($row[0], $row[1], "teamdetails.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                } else if (isset($_POST['team_name_title']) && $_POST['team_name_title'] != "") {
                    $name = $_POST['team_name_title'];
                    echo "<br />";
                    echo "Returning teams with name containing: '" . $name . "'";

                    $query_str .= "teamID, team_name FROM team WHERE team_name LIKE '%" . $name . "%'";
                    if (isset($_POST['team_wins'])) {
                        if (empty($_POST['team_minWinAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND wins > " . $enteredWins;
                    }
                    if (isset($_POST['team_losses'])) {
                        if (empty($_POST['team_minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND losses > " . $enteredLosses;
                    }
                    if (isset($_POST['team_otloss'])) {
                        if (empty($_POST['team_minOTLossAmount'])) {
                            $enteredOTLosses = 0;
                        }
                        $query_str .= " AND ot_losses > " . $enteredOTLosses;
                    }
                    if (isset($_POST['team_points'])) {
                        if (empty($_POST['team_minPointsAmount'])) {
                            $enteredPoints = 0;
                        }
                        $query_str .= " AND points > " . $enteredPoints;
                    }
                    if (isset($_POST['team_goalsPerGame'])) {
                        if (empty($_POST['team_minGoalPerAmount'])) {
                            $enteredGoalsPerGame = 0;
                        }
                        $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
                    }
                    if (isset($_POST['team_goalsAllowed'])) {
                        if (empty($_POST['team_minGoalsAllowedAmount'])) {
                            $enteredGoalsAllowedPerGame = 0;
                        }
                        $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
                    }
                    if (isset($_POST['team_shotsPerGame'])) {
                        if (empty($_POST['team_minShotsPerAmount'])) {
                            $enteredShotsPerGame = 0;
                        }
                        $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
                    }
                    if (isset($_POST['team_shotsAllowedPerGame'])) {
                        if (empty($_POST['team_minShotsAllowedAmount'])) {
                            $enteredShotsAllowedPerGame = 0;
                        }
                        $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
                    }
                    if (isset($_POST['team_faceoffPercent'])) {
                        if (empty($_POST['team_minFaceoffAmount'])) {
                            $enteredFaceoffWin = 0;
                        }
                        $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
                    }

                    $query_str .= " ORDER BY team_name";

                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link_team($row[0], $row[1], "teamdetails.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                } else {
                    $query_str .= "teamID, team_name FROM team WHERE team_name LIKE '%%'";
                    if (isset($_POST['team_wins'])) {
                        if (empty($_POST['team_minWinAmount'])) {
                            $enteredWins = 0;
                        }
                        $query_str .= " AND wins > " . $enteredWins;
                    }
                    if (isset($_POST['team_losses'])) {
                        if (empty($_POST['team_minLossAmount'])) {
                            $enteredLosses = 0;
                        }
                        $query_str .= " AND losses > " . $enteredLosses;
                    }
                    if (isset($_POST['team_otloss'])) {
                        if (empty($_POST['team_minOTLossAmount'])) {
                            $enteredOTLosses = 0;
                        }
                        $query_str .= " AND ot_losses > " . $enteredOTLosses;
                    }
                    if (isset($_POST['team_points'])) {
                        if (empty($_POST['team_minPointsAmount'])) {
                            $enteredPoints = 0;
                        }
                        $query_str .= " AND points > " . $enteredPoints;
                    }
                    if (isset($_POST['team_goalsPerGame'])) {
                        if (empty($_POST['team_minGoalPerAmount'])) {
                            $enteredGoalsPerGame = 0;
                        }
                        $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
                    }
                    if (isset($_POST['team_goalsAllowed'])) {
                        if (empty($_POST['team_minGoalsAllowedAmount'])) {
                            $enteredGoalsAllowedPerGame = 0;
                        }
                        $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
                    }
                    if (isset($_POST['team_shotsPerGame'])) {
                        if (empty($_POST['team_minShotsPerAmount'])) {
                            $enteredShotsPerGame = 0;
                        }
                        $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
                    }
                    if (isset($_POST['team_shotsAllowedPerGame'])) {
                        if (empty($_POST['team_minShotsAllowedAmount'])) {
                            $enteredShotsAllowedPerGame = 0;
                        }
                        $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
                    }
                    if (isset($_POST['team_faceoffPercent'])) {
                        if (empty($_POST['team_minFaceoffAmount'])) {
                            $enteredFaceoffWin = 0;
                        }
                        $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
                    }

                    $query_str .= " ORDER BY team_name";
                    $res = $db->query($query_str);

                    echo "<br /><br />";
                    echo "<ul>";
                    while ($row = $res->fetch_row()) {
                        echo "<li>";
                        format_name_as_link_team($row[0], $row[1], "teamdetails.php");
                        echo "</li>\n";
                    };
                    echo "</ul>";

                    $res->free_result();
                }
            }
            $db->close();
            ?>
        </div>
    </div>
</body>
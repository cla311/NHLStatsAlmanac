<?php
require('../required/nav.php');
require('../required/functions.php');
?>

<?php
$team = "";
$city = "";
$name = "";
?>

<body>

    <h1>Database Search</h1>

    <form action="lookup.php" method="post">
        <table>
            <tr>
                <td>
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
                <option value="<?php echo $row['team_name']; ?>" <?php if (isset($_POST['team_name']) && $_POST['team_name'] != "" && $_POST['team_name'] == $team) echo " selected"; ?> > <?php echo $row['team_name'] ?> </option>;
                <?php
            }
            echo "</select>";

            mysqli_free_result($teamDropDownResult);
            ?>

            <p>or</p>

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
                <option value="<?php echo $row['city']; ?>" <?php if (isset($_POST['city']) && $_POST['city'] != "" && $_POST['city'] == $city) echo " selected"; ?> > <?php echo $row['city'] ?> </option>;
                <?php
            }
            echo "</select>";

            mysqli_free_result($cityDropDownResult);
            ?>

            <p>or</p>

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
                ?>

                <h3>Secondary Filters</h3>
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
            </td>
            </tr>
        </table>
        <br>

        <input type="submit" name="submit">
    </form>

    <?php
    if (isset($_POST['submit'])) { // if submit button was clicked
        $query_str = "SELECT DISTINCT ";

        if (isset($_POST['team_name']) && $_POST['team_name'] != "") {
            $team = $_POST['team_name'];
            $query_teamID = "SELECT teamID FROM team WHERE team_name = '" . $team . "'";

            $res = $db->query($query_teamID);
            $teamID = "";
            $row = $res->fetch_row();
            $teamID = $row[0];
            $res->free_result();

            echo $team;

            // get name to display link, use teamID to find players in database
            $query_str .= "player.playerID, name FROM player INNER JOIN team ON player.teamID = team.teamID INNER JOIN stats ON player.playerID = stats.playerID WHERE team.teamID = $teamID";
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

            $query_str .= " ORDER BY name";

            echo "<br />" . $query_str;
            $res = $db->query($query_str);

            echo "<ul>";
            while ($row = $res->fetch_row()) {
                echo "<li>";
                format_name_as_link($row[0], $row[1], "details.php"); // link shows product name, but is identified by it's product code
                echo "</li>\n";
            };
            echo "</ul>";

            $res->free_result();
        } else if (isset($_POST['city']) && $_POST['city'] != "") {
            $city = $_POST['city'];
            echo $city;

            // get name to display link, use city name to find players in database
            $query_str .= "player.playerID, name FROM player INNER JOIN team ON player.teamID = team.teamID INNER JOIN stats ON player.playerID = stats.playerID WHERE team.city = '" . $city . "'";
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

            $query_str .= " ORDER BY name";

            echo "<br />" . $query_str;
            $res = $db->query($query_str);

            echo "<ul>";
            while ($row = $res->fetch_row()) {
                echo "<li>";
                format_name_as_link($row[0], $row[1], "details.php"); // link shows product name, but is identified by it's product code
                echo "</li>\n";
            };
            echo "</ul>";

            $res->free_result();
        } else if (isset($_POST['name']) && $_POST['name'] != "") {
            $name = $_POST['name'];
            echo "Returning players with name containing: '" . $name . "'";

            $query_str .= "player.playerID, player.name FROM player INNER JOIN stats ON player.playerID = stats.playerID WHERE name LIKE '%" . $name . "%'";
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

            $query_str .= " ORDER BY name";

            echo "<br />" . $query_str;
            $res = $db->query($query_str);

            echo "<ul>";
            while ($row = $res->fetch_row()) {
                echo "<li>";
                format_name_as_link($row[0], $row[1], "details.php"); // link shows product name, but is identified by it's product code
                echo "</li>\n";
            };
            echo "</ul>";

            $res->free_result();
        }
    }
    $db->close();
    ?>
</body>
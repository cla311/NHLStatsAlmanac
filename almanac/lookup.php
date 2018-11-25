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

    <form name="player" action="lookup.php" method="post">
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
        } else {
          $query_str .= "player.playerID, player.name FROM player INNER JOIN stats ON player.playerID = stats.playerID WHERE name LIKE '%%'";
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
    ?>

    <form name="team" action="lookup.php" method="post">
        <table>
            <tr>
                <td>
                    <h3>Select Team Parameters</h3>

                    <p>By Team:</p>

                    <?php

                    // sql for filling drop down with order numbers from database
                    $sql = "SELECT team_name FROM team ORDER BY team_name";
                    $teamDropDownResult = mysqli_query($db, $sql);

                    // drop down with order numbers from database
                    echo "<select name =\"team_name\">";

                    // first value of drop down is empty
                    echo "<option value=\"\"></option>";

                    // fill drop down with teams from database
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

            <p>By Name:</p>
            Search: <input type="text" name="name" value="<?php if (isset($_POST['name'])) echo htmlentities($_POST['name']); ?>" />
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
                $enteredGames = 0;
                ?>

                <h3>Secondary Filters</h3>
                <input type="checkbox" name="wins" value="winNum" <?php if (isset($_POST['win'])) echo "checked=\"checked\""; ?> />
                Wins Greater than: <input type="number" name="minWinAmount" value="<?php
                if (isset($_POST['minWinAmount'])) {
                    echo htmlentities($_POST['minWinAmount']);
                    $enteredWins = $_POST['minWinAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="losses" value="lossNum" <?php if (isset($_POST['losses'])) echo "checked=\"checked\""; ?> />
                Losses Greater than: <input type="number" name="minLossAmount" value="<?php
                if (isset($_POST['minLossAmount'])) {
                    echo htmlentities($_POST['minLossAmount']);
                    $enteredLosses = $_POST['minLossAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="otloss" value="otLossNum" <?php if (isset($_POST['otloss'])) echo "checked=\"checked\""; ?> />
                OT Losses Greater than: <input type="number" name="minOTLossAmount" value="<?php
                if (isset($_POST['minOTLossAmount'])) {
                    echo htmlentities($_POST['minOTLossAmount']);
                    $enteredOTLosses = $_POST['minOTLossAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="points" value="gamePointsNum" <?php if (isset($_POST['points'])) echo "checked=\"checked\""; ?> />
                Points greater than: <input type="number" name="minPointsAmount" value="<?php
                if (isset($_POST['minPointsAmount'])) {
                    echo htmlentities($_POST['minPointsAmount']);
                    $enteredPoints = $_POST['minPointsAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="goalsPerGame" value="goalsPerNum" <?php if (isset($_POST['goalsPerGame'])) echo "checked=\"checked\""; ?> />
                Goals per Game greater than: <input type="number" name="minGoalPerAmount" value="<?php
                if (isset($_POST['minGoalPerAmount'])) {
                    echo htmlentities($_POST['minGoalPerAmount']);
                    $enteredGoalsPerGame = $_POST['minGoalPerAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="goalsAllowed" value="goalsAllowedNum" <?php if (isset($_POST['goalsAllowed'])) echo "checked=\"checked\""; ?> />
                Goals Allowed per Game greater than: <input type="number" name="minGoalsAllowedAmount" value="<?php
                if (isset($_POST['minGoalsAllowedAmount'])) {
                    echo htmlentities($_POST['minGoalsAllowedAmount']);
                    $enteredGoalsAllowedPerGame = $_POST['minGoalsAllowedAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="shotsPerGame" value="shotsPerNum" <?php if (isset($_POST['shotsPerGame'])) echo "checked=\"checked\""; ?> />
                Shots per Game greater than: <input type="number" name="minShotsPerAmount" value="<?php
                if (isset($_POST['minShotsPerAmount'])) {
                    echo htmlentities($_POST['minShotsPerAmount']);
                    $enteredShotsPerGame = $_POST['minShotsPerAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="shotsAllowedPerGame" value="shotsAllowedNum" <?php if (isset($_POST['shotsAllowedPerGame'])) echo "checked=\"checked\""; ?> />
                Shots Allowed per Game greater than: <input type="number" name="minShotsAllowedAmount" value="<?php
                if (isset($_POST['minShotsAllowedAmount'])) {
                    echo htmlentities($_POST['minShotsAllowedAmount']);
                    $enteredShotsAllowedPerGame = $_POST['minShotsAllowedAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="faceoffPercent" value="faceoffNum" <?php if (isset($_POST['faceoffPercent'])) echo "checked=\"checked\""; ?> />
                Faceoff Win Percentage greater than: <input type="number" name="minFaceoffAmount" value="<?php
                if (isset($_POST['minFaceoffAmount'])) {
                    echo htmlentities($_POST['minFaceoffAmount']);
                    $enteredFaceoffWin = $_POST['minFaceoffAmount'];
                }
                ?>"/><br>

                <input type="checkbox" name="gamesPlayed" value="gamesNum" <?php if (isset($_POST['gamesPlayed'])) echo "checked=\"checked\""; ?> />
                Games Played greater than: <input type="number" name="minGamesAmount" value="<?php
                if (isset($_POST['minGamesAmount'])) {
                    echo htmlentities($_POST['minGamesAmount']);
                    $enteredFaceoffWin = $_POST['minGamesAmount'];
                }
                ?>"/><br>
            </td>
            </tr>
        </table>
        <br>

        <input type="submit" name="search">
    </form>

    <?php
    if (isset($_POST['search'])) { // if search button was clicked
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
            $query_str .= "teamID, team_name FROM team WHERE teamID = $teamID";
            if (isset($_POST['wins'])) {
                if (empty($_POST['minWinAmount'])) {
                    $enteredWins = 0;
                }
                $query_str .= " AND wins > " . $enteredWins;
            }
            if (isset($_POST['losses'])) {
                if (empty($_POST['minLossAmount'])) {
                    $enteredLosses = 0;
                }
                $query_str .= " AND losses > " . $enteredLosses;
            }
            if (isset($_POST['otloss'])) {
                if (empty($_POST['minOTLossAmount'])) {
                    $enteredOTLosses = 0;
                }
                $query_str .= " AND ot_losses > " . $enteredOTLosses;
            }
            if (isset($_POST['points'])) {
                if (empty($_POST['minPointsAmount'])) {
                    $enteredPoints = 0;
                }
                $query_str .= " AND points > " . $enteredPoints;
            }
            if (isset($_POST['goalsPerGame'])) {
                if (empty($_POST['minGoalPerAmount'])) {
                    $enteredGoalsPerGame = 0;
                }
                $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
            }
            if (isset($_POST['goalsAllowed'])) {
                if (empty($_POST['minGoalsAllowedAmount'])) {
                    $enteredGoalsAllowedPerGame = 0;
                }
                $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
            }
            if (isset($_POST['shotsPerGame'])) {
              if (empty($_POST['minShotsPerAmount'])) {
                  $enteredShotsPerGame = 0;
              }
              $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
            }
            if (isset($_POST['shotsAllowedPerGame'])) {
              if (empty($_POST['minShotsAllowedAmount'])) {
                  $enteredShotsAllowedPerGame = 0;
              }
              $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
            }
            if (isset($_POST['faceoffPercent'])) {
              if (empty($_POST['minFaceoffAmount'])) {
                  $enteredFaceoffWin = 0;
              }
              $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
            }
            if (isset($_POST['gamesPlayed'])) {
              if (empty($_POST['minGamesAmount'])) {
                  $enteredGames = 0;
              }
              $query_str .= " AND games_played > " . $enteredGames;
            }

            $query_str .= " ORDER BY team_name";

            echo "<br />" . $query_str;
            $res = $db->query($query_str);

            echo "<ul>";
            while ($row = $res->fetch_row()) {
                echo "<li>";
                format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link shows product name, but is identified by it's product code
                echo "</li>\n";
            };
            echo "</ul>";

            $res->free_result();
        } else if (isset($_POST['city']) && $_POST['city'] != "") {
            $city = $_POST['city'];
            echo $city;

            // get name to display link, use city name to find players in database
            $query_str .= "teamID, team_name FROM team WHERE team.city = '" . $city . "'";
            if (isset($_POST['wins'])) {
              if (empty($_POST['minWinAmount'])) {
                  $enteredWins = 0;
              }
              $query_str .= " AND wins > " . $enteredWins;
          }
          if (isset($_POST['losses'])) {
              if (empty($_POST['minLossAmount'])) {
                  $enteredLosses = 0;
              }
              $query_str .= " AND losses > " . $enteredLosses;
          }
          if (isset($_POST['otloss'])) {
              if (empty($_POST['minOTLossAmount'])) {
                  $enteredOTLosses = 0;
              }
              $query_str .= " AND ot_losses > " . $enteredOTLosses;
          }
          if (isset($_POST['points'])) {
              if (empty($_POST['minPointsAmount'])) {
                  $enteredPoints = 0;
              }
              $query_str .= " AND points > " . $enteredPoints;
          }
          if (isset($_POST['goalsPerGame'])) {
              if (empty($_POST['minGoalPerAmount'])) {
                  $enteredGoalsPerGame = 0;
              }
              $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
          }
          if (isset($_POST['goalsAllowed'])) {
              if (empty($_POST['minGoalsAllowedAmount'])) {
                  $enteredGoalsAllowedPerGame = 0;
              }
              $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
          }
          if (isset($_POST['shotsPerGame'])) {
            if (empty($_POST['minShotsPerAmount'])) {
                $enteredShotsPerGame = 0;
            }
            $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
          }
          if (isset($_POST['shotsAllowedPerGame'])) {
            if (empty($_POST['minShotsAllowedAmount'])) {
                $enteredShotsAllowedPerGame = 0;
            }
            $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
          }
          if (isset($_POST['faceoffPercent'])) {
            if (empty($_POST['minFaceoffAmount'])) {
                $enteredFaceoffWin = 0;
            }
            $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
          }
          if (isset($_POST['gamesPlayed'])) {
            if (empty($_POST['minGamesAmount'])) {
                $enteredGames = 0;
            }
            $query_str .= " AND games_played > " . $enteredGames;
          }

          $query_str .= " ORDER BY team_name";

          echo "<br />" . $query_str;
          $res = $db->query($query_str);

          echo "<ul>";
          while ($row = $res->fetch_row()) {
              echo "<li>";
              format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link shows product name, but is identified by it's product code
              echo "</li>\n";
          };
          echo "</ul>";

          $res->free_result();
        } else if (isset($_POST['name']) && $_POST['name'] != "") {
            $name = $_POST['name'];
            echo "Returning teams with name containing: '" . $name . "'";

            $query_str .= "teamID, team_name FROM team WHERE team_name LIKE '%" . $name . "%'";
            if (isset($_POST['wins'])) {
              if (empty($_POST['minWinAmount'])) {
                  $enteredWins = 0;
              }
              $query_str .= " AND wins > " . $enteredWins;
          }
          if (isset($_POST['losses'])) {
              if (empty($_POST['minLossAmount'])) {
                  $enteredLosses = 0;
              }
              $query_str .= " AND losses > " . $enteredLosses;
          }
          if (isset($_POST['otloss'])) {
              if (empty($_POST['minOTLossAmount'])) {
                  $enteredOTLosses = 0;
              }
              $query_str .= " AND ot_losses > " . $enteredOTLosses;
          }
          if (isset($_POST['points'])) {
              if (empty($_POST['minPointsAmount'])) {
                  $enteredPoints = 0;
              }
              $query_str .= " AND points > " . $enteredPoints;
          }
          if (isset($_POST['goalsPerGame'])) {
              if (empty($_POST['minGoalPerAmount'])) {
                  $enteredGoalsPerGame = 0;
              }
              $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
          }
          if (isset($_POST['goalsAllowed'])) {
              if (empty($_POST['minGoalsAllowedAmount'])) {
                  $enteredGoalsAllowedPerGame = 0;
              }
              $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
          }
          if (isset($_POST['shotsPerGame'])) {
            if (empty($_POST['minShotsPerAmount'])) {
                $enteredShotsPerGame = 0;
            }
            $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
          }
          if (isset($_POST['shotsAllowedPerGame'])) {
            if (empty($_POST['minShotsAllowedAmount'])) {
                $enteredShotsAllowedPerGame = 0;
            }
            $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
          }
          if (isset($_POST['faceoffPercent'])) {
            if (empty($_POST['minFaceoffAmount'])) {
                $enteredFaceoffWin = 0;
            }
            $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
          }
          if (isset($_POST['gamesPlayed'])) {
            if (empty($_POST['minGamesAmount'])) {
                $enteredGames = 0;
            }
            $query_str .= " AND games_played > " . $enteredGames;
          }

          $query_str .= " ORDER BY team_name";

          echo "<br />" . $query_str;
          $res = $db->query($query_str);

          echo "<ul>";
          while ($row = $res->fetch_row()) {
              echo "<li>";
              format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link shows product name, but is identified by it's product code
              echo "</li>\n";
          };
          echo "</ul>";

          $res->free_result();
        } else {
          $query_str .= "teamID, team_name FROM team WHERE team_name LIKE '%%'";
          if (isset($_POST['wins'])) {
            if (empty($_POST['minWinAmount'])) {
                $enteredWins = 0;
            }
            $query_str .= " AND wins > " . $enteredWins;
        }
        if (isset($_POST['losses'])) {
            if (empty($_POST['minLossAmount'])) {
                $enteredLosses = 0;
            }
            $query_str .= " AND losses > " . $enteredLosses;
        }
        if (isset($_POST['otloss'])) {
            if (empty($_POST['minOTLossAmount'])) {
                $enteredOTLosses = 0;
            }
            $query_str .= " AND ot_losses > " . $enteredOTLosses;
        }
        if (isset($_POST['points'])) {
            if (empty($_POST['minPointsAmount'])) {
                $enteredPoints = 0;
            }
            $query_str .= " AND points > " . $enteredPoints;
        }
        if (isset($_POST['goalsPerGame'])) {
            if (empty($_POST['minGoalPerAmount'])) {
                $enteredGoalsPerGame = 0;
            }
            $query_str .= " AND goals_for_per_game > " . $enteredGoalsPerGame;
        }
        if (isset($_POST['goalsAllowed'])) {
            if (empty($_POST['minGoalsAllowedAmount'])) {
                $enteredGoalsAllowedPerGame = 0;
            }
            $query_str .= " AND goals_against_per_game > " . $enteredGoalsAllowedPerGame;
        }
        if (isset($_POST['shotsPerGame'])) {
          if (empty($_POST['minShotsPerAmount'])) {
              $enteredShotsPerGame = 0;
          }
          $query_str .= " AND shots_for_per_game > " . $enteredShotsPerGame;
        }
        if (isset($_POST['shotsAllowedPerGame'])) {
          if (empty($_POST['minShotsAllowedAmount'])) {
              $enteredShotsAllowedPerGame = 0;
          }
          $query_str .= " AND shots_against_per_game > " . $enteredShotsAllowedPerGame;
        }
        if (isset($_POST['faceoffPercent'])) {
          if (empty($_POST['minFaceoffAmount'])) {
              $enteredFaceoffWin = 0;
          }
          $query_str .= " AND faceoff_win_percent > " . $enteredFaceoffWin;
        }
        if (isset($_POST['gamesPlayed'])) {
          if (empty($_POST['minGamesAmount'])) {
              $enteredGames = 0;
          }
          $query_str .= " AND games_played > " . $enteredGames;
        }

        $query_str .= " ORDER BY team_name";

        echo "<br />" . $query_str;
        $res = $db->query($query_str);

        echo "<ul>";
        while ($row = $res->fetch_row()) {
            echo "<li>";
            format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link shows product name, but is identified by it's product code
            echo "</li>\n";
        };
        echo "</ul>";

        $res->free_result();
        }
    }
    $db->close();
    ?>
</body>
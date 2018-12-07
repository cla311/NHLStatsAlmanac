<?php
session_start();
require('../required/nav.php');
require('../required/functions.php');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '960M');

$team = "";
$city = "";
$name = "";

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
            <form name="player">

                <h3>Select Player Parameters</h3>

                <p>By Team:</p>

                <?php
                // sql for filling drop down with order numbers from database
                $sql = "SELECT team_name FROM team ORDER BY team_name";
                $teamDropDownResult = mysqli_query($db, $sql);

                // drop down with team name from database
                echo "<select name =\"team_name\">";

                // first value of drop down is empty
                echo "<option value=\"\"></option>";

                // fill drop down with team names from database
                while ($row = mysqli_fetch_array($teamDropDownResult)) {
                    ?>
                    <option value="<?php echo $row['team_name']; ?>"> <?php echo $row['team_name'] ?> </option>;
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
                    <option value="<?php echo $row['city']; ?>"> <?php echo $row['city'] ?> </option>;
                    <?php
                }
                echo "</select>";

                mysqli_free_result($cityDropDownResult);
                ?>

                <br /><br />
                <p>OR</p>
                <br />

                <p>By Player:</p>
                Search: <input type="text" name="name"  value="<?php if (isset($_POST['name'])) echo htmlentities($_POST['name']); ?>" />
                </td>

                <td>
                    <?php
                    // secondary filtering variables
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

                    <!-- set up secondary filtering -->
                    <h4>Secondary Filters</h4>
                    <input type="checkbox" name="goals" value="goalNum" <?php if (isset($_POST['goals'])) echo "checked=\"checked\""; ?> />
                    Goals Greater than: <input type="number" name="minGoalAmount"/><br>

                    <input type="checkbox" name="assists" value="assistNum" <?php if (isset($_POST['assists'])) echo "checked=\"checked\""; ?> />
                    Assists Greater than: <input type="number" name="minAssistAmount"/><br>

                    <input type="checkbox" name="shots" value="shotNum" <?php if (isset($_POST['shots'])) echo "checked=\"checked\""; ?> />
                    Shots Greater than: <input type="number" name="minShotAmount"/><br>

                    <input type="checkbox" name="gameWinningGoals" value="gameWinningNum" <?php if (isset($_POST['gameWinningGoals'])) echo "checked=\"checked\""; ?> />
                    Game Winning Goals greater than: <input type="number" name="minGWGAmount"/><br>

                    <input type="checkbox" name="penaltyMinutes" value="penaltyNum" <?php if (isset($_POST['penaltyMinutes'])) echo "checked=\"checked\""; ?> />
                    Penalty Minutes greater than: <input type="number" name="minPenaltyAmount"/><br>

                    <input type="checkbox" name="gamesPlayed" value="gamesNum" <?php if (isset($_POST['gamesPlayed'])) echo "checked=\"checked\""; ?> />
                    Games Played greater than: <input type="number" name="minGamesAmount"/><br>

                    <input type="checkbox" name="saves" value="savesNum" <?php if (isset($_POST['saves'])) echo "checked=\"checked\""; ?> />
                    Saves greater than: <input type="number" name="minSavesAmount"/><br>

                    <input type="checkbox" name="wins" value="winsNum" <?php if (isset($_POST['wins'])) echo "checked=\"checked\""; ?> />
                    Wins greater than: <input type="number" name="minWinsAmount"/><br>

                    <input type="checkbox" name="losses" value="lossesNum" <?php if (isset($_POST['losses'])) echo "checked=\"checked\""; ?> />
                    Losses greater than: <input type="number" name="minLossAmount"/><br>
                    <br>

                    <input type="submit" name="submit">
            </form>
            <div id="playerResults">

            </div>
        </div>

        <!-- search for teams -->
        <div class="grid-col-1of2">
            <form name="team">
                <h3>Select Team Parameters</h3>

                <p>By Team:</p>

                <?php
                // sql for filling drop down with team name from database
                $sql = "SELECT team_name FROM team ORDER BY team_name";
                $teamDropDownResult = mysqli_query($db, $sql);

                // drop down with team name from database
                echo "<select name=\"team_title\">";

                // first value of drop down is empty
                echo "<option value=\"\"></option>";

                // fill drop down with teams from database
                while ($row = mysqli_fetch_array($teamDropDownResult)) {
                    ?>
                    <option value="<?php echo $row['team_name']; ?>"> <?php echo $row['team_name'] ?> </option>;
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
                    <option value="<?php echo $row['city']; ?>"> <?php echo $row['city'] ?> </option>;
                    <?php
                }
                echo "</select>";

                mysqli_free_result($cityDropDownResult);
                ?>

                <br /><br />
                <p>OR</p>
                <br />

                <!-- search by entered text -->
                <p>By Name:</p>
                Search: <input type="text" name="team_name_title"  value="<?php if (isset($_POST['team_name_title'])) echo htmlentities($_POST['team_name_title']); ?>" />
                </td>

                <td>
                    <?php
                    // secondary filtering variables
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
                    Wins Greater than: <input type="number" name="team_minWinAmount" /><br>

                    <input type="checkbox" name="team_losses" value="lossNum" <?php if (isset($_POST['team_losses'])) echo "checked=\"checked\""; ?> />
                    Losses Greater than: <input type="number" name="team_minLossAmount"/><br>

                    <input type="checkbox" name="team_otloss" value="otLossNum" <?php if (isset($_POST['team_otloss'])) echo "checked=\"checked\""; ?> />
                    OT Losses Greater than: <input type="number" name="team_minOTLossAmount"/><br>

                    <input type="checkbox" name="team_points" value="gamePointsNum" <?php if (isset($_POST['team_points'])) echo "checked=\"checked\""; ?> />
                    Points greater than: <input type="number" name="team_minPointsAmount"/><br>

                    <input type="checkbox" name="team_goalsPerGame" value="goalsPerNum" <?php if (isset($_POST['team_goalsPerGame'])) echo "checked=\"checked\""; ?> />
                    Goals per Game greater than: <input type="number" name="team_minGoalPerAmount"/><br>

                    <input type="checkbox" name="team_goalsAllowed" value="goalsAllowedNum" <?php if (isset($_POST['team_goalsAllowed'])) echo "checked=\"checked\""; ?> />
                    Goals Allowed per Game greater than: <input type="number" name="team_minGoalsAllowedAmount"/><br>

                    <input type="checkbox" name="team_shotsPerGame" value="shotsPerNum" <?php if (isset($_POST['team_shotsPerGame'])) echo "checked=\"checked\""; ?> />
                    Shots per Game greater than: <input type="number" name="team_minShotsPerAmount"/><br>

                    <input type="checkbox" name="team_shotsAllowedPerGame" value="shotsAllowedNum" <?php if (isset($_POST['team_shotsAllowedPerGame'])) echo "checked=\"checked\""; ?> />
                    Shots Allowed per Game greater than: <input type="number" name="team_minShotsAllowedAmount"/><br>

                    <input type="checkbox" name="team_faceoffPercent" value="faceoffNum" <?php if (isset($_POST['team_faceoffPercent'])) echo "checked=\"checked\""; ?> />
                    Faceoff Win Percentage greater than: <input type="number" name="team_minFaceoffAmount"/><br>
                    <br>

                    <input type="submit" name="submit">
            </form>

            <div id="teamResults">

            </div>
        </div>
    </div>

    <!-- don't reload the whole page -->
    <script>
        $("form[name='player']").on('submit', function (e) {

            e.preventDefault();

            $.ajax({
                type: 'post',
                url: 'searchPlayer.php',
                data: $(this).serialize(),
                success: function (response) {
                    $("#playerResults").html(response);
                }
            });

        });

        $("form[name='team']").on('submit', function (e) {

            e.preventDefault();

            $.ajax({
                type: 'post',
                url: 'searchTeam.php',
                data: $(this).serialize(),
                success: function (response) {
                    $("#teamResults").html(response);
                }
            });

        });

        $(document).on('click', '.page', function (e) {

            e.preventDefault();

            var link = $(this).attr("href");
            $.ajax({
                type: 'post',
                url: link,
                data: $("form[name='player']").serialize(),
                success: function (response) {
                    $("#playerResults").html(response);
                }
            });

        });

        $(document).on('submit', "form[name='goToPage']", function (e) {

            e.preventDefault();

            $.ajax({
                type: 'post',
                url: 'searchPlayer.php?page=' + $('#pages').val(),
                data: $(this).serialize(),
                success: function (response) {
                    $("#playerResults").html(response);
                }
            });

        });
    </script>

</body>
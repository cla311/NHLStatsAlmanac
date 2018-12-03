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

unset($_SESSION['playerID']);
unset($_SESSION['name']);
?>

<?php require_login(); // if not logged in, redirect to login page ?>

<?php
if (isset($_POST['submit'])) {
    if (isset($_POST['team_title'])) {
        $title = $_POST['team_title'];
        $_SESSION['team_title'] = $title;
        $author = $_SESSION['username'];
        $fantasyTeamID = $title . "_" . $author;
        $_SESSION['fantasyTeamID'] = $fantasyTeamID;
        $sql_create_team = "CREATE TABLE IF NOT EXISTS `" . $fantasyTeamID . "` (
        fantasyTeamID VARCHAR(50) NOT NULL,
        team_author VARCHAR(50) NOT NULL,
        playerID VARCHAR(50) NOT NULL,
        team_title VARCHAR(50) NOT NULL,
        PRIMARY KEY (playerID)
        )";
        $res = $db->query($sql_create_team);

        echo "<table border=\"solid\">";
        echo "<tr>";
        echo "<th>" . $title . "</th>";
        echo "</tr>";
        echo "</table>";
        $_SESSION["fantasyTeamID"] = $fantasyTeamID;
        $_SESSION["addPlayer"] = "Please add a player to your new fantasy team to continue";
        redirect_to("lookup.php");
    } else {
        echo "Team name is needed";
    }
}
?>

<div class="grid">
    <div class="grid-col-1of2">
        <h3>Team Setup</h3>
        <form action="fantasy.php" method="post">
            Title: <input type="text" name="team_title" pattern="^[A-Za-z0-9]*$" title="Team name may only contain letters and numbers" value="<?php if (isset($_POST['team_title'])) echo htmlentities($_POST['team_title']); ?>" />
            <br /><br /><input type="submit" name="submit" value="Create" />
        </form>
    </div>

    <br /><br />

    <div class="grid-col-1of2">
        <?php
        if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
            $sql_show_teams = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%_" . $username . "'";
            $res = $db->query($sql_show_teams);
            echo "<h3>My Fantasy Teams</h3>";
            echo "<ul>";
            while ($row = $res->fetch_row()) {
                $sql_show_team_name = "SELECT team_title, fantasyTeamID FROM $row[0]";
                $result = $db->query($sql_show_team_name);
                $subRow = $result->fetch_row();
                echo "<li>";
                echo "<a href=\"userteam.php?fantasyTeamID=$subRow[1]\">$subRow[0]</a>";
                echo "</li>";
            }
            echo "</ul>";
        }
        ?>
    </div>
</div>

<?php
echo "<div class=\"center-block\">";
echo "<p><a href=\"lookup.php\">Search Players</a></p>";
echo "</div>";
$db->close();
?>

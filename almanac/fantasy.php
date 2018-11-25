<?php
require('../required/nav.php');
require('../required/functions.php');
session_start();

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
  $email = $_SESSION['user_email'];
  $firstName = $_SESSION['firstName'];
  $username = $_SESSION['username'];
} else {
  $email = $_SESSION['user_email'] = [];
  $firstName = $_SESSION['firstName'] = [];
  $username = $_SESSION['username'] = [];
}
?>

<?php require_login(); // if not logged in, redirect to login page ?>

<body>
    <form action="fantasy.php" method="post">
        <table>
            <tr>
                <td>
                    <h3>Team Setup</h3>

                    <p></p>
                    Title: <input type="text" name="team_title" value="<?php if (isset($_POST['team_title'])) echo htmlentities($_POST['team_title']); ?>" />
                    <?php
                    //  $city="";
                    //   // sql for filling drop down with city from database
                    //   $sql = "SELECT DISTINCT city FROM team ORDER BY city";
                    //   $cityDropDownResult = mysqli_query($db, $sql);
                    //   // drop down with city from database
                    //   echo "<br /><br />";
                    //   echo "City: ";
                    //   echo "<select name =\"city\">";
                    //   // first value of drop down is empty
                    //   echo "<option value=\"\"></option>";
                    //   // fill drop down with city from database
                    //   while ($row = mysqli_fetch_array($cityDropDownResult))
                    //   {
                    ?>
                     <!-- <option value="<?php echo $row['city']; ?>" <?php if (isset($_POST['city']) && $_POST['city'] != "" && $_POST['city'] == $city) echo " selected"; ?> > <?php echo $row['city'] ?> </option>; -->
                    <?php
                    // }
                    // echo "</select>";
                    // mysqli_free_result($cityDropDownResult);
                    ?>

                    <br /><br />
                </td>
            </tr>
        </table>
        <br>

        <input type="submit" name="submit" value="Create" />
    </form>
</body>

<?php
echo "<a href=\"lookup.php\">Search Players</a>";

if (isset($_POST['submit'])) { // if submit button was clicked
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
      PRIMARY KEY (fantasyTeamID)
      )";
        $res = $db->query($sql_create_team);

        echo "<table border=\"solid\">";
        echo "<tr>";
        echo "<th>" . $title . "</th>";
        echo "</tr>";
        echo "</table>";

        redirect_to("userteam.php?fantasyTeamID=$fantasyTeamID");
    } else {
        echo "Team name is needed";
    }
}

$sql_show_teams = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%_".$username."'";
$res = $db->query($sql_show_teams);
echo "<ul>";
while ($row = $res->fetch_row())
{
  $sql_show_team_name = "SELECT team_title FROM $row[0]";
  $result = $db->query($sql_show_team_name);
  $subRow = $result->fetch_row();
  echo "<h3>My Fantasy Teams</h3>";
  echo "<li>";
  format_name_as_link($row[0], $subRow[0], "userteam.php");
  echo "</li>";
}
echo "</ul>";

$db->close();
?>
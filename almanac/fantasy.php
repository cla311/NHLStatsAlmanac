<?php 
require('../required/nav.php');
require('../required/functions.php');
session_start();
?>

<?php require_login(); // if not logged in, redirect to login page ?>

<body>
<form action=<?php fantasy_team_page("userteam.php",$title) ?> method="post">
<!-- "<a href=\"$page?playerID=$id\">$name</a>"; -->
    <table>
    <tr>
            <td>
                <h3>Team Setup</h3>

                <p></p>
               Title: <input type="text" name="team_title" value="<?php if(isset($_POST['team_title'])) echo htmlentities($_POST['team_title']); ?>" />
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
                <!-- <option value="<?php echo $row['city']; ?>" <?php if(isset($_POST['city']) && $_POST['city'] != "" && $_POST['city'] == $city) echo " selected"; ?> > <?php echo $row['city'] ?> </option>; -->
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
  echo $_SESSION['username'];

  if (isset($_POST['submit'])) // if submit button was clicked
  {
    if (isset($_POST['team_title']))
    {
      $_SESSION['currentFantasyTeam'] = $title;
      $title = $_POST['team_title'];
      $author = $_SESSION['username'];
      $fantasyTeamID = $title."_".$author;
      $sql_create_team = "CREATE TABLE IF NOT EXISTS `".$fantasyTeamID."` (
      fantasyTeamID VARCHAR(50) NOT NULL,
      team_author VARCHAR(50) NOT NULL,
      playerID VARCHAR(50) NOT NULL
      PRIMARY KEY (fantasyTeamID)
      )";
      $res = $db->query($sql_create_team);

      echo "<table border=\"solid\">";
      echo "<tr>";
      echo "<th>".$title."</th>";
      echo "</tr>";
      echo "</table>";

    } else {
      echo "Team name is needed";
    }
  }
  ?>
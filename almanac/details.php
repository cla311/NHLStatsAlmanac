<?php 
require('../required/nav.php');
require('../required/functions.php');
?>

<?php
session_start();

if (!empty($_SESSION['team_title'])) {
  $title = $_SESSION['team_title'];
} else {
  $title = $_SESSION['team_title'] = [];
}

$id = trim($_GET['playerID']);

echo "<br /><br />";

// display player image
$query = "SELECT photo FROM player INNER JOIN team ON player.teamID = team.teamID WHERE playerID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s',$id);
$stmt->execute();
$stmt->bind_result($photo1);

if($stmt->fetch())
{
  echo "<img src=\"$photo1\" alt=\"Player Photo\">";
}
$stmt->free_result();

echo "<br /><br />";

$query = "SELECT name, team.team_name, weight, height, nationality, age, position FROM player INNER JOIN team ON player.teamID = team.teamID WHERE playerID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s',$id);
$stmt->execute();
$stmt->bind_result($name1,$team_name1,$weight1,$height1,$nationality1,$age1,$position1);

get_player($id,$name1);

// display information in a table
echo "<table border=\"solid\">";
echo "<tr>";
echo "<th>Name</th>";
echo "<th>Team</th>";
echo "<th>Weight</th>";
echo "<th>Height</th>";
echo "<th>Nationality</th>";
echo "<th>Age</th>";
echo "<th>Position</th>";
echo "</tr>";

if($stmt->fetch())
{
  echo "<td align=\"center\">".$name1."</td>";
  echo "<td align=\"center\">".$team_name1."</td>";
  echo "<td align=\"center\">".$weight1."</td>";
  echo "<td align=\"center\">".$height1."</td>";
  echo "<td align=\"center\">".$nationality1."</td>";
  echo "<td align=\"center\">".$age1."</td>";
  echo "<td align=\"center\">".$position1."</td>";
}
echo "</tr>";
echo "</table>";
$stmt->free_result();

$query_pos = "SELECT position FROM player INNER JOIN team on player.teamID = team.teamID WHERE playerID = $id";
$res_pos = $db->query($query_pos);
$row = $res_pos->fetch_row();
$player_position="";
$player_position = $row[0];
$res_pos->free_result();

echo "<br /><br />";

echo "<h3>Player Stats</h3>";
switch ($player_position)
{
    case ('G'):
    $query = "SELECT games_played, starts, wins, losses, ot_losses, shots_against, saves, goals_against, save_percent, goals_against_average, shutouts FROM goalie_stats INNER JOIN player ON goalie_stats.playerID = player.playerID WHERE goalie_stats.playerID = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$id);
    $stmt->execute();
    $stmt->bind_result($games_played1, $starts1, $wins1, $losses1, $ot_losses1, $shots_against1, $saves1, $goals_against1, $save_percent1, $goals_against_average1, $shutouts1);
    
    echo "<table border=\"solid\">";
    echo "<tr>";
    echo "<th>Games Played</th>";
    echo "<th>Starts</th>";
    echo "<th>Wins</th>";
    echo "<th>Losses</th>";
    echo "<th>Overtime Losses</th>";
    echo "<th>Shots Against</th>";
    echo "<th>Saves</th>";
    echo "<th>Goals Against</th>";
    echo "<th>Save Percentage</th>";
    echo "<th>Average Goals Against</th>";
    echo "<th>Shutouts</th>";
    echo "</tr>";

    if($stmt->fetch())
    {
      echo "<td align=\"center\">".$games_played1."</td>";
      echo "<td align=\"center\">".$starts1."</td>";
      echo "<td align=\"center\">".$wins1."</td>";
      echo "<td align=\"center\">".$losses1."</td>";
      echo "<td align=\"center\">".$ot_losses1."</td>";
      echo "<td align=\"center\">".$shots_against1."</td>";
      echo "<td align=\"center\">".$saves1."</td>";
      echo "<td align=\"center\">".$goals_against1."</td>";
      echo "<td align=\"center\">".$save_percent1."</td>";
      echo "<td align=\"center\">".$goals_against_average1."</td>";
      echo "<td align=\"center\">".$shutouts1."</td>";
    
    }
    echo "</tr>";
    echo "</table>";
    $stmt->free_result();
    break;

    default :
    $query = "SELECT games_played, goals, assists, penalty_minutes, power_play_goals, power_play_points, short_handed_goals, short_handed_points, game_winning_goals, plus_minus, shots, overtime_goals FROM stats INNER JOIN player ON stats.playerID = player.playerID WHERE stats.playerID = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$id);
    $stmt->execute();
    $stmt->bind_result($games_played1,$goals1,$assists1,$penalty_minutes1,$power_play_goals1,$power_play_points1,$short_handed_goals1, $short_handed_points1, $game_winning_goals1, $plus_minus1, $shots1, $overtime_goals1);
    
    echo "<table border=\"solid\">";
    echo "<tr>";
    echo "<th>Games Played</th>";
    echo "<th>Goals</th>";
    echo "<th>Assists</th>";
    echo "<th>Penalty Minutes</th>";
    echo "<th>Power Play Goals</th>";
    echo "<th>Power Play Points</th>";
    echo "<th>Short Handed Goals</th>";
    echo "<th>Short Handed Points</th>";
    echo "<th>Game Winning Goal</th>";
    echo "<th>Plus/Minus</th>";
    echo "<th>Shots</th>";
    echo "<th>Overtime Goals</th>";
    echo "</tr>";
    
    if($stmt->fetch())
    {
      echo "<td align=\"center\">".$games_played1."</td>";
      echo "<td align=\"center\">".$goals1."</td>";
      echo "<td align=\"center\">".$assists1."</td>";
      echo "<td align=\"center\">".$penalty_minutes1."</td>";
      echo "<td align=\"center\">".$power_play_goals1."</td>";
      echo "<td align=\"center\">".$power_play_points1."</td>";
      echo "<td align=\"center\">".$short_handed_goals1."</td>";
      echo "<td align=\"center\">".$short_handed_points1."</td>";
      echo "<td align=\"center\">".$game_winning_goals1."</td>";
      echo "<td align=\"center\">".$plus_minus1."</td>";
      echo "<td align=\"center\">".$shots1."</td>";
      echo "<td align=\"center\">".$overtime_goals1."</td>";
    
    }
    echo "</tr>";
    echo "</table>";
    $stmt->free_result();
    break;
}

$db->close();

echo "<br /><br />";
echo "<a href=\"userteam.php?fantasyTeamID=$title\">Add to Fantasy Team</a>";
?>
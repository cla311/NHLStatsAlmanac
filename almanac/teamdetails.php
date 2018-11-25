<?php 
require('../required/nav.php');
require('../required/functions.php');
?>

<?php
session_start();

if (!empty($_SESSION['team_title']) && !empty($_SESSION['fantasyTeamID'])) {
  $title = $_SESSION['team_title'];
  $fantasyTeamID = $_SESSION['fantasyTeamID'];
} else {
  $title = $_SESSION['team_title'] = [];
  $fantasyTeamID = $_SESSION['fantasyTeamID'] = [];
}

$id = trim($_GET['teamID']);

echo "<br /><br />";

$query = "SELECT team_name, city, arena, conference, division FROM team WHERE teamID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s',$id);
$stmt->execute();
$stmt->bind_result($team_name1,$city1,$arena1,$conference1,$division1);

// display information in a table
echo "<table border=\"solid\">";
echo "<tr>";
echo "<th>Name</th>";
echo "<th>City</th>";
echo "<th>Arena</th>";
echo "<th>Conference</th>";
echo "<th>Division</th>";
echo "</tr>";

if($stmt->fetch())
{
  echo "<td align=\"center\">".$team_name1."</td>";
  echo "<td align=\"center\">".$city1."</td>";
  echo "<td align=\"center\">".$arena1."</td>";
  echo "<td align=\"center\">".$conference1."</td>";
  echo "<td align=\"center\">".$division1."</td>";
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

echo "<h3>Team Stats</h3>";
    
    $query = "SELECT games_played, wins, losses, ot_losses, points, goals_for_per_game, goals_against_per_game, power_play_percent, penalty_kill_percent, shots_for_per_game, shots_against_per_game, faceoff_win_percent FROM team WHERE teamID = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$id);
    $stmt->execute();
    $stmt->bind_result($games_played1,$wins1,$losses1,$ot_losses1,$points1,$goals_for_per_game1,$goals_against_per_game1, $power_play_percent1, $penalty_kill_percent1, $shots_for_per_game1, $shots_against_per_game1, $faceoff_win_percent1);
    
    echo "<table border=\"solid\">";
    echo "<tr>";
    echo "<th>Games Played</th>";
    echo "<th>Wins</th>";
    echo "<th>Losses</th>";
    echo "<th>OT Losses</th>";
    echo "<th>Points</th>";
    echo "<th>Goals per Game</th>";
    echo "<th>Goals Allowed per Game</th>";
    echo "<th>Powerplay Efficiency</th>";
    echo "<th>Penalty Kill Efficiency</th>";
    echo "<th>Shots per Game</th>";
    echo "<th>Shots Allwed per Game</th>";
    echo "<th>Faceoff Efficiency</th>";
    echo "</tr>";
    
    if($stmt->fetch())
    {
      echo "<td align=\"center\">".$games_played1."</td>";
      echo "<td align=\"center\">".$wins1."</td>";
      echo "<td align=\"center\">".$losses1."</td>";
      echo "<td align=\"center\">".$ot_losses1."</td>";
      echo "<td align=\"center\">".$points1."</td>";
      echo "<td align=\"center\">".$goals_for_per_game1."</td>";
      echo "<td align=\"center\">".$goals_against_per_game1."</td>";
      echo "<td align=\"center\">".$power_play_percent1."%</td>";
      echo "<td align=\"center\">".$penalty_kill_percent1."%</td>";
      echo "<td align=\"center\">".$shots_for_per_game1."</td>";
      echo "<td align=\"center\">".$shots_against_per_game1."</td>";
      echo "<td align=\"center\">".$faceoff_win_percent1."%</td>";
    
    }
    echo "</tr>";
    echo "</table>";
    $stmt->free_result();

$db->close();
?>
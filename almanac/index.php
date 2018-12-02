<?php

require('../required/nav.php');
require('../required/functions.php');
session_start();
?>

<?php 
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    $email = $_SESSION['user_email'];
    $firstName = $_SESSION['firstName'];
    $username = $_SESSION['username'];
} else {
    $email = $_SESSION['user_email'] = [];
    $firstName = $_SESSION['firstName'] = [];
    $username = $_SESSION['username'] = [];
}

$sql_display_fantasy = "SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = 'nhl_stats' 
AND TABLE_NAME != 'goalie_stats' AND TABLE_NAME != 'members' AND TABLE_NAME != 'player' 
AND TABLE_NAME != 'roster' AND TABLE_NAME != 'stats' AND TABLE_NAME != 'team'";
$sql_display_fantasy .= " LIMIT 10";
$res = $db->query($sql_display_fantasy);

echo "<div class=\"grid\">";
echo "<div class=\"grid-col-1of2\">";
  echo "<h3>Member Fantasy Teams</h3>";
  echo "<ul>";
  while ($row = $res->fetch_row())
  {
    $sql_display = "SELECT DISTINCT team_title FROM $row[0]";
    $result = $db->query($sql_display);
    while ($subRow = $result->fetch_row())
    {
        echo "<li>";
        echo "<a href=\"userteam.php?fantasyTeamID=$row[0]\">$subRow[0]</a>";
        echo "</li>";
    }
    
  }
  echo "</ul>";
  echo "</div>";
  echo "</div>";
?>
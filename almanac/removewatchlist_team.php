<?php

session_start();
require('../required/nav.php');
require('../required/functions.php');
require_login();

//Remove team to current user's team watchlist/favoutires
$sql = "DELETE FROM team_watchlist WHERE ";
$sql .= "username='" . $_SESSION['username'] . "' AND ";
$sql .= "teamID='" . $_SESSION['teamID'] . "'";
$result = mysqli_query($db, $sql);

// For INSERT statements, $result is true/false
if ($result) {
    header("Location: account.php");
} else {
    // INSERT failed
    echo mysqli_error($db);
    db_disconnect($db);
    exit;
}
?>

<?php

session_start();
require('../required/nav.php');
require('../required/functions.php');
require_login();

//Add team to current user's team watchlist/favoutires
$sql = "INSERT INTO team_watchlist ";
$sql .= "(username, teamID) ";
$sql .= "VALUES (";
$sql .= "'" . $_SESSION['username'] . "',";
$sql .= "'" . $_SESSION['teamID'] . "'";
$sql .= ")";
$result = mysqli_query($db, $sql);

if ($result) {
    header("Location: account.php");
} else {
    echo mysqli_error($db);
    db_disconnect($db);
    exit;
}
?>

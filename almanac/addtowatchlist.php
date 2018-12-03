<?php

session_start();
require('../required/nav.php');
require('../required/functions.php');

require_login();
$sql = "INSERT INTO watchlist ";
$sql .= "(username, playerID) ";
$sql .= "VALUES (";
$sql .= "'" . $_SESSION['username'] . "',";
$sql .= "'" . $_SESSION['playerID'] . "'";
$sql .= ")";
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

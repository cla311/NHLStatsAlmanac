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

if ($result) {
    header("Location: account.php");
} else {
    echo mysqli_error($db);
    db_disconnect($db);
    exit;
}
?>

<?php

session_start();
require('../required/nav.php');
require('../required/functions.php');

require_login();
$sql = "DELETE FROM watchlist WHERE ";
$sql .= "username='" . $_SESSION['username'] . "' AND ";
$sql .= "playerID='" . $_SESSION['playerID'] . "'";
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

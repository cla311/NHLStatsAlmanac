<?php

session_start();
require_once('../required/functions.php');
require_once('../required/nav.php');
echo "<br />";
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    // log out user
    session_unset();
    session_destroy(); // destroy all saved values
    echo "You are logged out.";
    header("Location: login.php"); // go back to login page automatically
} else {
    header("Location: login.php");
}
?>
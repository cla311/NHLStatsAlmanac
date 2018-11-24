<?php 
require('../required/nav.php');
require('../required/functions.php');
session_start();
?>

<?php require_login(); // if not logged in, redirect to login page ?>

<?php
$teamID = trim($_GET['fantasyTeamID']);
?>
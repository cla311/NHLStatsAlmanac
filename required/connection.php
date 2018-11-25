<?php

// define connction properties
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "nhl_stats";

// establish connection
@$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Test if connection succeeded
if (mysqli_connect_errno()) {
    die("Database connection failed: " .
            mysqli_connect_error() .
            " (" . mysqli_connect_errno() . ")"
    );
}
?>
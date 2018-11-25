<?php
session_start();
require_once('../required/functions.php');
require_once('../required/nav.php');
echo "<br />";
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    if (isset($_POST['submit'])) {
        // log out
        unset($_SESSION['user_email']);
        unset($_SESSION['firstName']);
        unset($_SESSION['username']);
        unset($_SESSION['playerID']);
        unset($_SESSION['name']);
        session_destroy(); // destroy all saved values
        echo "You are logged out.";
        header("Location: login.php");
    } else {
        ?>
        <br /><br />
        <form action="logout.php" method="post">
            <input type="submit" name="submit" value="Logout"/>
        </form>

        <?php
    }
} else {
    echo "You are not logged in."; // can't logout if user is not already logged in
}
?>
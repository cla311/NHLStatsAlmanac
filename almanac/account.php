<?php
require('../required/nav.php');
require('../required/functions.php');
session_start();

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
  $email = $_SESSION['user_email'];
  $firstName = $_SESSION['firstName'];
  $username = $_SESSION['username'];
} else {
  $email = $_SESSION['user_email'] = [];
  $firstName = $_SESSION['firstName'] = [];
  $username = $_SESSION['username'] = [];
}
?>
<header>
    <nav>
        <!-- set each link to http protocol, NOT https -->
        <?php
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/index.php\">Home</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/lookup.php\">Search</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/fantasy.php\">Fantasy Teams</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/account.php\">My Account</a>";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/login.php\">Login/Logout</a>";
        ?>
    </nav>
</header>
<?php
require_login();

$sql_user_info = "SELECT firstName, lastName, email, username FROM members WHERE email = '$email' AND firstName = '$firstName' AND username = '$username'";
// echo $sql_user_info;
echo "<br /><br />";
$res = $db->query($sql_user_info);

echo "<div class=\"space\">";
        echo "<div class=\"section\">";
while ($row = $res->fetch_row())
    {
        

        echo "<div class=\"fill\">";
        echo "<h3>Current Name</h3>";
        echo "<p>" . $row[0] . " " . $row[1] . "</p>";
        echo "</div>";

        echo "<div class=\"fill\">";
        echo "<h3>Email</h3>";
        echo "<p>" . $row[2] . "</p>";
        echo "</div>";

        echo "<div class=\"fill\">";
        echo "<h3>Username</h3>";
        echo "<p>" . $row[3] . "</p>";
        echo "</div>";

        
    }
    echo "<div>";
        echo "</div>";
?>
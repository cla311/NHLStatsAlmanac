<head>
<link rel="stylesheet" href="../css/main.css">
<link rel="stylesheet" href="../css/grid.css">
</head>

<?php
$host = $_SERVER['HTTP_HOST']
?>

<img class="logo" src="../img/hockey.png" alt="Website Logo" />

<header>
    <nav>
        <!-- set each link to http protocol, NOT https -->
        <?php
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/index.php\">Home</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/lookup.php\">Search</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/fantasy.php\">Fantasy Teams</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/login.php\">Login/Logout</a>";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/account.php\">My Account</a>";
        ?>
    </nav>

    <?php
    // confirm if user is logged in
    if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
        $email = $_SESSION['user_email'];
        $firstName = $_SESSION['firstName'];
        $username = $_SESSION['username'];

        echo "<p>";
        echo "Hi " . $firstName . "!";
        echo "</p>";
    }
    ?>
</header>
<?php
$host = $_SERVER['HTTP_HOST']
?>

<header>
    <nav>
        <!-- set each link to http protocol, NOT https -->
        <?php
        echo "<a href=\"http://" . $host . "/NHLStatsAlmanac/almanac/index.php\">Home</a> ";
        echo "<a href=\"http://" . $host . "/NHLStatsAlmanac/almanac/lookup.php\">Search</a> ";
        echo "<a href=\"http://" . $host . "/NHLStatsAlmanac/almanac/fantasy.php\">Create a Fantasy Team</a> ";
        echo "<a href=\"http://" . $host . "/NHLStatsAlmanac/almanac/login.php\">Login/Logout</a>";
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
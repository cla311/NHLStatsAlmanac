<head>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/grid.css">
</head>

<?php
$host = $_SERVER['HTTP_HOST'];

if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    $page = "logout.php";
    $pageName = "Logout";
} else {
    $page = "login.php";
    $pageName = "Login";
}
?>

<img class="logo" src="../img/hockey.png" alt="Website Logo" />

<header>
    <nav>
        <!-- set each link to http protocol, NOT https -->
        <?php
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/index.php\">Home</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/lookup.php\">Search</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/fantasy.php\">Fantasy Teams</a> ";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/account.php\">My Account</a>";
        echo "<a class=\"nav-item\" href=\"http://" . $host . "/NHLStatsAlmanac/almanac/$page\">$pageName</a>";
        ?>
    </nav>
</header>
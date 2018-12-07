<?php
session_start();
require('../required/nav.php');
require('../required/functions.php');

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

<?php
require_login();
require_ssl(); // set to https
$passwordError = array();
if (isset($_POST['accountInfoUpdate'])) {
    $user = array();
    $user['firstName'] = $_POST['firstName'] ?? '';
    $user['lastName'] = $_POST['lastName'] ?? '';
    $user['email'] = $_POST['email'] ?? '';
    $user['password'] = $_POST['password'] ?? '';
    $user['confirm_password'] = $_POST['confirm_password'] ?? '';

    $newInfo = array();
    if (!empty($user['firstName'])) {
        $_SESSION['firstName'] = $user['firstName'];
        $firstName = $_SESSION['firstName'];
        array_push($newInfo, "firstName='" . db_escape($db, $user['firstName']) . "'");
    }

    if (!empty($user['lastName'])) {
        array_push($newInfo, "lastName='" . db_escape($db, $user['lastName']) . "'");
    }

    if (!empty($user['email'])) {
        $_SESSION['user_email'] = $user['email'];
        $email = $_SESSION['user_email'];
        array_push($newInfo, "email='" . db_escape($db, $user['email']) . "'");
    }

    if (!empty($user['password']) && !empty($user['confirm_password'])) {
        if ($user['password'] != $user['confirm_password']) {
            array_push($passwordError, "Password and confirm password must match.");
        } else {
            $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);
            array_push($newInfo, "password='" . db_escape($db, $hashed_password) . "'");
        }
    } else if ((empty($user['password']) && !empty($user['confirm_password'])) || (!empty($user['password']) && empty($user['confirm_password']))) {
        array_push($passwordError, "Password and confirm password must match.");
    }

    if (!empty($newInfo) && empty($passwordError)) {
        $sql_update = "UPDATE members SET ";
        for ($i = 0; $i < count($newInfo); $i++) {
            if ($i == 0) {
                $sql_update .= $newInfo[$i];
            } else {
                $sql_update .= ", " . $newInfo[$i];
            }
        }
        $sql_update .= " WHERE username = '$username'";
        $update = $db->query($sql_update);
        echo "Account info updated sucessfully";
    }
}

$sql_user_info = "SELECT firstName, lastName, email, username FROM members WHERE email = '$email' AND firstName = '$firstName' AND username = '$username'";
$res = $db->query($sql_user_info);

$sql_watchlist = "SELECT watchlist.playerID, player.name FROM watchlist JOIN player ON watchlist.playerID = player.playerID WHERE username = '$username'";
$list = $db->query($sql_watchlist);

$sql_team_watchlist = "SELECT team_watchlist.teamID, team.team_name FROM team_watchlist JOIN team ON team_watchlist.teamID = team.teamID WHERE username = '$username'";
$listTeam = $db->query($sql_team_watchlist);
echo mysqli_error($db);

$firstName = "";
$lastName = "";
$email = "";
$userame = "";
?>

<div class="grid">

    <?php
    // dispaly the user's current information
    while ($row = $res->fetch_assoc()) {
        $firstName = $row["firstName"];
        $lastName = $row["lastName"];
        $email = $row["email"];
        $username = $row["username"];

        echo "<div class=\"grid-col-1of3 left\">";
        echo "<h3>Name</h3>";
        echo "<p>" . $firstName . " " . $lastName . "</p>";
        echo "</div>";

        echo "<div class=\"grid-col-1of3 left\">";
        echo "<h3>Email</h3>";
        echo "<p>" . $email . "</p>";
        echo "</div>";

        echo "<div class=\"grid-col-1of3\">";
        echo "<h3>Username</h3>";
        echo "<p>" . $username . "</p>";
        echo "</div>";
    }
    mysqli_free_result($res);
    ?>

</div>

<div class="grid">
    <div class="grid-form-1of3" >
        <?php echo display_errors($passwordError); ?>

        <!-- dispaly form for updating user information -->
        <div class="account-field">
            <form action="account.php" method="post"> 
                <label>New Email: </label><input type="text" name="email" /><br /><br />
                <label>New First Name: </label><input type="text" name="firstName" /><br />
                <label>New Last Name: </label><input type="text" name="lastName" /><br /><br />
                <label>New Password: </label><input type="password" name="password" /><br />
                <label>Confirm Password: </label><input type="password" name="confirm_password" /><br /><br />
                <input type="submit" name="accountInfoUpdate" value="Update" />
            </form>
        </div>

    </div>

    <!-- show player and team favourites -->
    <div class="grid-col-1of3">
        <?php
        echo "<h3>Favourites</h3>";
        echo "<label class='watchlist'>Players</label>";
        echo "<ul>";
        while ($row = $list->fetch_row()) {
            echo "<li class='list-item'>";
            format_name_as_link($row[0], $row[1], "details.php"); // link shows player name, but is identified by it's player code
            echo "</li>\n";
        }
        mysqli_free_result($list);
        echo "</ul><br />";
        echo "<label class='watchlist'>Teams</label>";
        echo "<ul>";
        while ($row = $listTeam->fetch_row()) {
            echo "<li class='list-item'>";
            format_name_as_link_team($row[0], $row[1], "teamdetails.php"); // link shows player name, but is identified by it's player code
            echo "</li>\n";
        }
        mysqli_free_result($listTeam);
        echo "</ul>";
        ?>
    </div>
</div>

<?php $db->close(); ?>
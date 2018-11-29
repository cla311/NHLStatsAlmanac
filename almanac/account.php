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

<?php
require_login();
require_ssl(); // set to https

$user = [];
$user['firstName'] = $_POST['firstName'] ?? '';
$user['lastName'] = $_POST['lastName'] ?? '';
$user['username'] = $_POST['username'] ?? '';
$user['password'] = $_POST['password'] ?? '';
$user['confirm_password'] = $_POST['confirm_password'] ?? '';

$sql_user_info = "SELECT firstName, lastName, email, username FROM members WHERE email = '$email' AND firstName = '$firstName' AND username = '$username'";
// echo $sql_user_info;
echo "<br /><br />";
$res = $db->query($sql_user_info);

$firstName="";
$lastName="";
$email="";
$userame="";
?>

<div class="grid">

    <?php 
    while ($row = $res->fetch_row())
        {
            $firstName = $row[0];
            $lastName = $row[1];
            $email = $row[2];
            $username = $row[3];

            echo "<div class=\"grid-col-1of3 left\">";
            echo "<h3>Current Name</h3>";
            echo "<p>" . $row[0] . " " . $row[1] . "</p>";
            echo "</div>";
            
            echo "<div class=\"grid-col-1of3 left\">";
            echo "<h3>Current Email</h3>";
            echo "<p>" . $row[2] . "</p>";
            echo "</div>";
    
            echo "<div class=\"grid-col-1of3\">";
            echo "<h3>Current Username</h3>";
            echo "<p>" . $row[3] . "</p>"; 
            echo "</div>";    
        }
        mysqli_free_result($res);
    ?>

</div>


<?php
if (isset($_POST['nameSubmit'])) 
{
    $newFirstName = $user['firstName'];
    $newLastName = $user['lastName'];

    $sql_update_name = "UPDATE members SET firstName = '$newFirstName', lastName = '$newLastName' WHERE email = '$email' AND username = '$username'";
    echo $sql_update_name;
}
else if (isset($_POST['usernameSubmit']))
{

}
else if (isset($_POST['passwordSubmit']))
{

}
?>

<div class="grid">
    <div class="grid-col-1of3">
        <form action="account.php" method="post">
            New First Name: <input type="text" name="firstName" value="<?php echo h($user['firstName']); ?>" /><br />
            New Last Name: <input type="text" name="lastName" value="<?php echo h($user['lastName']); ?>" /><br /><br />
            <input type="submit" name="nameSubmit" value="Update" />
        </form>
    </div>

    <div class="grid-col-1of3">
        <form action="account.php" method="post">
            New Username: <input type="text" name="username" value="<?php echo h($user['username']); ?>" /><br /><br />
            <input type="submit" name="usernameSubmit" value="Update" />
        </form>
    </div>

    <div class="grid-col-1of3">
        <form action="account.php" method="post">
            New Password: <input type="password" name="password" value="" /><br />
            Confirm Password: <input type="password" name="confirm_password" value="" /><br /><br />
            <input type="submit" name="passwordSubmit" value="Update" />
        </form>
    </div>
</div>

<?php $db->close(); ?>
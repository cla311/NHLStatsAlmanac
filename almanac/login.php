<?php
session_start();
require('../required/nav.php');
require('../required/functions.php');
?>

<?php
require_ssl(); // set to https
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && $_SESSION['username']) {
    $email = $_SESSION['user_email'];
    $firstName = $_SESSION['firstName'];
    $username = $_SESSION['username'];

    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/NHLStatsAlmanac/almanac/index.php');
    exit();
} else {
    $email = $_SESSION['user_email'] = [];
    $firstName = $_SESSION['firstName'] = [];
    $username = $_SESSION['username'] = [];
}

$errors = [];
$email = '';
$password = '';

// use post request
if (is_post_request()) {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Validations
    if (is_blank($email)) {
        $errors[] = "Email cannot be blank.";
    }
    if (is_blank($password)) {
        $errors[] = "Password cannot be blank.";
    }

    // if there were no errors, try to login
    if (empty($errors)) {
        // Using one variable ensures that msg is the same
        $login_failure_msg = "Log in was unsuccessful.";

        $user = find_user_by_email($email);
        if ($user) {

            // if password matches
            if (password_verify($password, $user['password'])) {
                log_in_user($user);

                if (!empty($_SESSION['playerID']) && !empty($_SESSION['name'])) { // if user wanted to add a player to the team, redirect to the player info page
                    header('Location: http://' . $_SERVER['HTTP_HOST']
                            . '/NHLStatsAlmanac/almanac/details.php?playerID='
                            . $_SESSION['playerID']);
                    exit();
                } else { // go to home page
                    header('Location: http://' . $_SERVER['HTTP_HOST']
                            . '/NHLStatsAlmanac/almanac/index.php');
                    exit();
                }
            } else {
                // email found, but password does not match
                $errors[] = $login_failure_msg;
                $errors[] = "Email found, but password does not match.";
            }
        } else {
            // no email found
            $errors[] = $login_failure_msg;
        }
    }
}
?>

<div class="grid">
    <div class="grid-col-1of3">
        <h1 class="log">Login</h1>

        <?php echo display_errors($errors); ?>

        <!-- sign in form -->
        <form action="login.php" method="post">
            <div class="input">
                Email:<br />
                <input type="text" name="email" value="<?php echo h($email); ?>" /><br /><br />
            </div>
            <div class="input">
                Password:<br />
                <input type="password" name="password" value="" /><br /><br />
            </div>
            <div class="input" id="bottom-space">
                <input type="submit" name="submit" value="Login"/>
            </div>
        </form>

        <!-- link to register page -->
        <p class="center">Not registered yet? <a class="blue" href="register.php">Register here</a></p> 
    </div>
</div>

<?php
session_start();
require_once('../required/nav.php');
require_once('../required/functions.php');
require_ssl(); // set to https
// if user accessed this page when logged in, log them out
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && !empty($_SESSION['username'])) {
    unset($_SESSION['user_email']);
    unset($_SESSION['firstName']);
    unset($_SESSION['username']);
    session_destroy();
    echo "You were logged out automatically.";
}

// use post request
if (is_post_request()) {

    // get new user information
    $user = [];
    $user['firstName'] = $_POST['firstName'] ?? '';
    $user['lastName'] = $_POST['lastName'] ?? '';
    $user['email'] = $_POST['email'] ?? '';
    $user['username'] = $_POST['username'] ?? '';
    $user['password'] = $_POST['password'] ?? '';
    $user['confirm_password'] = $_POST['confirm_password'] ?? '';

    $result = insert_user($user); // insert user into database
    if ($result === true) {
        log_in_user($user); // log the user in
        if (!empty($_SESSION['playerID']) && !empty($_SESSION['name'])) {
            header('Location: http://' . $_SERVER['HTTP_HOST']
                    . '/NHLStatsAlmanac/almanac/details.php?playerID='
                    . $_SESSION['playerID']);
            exit();
        } else {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/NHLStatsAlmanac/almanac/index.php');
            exit();
        }
    } else {
        $errors = $result;
        print_r($errors);
    }
    log_in_user($user); // log user in
} else {
    // display the blank form
    $user = [];
    $user["firstName"] = '';
    $user["lastName"] = '';
    $user["email"] = '';
    $user['username'] = '';
    $user['password'] = '';
    $user['confirm_password'] = '';
}
?>


<div class="grid">
    <div class="grid-col-1of3">
        <h1 class="log">Create User</h1>

        <form action="register.php" method="post">
            <div class="input">
                <dl>
                    First Name:<br /><input type="text" name="firstName" value="<?php echo h($user['firstName']); ?>" />
                </dl>
                <br />
            </div>

            <dl>
                Last Name:<br />
                <input type="text" name="lastName" value="<?php echo h($user['lastName']); ?>" />
                <br /><br />
            </dl>

            <div class="input">
                <dl>
                    Email:<br />
                    <input type="email" name="email" value="<?php echo h($user['email']); ?>" /><br />
                </dl>
                <br />
            </div>

            <div class="input">
                <dl>
                    Username:<br />
                    <input type="text" name="username" pattern="^[A-Za-z0-9]*$" title="Username may only contain letters and numbers" value="<?php echo h($user['username']); ?>" /><br />
                </dl>
                <br />
            </div>

            <div class="input">
                <dl>
                    Password:<br />
                    <input type="password" name="password" value="" />
                </dl>
                <br />
            </div>

            <div class="input">
                <dl>
                    Confirm Password:<br />
                    <input type="password" name="confirm_password" value="" />
                </dl>
                <br />
            </div>
            <input type="submit" name="submit" value="Create user" />
        </form>
    </div>
</div>
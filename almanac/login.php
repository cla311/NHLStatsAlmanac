<?php 
require('../required/nav.php');
require('../required/functions.php');
?>

<?php
session_start();

require_ssl(); // set to https

// check for user login
if (!empty($_SESSION['user_email']) && !empty($_SESSION['firstName']) && $_SESSION['username']) {
  $email = $_SESSION['user_email'];
  $firstName = $_SESSION['firstName'];
  $username = $_SESSION['username'];
  // if user is logged in, go to logout page
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/NHLStatsAlmanac/almanac/logout.php');
  exit();
} else { // otherwise, go to login page
  $email = $_SESSION['user_email'] = [];
  $firstName = $_SESSION['firstName'] = [];
  $username = $_SESSION['username'] = [];
}

$errors = [];
$email = '';
$password = '';

// use post request
if(is_post_request()) {

  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  // Validations
  if(is_blank($email)) {
    $errors[] = "email cannot be blank.";
  }
  if(is_blank($password)) {
    $errors[] = "Password cannot be blank.";
  }

  // if there were no errors, try to login
  if(empty($errors)) {
    // Using one variable ensures that msg is the same
    $login_failure_msg = "Log in was unsuccessful.";

    $user = find_user_by_email($email);
    if($user) {

      // if password matches
      if(password_verify($password, $user['password'])) {
        log_in_user($user);

        // if (!empty($_SESSION['productID']) && !empty($_SESSION['productName'])) { // if user was trying to save an item to their watchlist
        //   header('Location: http://'.$_SERVER['HTTP_HOST'].'/rdonnell/A4/addtowatchlist.php');
        //   exit();
        // } else { // otherwise, send user to model list
          header('Location: http://'.$_SERVER['HTTP_HOST'].'/NHLStatsAlmanac/almanac/lookup.php');
        //   exit();
        // }
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

<?php
// only show login info they aren't logged in
if (empty($_SESSION['user_email']) && empty($_SESSION['firstName']) && empty($_SESSION['username'])) {
?>
  <h1>Log in</h1>

  <?php echo display_errors($errors); ?>

  <form action="login.php" method="post">
    Email:<br />
    <input type="text" name="email" value="<?php echo h($email); ?>" /><br /><br />
    Password:<br />
    <input type="password" name="password" value="" /><br /><br />
    <input type="submit" name="submit" value="Submit"/>
  </form>

  <p>Not registered yet? <a href="register.php">Register here<a></p>

<?php
} else { // otherwise, don't let them
  echo "You are already logged in.";
}
?>

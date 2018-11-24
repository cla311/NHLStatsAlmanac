<?php
require_once('connection.php');

// set to https
function require_ssl() {
  if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    exit();
  }
}

function url_for($script_path) {
  // add the leading '/' if not present
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return $script_path;
}

function h($string="") {
  return htmlspecialchars($string);
}

function redirect_to($location) {
  header("Location: " . $location);
  exit;
}

// if post method is used in form
function is_post_request() {
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function display_errors($errors=array()) {
  $output = '';
  if(!empty($errors)) {
    $output .= "<div class=\"errors\">";
    $output .= "Please fix the following errors:";
    $output .= "<ul>";
    foreach($errors as $error) {
      $output .= "<li>" . h($error) . "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}

// check for blank values
function is_blank($value) {
    return !isset($value) || trim($value) === '';
  }

// identify user
function find_user_by_email($email) {
  global $db;

  $sql = "SELECT * FROM members ";
  $sql .= "WHERE email='" . db_escape($db, $email) . "' ";
  $sql .= "LIMIT 1";
  $result = mysqli_query($db, $sql);
  confirm_result_set($result);
  $email = mysqli_fetch_assoc($result); // find first
  mysqli_free_result($result);
  return $email; // returns an assoc. array
}

// save product values
function get_product($code, $name) {
  $_SESSION['productID'] = $code;
  $_SESSION['productName'] = $name;
}

// save player instance
function get_player($id, $name) {
  $_SESSION['playerID'] = $id;
  $_SESSION['name'] = $name;
} 

// Performs all actions necessary to log in an user
function log_in_user($user) {
// Renerating the ID protects the user from session fixation.
  $_SESSION['user_email'] = $user['email'];
  $_SESSION['firstName'] = $user['firstName'];
  $_SESSION['username'] = $user['username'];
  return true;
}

// checks if user is logged in
function is_logged_in()
{
  return !empty($_SESSION['user_email']);
}

//  create require_login() which calls at the top of any page which needs to
// require a valid login before granting acccess to the page.
function require_login()
{
  if (!is_logged_in())
  {
    return redirect_to(url_for("/NHLStatsAlmanac/almanac/login.php"));
  }

  return;
}

// insert user into database
function insert_user($user) {
    global $db;

    $errors = validate_user($user);
    if (!empty($errors)) {
      return $errors;
    }

    $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT); // encrypt password

    $sql = "INSERT INTO members ";
    $sql .= "(firstName, lastName, email, username, password) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $user['firstName']) . "',";
    $sql .= "'" . db_escape($db, $user['lastName']) . "',";
    $sql .= "'" . db_escape($db, $user['email']) . "',";
    $sql .= "'" . db_escape($db, $user['username']) . "',";
    $sql .= "'" . db_escape($db, $hashed_password) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);

    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  // disconnect from database
  function db_disconnect($db) {
    if(isset($db)) {
      mysqli_close($db);
    }
  }

  // make sure user has totally filled out the registration form
  function validate_user($user) {
    $errors = array();

    if(is_blank($user['firstName'])) {
      array_push($errors,"First name cannot be blank.");
      // $errors[] = "First name cannot be blank.";
    }

    if(is_blank($user['lastName'])) {
      array_push($errors,"Last name cannot be blank.");
      // $errors[] = "Last name cannot be blank.";
    }

    if(is_blank($user['email'])) {
      array_push($errors,"Email cannot be blank.");
      // $errors[] = "Email cannot be blank.";
    }

    if(is_blank($user['username'])) {
      array_push($errors,"Username cannot be blank.");
      // $errors[] = "Username cannot be blank.";
    }

    if(is_blank($user['password'])) {
      array_push($errors,"Password cannot be blank.");
      // $errors[] = "Password cannot be blank.";
    }

    if(is_blank($user['confirm_password'])) {
      array_push($errors,"Confirm password cannot be blank.");
      // $errors[] = "Confirm password cannot be blank.";
    } else if ($user['password'] != $user['confirm_password']) {
      array_push($errors,"Password and confirm password must match.");
      // $errors[] = "Password and confirm password must match.";
    }

    return $errors;
  }

  function db_escape($connection, $string) {
    return mysqli_real_escape_string($connection, $string);
  }

  function confirm_result_set($result_set) {
    if (!$result_set) {
    	exit("Database query failed.");
    }
  }

  // function for showing each product item as link in a list
  function format_name_as_link($id,$name,$page) {
  	echo "<a href=\"$page?playerID=$id\">$name</a>";
    }

    function fantasy_team_page($page,$title)
    {
      echo "$page?fantasyTeamID=$title";
    }
?>

<?php
/**
 * Register a user.
 *
 * This script get POST vars sent by the form and register a new user if everything is good. All const are referenced in
 * init.php.
 *
 * @author      Alexis L. <293287@supinfo>
 * @version     1.0
 * @see         init.php
 */

require_once "../init.php";

############################
# Check vars
############################

$username = $_POST["username"] ?? null;
$email = $_POST["email"] ?? null;
$password1 = $_POST["password1"] ?? null;
$password2 = $_POST["password2"] ?? null;

if (is_null($username))     { redirectTo("../register.php?error=".USR_NOT_VALID); }
if (is_null($email))        { redirectTo("../register.php?error=".EMAIL_NOT_VALID); }
if (is_null($password1))    { redirectTo("../register.php?error=".PASSWORD_NOT_VALID); }
if (is_null($password2))    { redirectTo("../register.php?error=".PASSWORD_NOT_VALID); }

############################
# Check username
############################

// Check length
$usernameLen = strlen($username);

if ($usernameLen < 1 OR $usernameLen > 32) { redirectTo("../register.php?error=".USR_NOT_VALID); }

// Check availability
$usrResult = PDOFactory::sendQuery($db, 'SELECT user_id FROM users WHERE username = :username', ["username" => $username]);

if ($usrResult) { redirectTo("../register.php?error=".USR_ALREADY_USED); }

############################
# Check email
############################

// Check validity
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { redirectTo("../register.php?error=".EMAIL_NOT_VALID); }

// Check availability
$emailResult = PDOFactory::sendQuery($db, 'SELECT user_id FROM users WHERE email = :email', ["email" => $email]);

if ($emailResult) { redirectTo("../register.php?error=".EMAIL_ALREADY_USED); }

############################
# Check password
############################

// Check if passwords match
if ($password1 !== $password2) { redirectTo("../register.php?error=".PASSWORD_DONT_MATCH); }

// Are the passwords secure?
$p_length_valid = (strlen($password1) >= 8);
$p_one_number = (preg_match("#[0-9]+#", $password1));
$p_one_lower_char = (preg_match("#[a-z]+#", $password1));
$p_one_upper_char = (preg_match("#[A-Z]+#", $password1));
$p_one_special_char = (preg_match("#[\W]+#", $password1));

if (!$p_length_valid OR !$p_one_number OR !$p_one_lower_char OR !$p_one_upper_char OR !$p_one_special_char) {

    redirectTo("../register.php?error=".PASSWORD_NOT_SECURE);
}

// Hash password
$password_peppered = hash_hmac("sha256", $password1, $config["SECURITY"]["pepper"]);
$password_hashed = password_hash($password_peppered, PASSWORD_ARGON2ID);

############################
# Add user
############################

// Get last ID before query
$lastIDBefore = PDOFactory::sendQuery($db, 'SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1')[0]["user_id"];

// Add user
$sql = 'INSERT INTO users(username, email, password) VALUES (:username, :email, :password )';
$vars = [ "username" => $username, "email" => $email, "password" => $password_hashed ];

PDOFactory::sendQuery($db, $sql, $vars, false);

// Get last ID after query
$lastIDAfter = PDOFactory::sendQuery($db, 'SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1')[0]["user_id"];

// Redirect to login page with error or success code
if ($lastIDBefore === $lastIDAfter) { redirectTo("../register.php?error=".UNKNOWN_REGISTER_ERROR); }
else                                { redirectTo("../login.php?success=".REGISTRATION_COMPLETE); }
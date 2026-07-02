<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// The main file contains the database connection, session initializing, and functions, other PHP files will depend on this file.
// Include the configuration file
include_once 'config.php';
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// Connect to the MySQL database using the PDO interface
try {
	$pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to database!');
}
// The below function will check if the user is logged-in and also check the remember me cookie
function check_loggedin($pdo, $redirect_file = 'index.php') {
	// If you want to update the "last seen" column on every page load, you can uncomment the below code
	/*
	if (isset($_SESSION['loggedin'])) {
		$date = date('Y-m-d\TH:i:s');
		$stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
		$stmt->execute([ $date, $_SESSION['id'] ]);
	}
	*/
	// Check for remember me cookie variable and loggedin session variable
    if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme']) && !isset($_SESSION['loggedin'])) {
    	// If the remember me cookie matches one in the database then we can update the session variables.
    	$stmt = $pdo->prepare('SELECT * FROM accounts WHERE rememberme = ?');
    	$stmt->execute([ $_COOKIE['rememberme'] ]);
    	$account = $stmt->fetch(PDO::FETCH_ASSOC);
		// If account exists...
    	if ($account) {
    		// Found a match, update the session variables and keep the user logged-in
    		session_regenerate_id();
    		$_SESSION['loggedin'] = TRUE;
    		$_SESSION['name'] = $account['username'];
			$_SESSION['fname'] = $account['firstname'];
			$_SESSION['lname'] = $account['lastname'];
    		$_SESSION['id'] = $account['id'];
			$_SESSION['role'] = $account['role'];
			$_SESSION['guid'] = $account['guid'];
			// Update last seen date
			$date = date('Y-m-d\TH:i:s');
			$stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
			$stmt->execute([ $date, $account['id'] ]);
    	} else {
    		// If the user is not remembered redirect to the login page.
    		header('Location: ' . $redirect_file);
    		exit;
    	}
    } else if (!isset($_SESSION['loggedin'])) {
    	// If the user is not logged in redirect to the login page.
    	header('Location: ' . $redirect_file);
    	exit;
    }
}
// Send activation email function
function send_activation_email($email, $code) {

$fullname = $_POST['firstname'] . " " . $_POST['lastname'];

$activate_link = activation_link . '?email=' . rawurlencode($email) . '&code=' . $code;
$email_template = str_replace('%link%', $activate_link, file_get_contents('activate_email.html'));
$email_template = str_replace('%fname%', $_POST['firstname'], $email_template);

$mail = new PHPMailer(TRUE);

/* Open the try/catch block. */
try {

    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'gameatl.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'gameroom@gameatl.com';                     //SMTP username
    $mail->Password   = 'SFGEPinball';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;  

   $mail->IsHTML(true); 
   $mail->setFrom('gameroom@gameatl.com', 'Southern-Fried Gaming Expo');
   $mail->addAddress($email, $fullname);
   $mail->Subject = 'SFGE: Account Activation Required';
   $mail->Body = $email_template;
   $mail->send();
}
catch (Exception $e)
{
   /* PHPMailer exception. */
//   echo $e->errorMessage();
}
catch (\Exception $e)
{
   /* PHP exception (note the backslash to select the global namespace Exception class). */
//   echo $e->getMessage();
}
}

// Send autologin email function
function send_autologin_email($email, $code) {

$fullname = $_SESSION['fname'] . " " . $_SESSION['lname'];

$autologin_link = autologin_link . '?guid=' . $code;
$email_template = str_replace('%link%', $autologin_link, file_get_contents(__DIR__ . '/autologin_email.html'));
$subject = 'SFGE: Game Registration Login Link';
$mail = new PHPMailer(TRUE);

try {

    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'gameatl.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'gameroom@gameatl.com';                     //SMTP username
    $mail->Password   = 'SFGEPinball';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;  

   $mail->IsHTML(true); 
   $mail->setFrom('gameroom@gameatl.com', 'Southern-Fried Gaming Expo');
   $mail->addAddress($email, $fullname);
   $mail->Subject = $subject;
   $mail->Body = $email_template;
   $mail->send();
}
catch (Exception $e)
{
   /* PHPMailer exception. */
//   echo $e->errorMessage();
}
catch (\Exception $e)
{
   /* PHP exception (note the backslash to select the global namespace Exception class). */
//   echo $e->getMessage();
}
}

// Send newmachine email function
function send_newmachine_email($machinename, $cleannotes) {

$fullname = $_SESSION['fname'] . " " . $_SESSION['lname'];

$email_template = str_replace('%machinename%', $machinename, file_get_contents('newmachine_email.html'));
$email_template = str_replace('%gamebringer%', $fullname, $email_template);
if (empty($cleannotes)) {
  $cleannotes= "No details left by submitter";
}
$email_template = str_replace('%cleannotes%', $cleannotes, $email_template);
$subject = "SFGE".$_SESSION['showyear'].": Game Registration - " . $machinename;

$mail = new PHPMailer(TRUE);


try {

    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'gameatl.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'gameroom@gameatl.com';                     //SMTP username
    $mail->Password   = 'SFGEPinball';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;  

   $mail->IsHTML(true); 
   $mail->setFrom('gameroom@gameatl.com', 'Southern-Fried Gaming Expo');
   $mail->addAddress('sdewitti@gmail.com', 'Shannon DeWitt');
   $mail->addAddress('nocashvalue80@gmail.com', 'Preston Burt');
   $mail->addAddress('jgeorge@nbi6.com', 'Joe George');
   $mail->addAddress('sfgegameroom@gmail.com ', 'SFGE Gameroom Crew');
   
      
   $mail->Subject = $subject;
   $mail->Body = $email_template;
   $mail->send();
}
catch (Exception $e)
{
   /* PHPMailer exception. */
  echo $e->errorMessage();
}
catch (\Exception $e)
{
   /* PHP exception (note the backslash to select the global namespace Exception class). */
   echo $e->getMessage();
}
}

if (!function_exists('com_create_guid')) {
    function com_create_guid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
?>
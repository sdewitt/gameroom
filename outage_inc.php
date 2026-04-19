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



// Send newmachine email function
function send_outage_email($machineid, $cleanissue) {

$fullname = $_SESSION['fname'] . " " . $_SESSION['lname'];

$email_template = str_replace('%machinename%', $machineid, file_get_contents('newmachine_email.html'));
$email_template = str_replace('%cleannotes%', $cleanissue, $email_template);
$subject = "SFGE: Game Registration - " . $machinename;

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
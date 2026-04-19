<?php
include_once 'config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$mail = new PHPMailer(TRUE);

$activate_link = activation_link . '?email=' . $email;
$email_template = str_replace('%link%', $activate_link, file_get_contents('activate_email.html'));

/* Open the try/catch block. */
try {

    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp-relay.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = false;                                   //Enable SMTP authentication
    $mail->Username   = 'info@southernfriedgameroomexpo.com';                     //SMTP username
    $mail->Password   = 'Pinball3000!';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;  

   $mail->IsHTML(true); 
   $mail->setFrom('info@southernfriedgameroomexpo.com', 'Darth Vader');
   $mail->addAddress('sdewitti@gmail.com');
   $mail->Subject = 'Force';
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
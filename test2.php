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
    $mail->Host       = smtp_host;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = smtp_auth;                                   //Enable SMTP authentication
    $mail->Username   = smtp_user;                     //SMTP username
    $mail->Password   = smtp_pass;                               //SMTP password
    $mail->SMTPSecure = smtp_secure;            //Enable implicit TLS encryption
    $mail->Port       = smtp_port;  

   $mail->IsHTML(true); 
   $mail->setFrom(smtp_from_email, smtp_from_name);
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
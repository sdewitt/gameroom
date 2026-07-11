<?php
  include_once '../config.php';

  $mail->SMTPAuth   = smtp_auth;
  $mail->SMTPSecure = smtp_secure;
  $mail->Port       = smtp_port;
  $mail->Host       = smtp_host;
  $mail->Username   = smtp_user;
  $mail->Password   = smtp_pass;
  $mail->SetFrom(smtp_from_email, smtp_from_name);
  require 'PHPMailer-master/src/PHPMailer.php';
  require 'PHPMailer-master/src/SMTP.php';

  $mail = new PHPMailer();
  $mail->IsSMTP();

  $mail->SMTPDebug  = 0;  
  $mail->SMTPAuth   = TRUE;
  $mail->SMTPSecure = "tls";
  $mail->Port       = 587;
  $mail->Host       = "smtp.gmail.com";
  $mail->Username   = "info@southernfriedgameroomexpo.com";
  $mail->Password   = "Pinball3000!";

  $mail->IsHTML(true);
  $mail->AddAddress("recipient-email", "recipient-name");
  $mail->SetFrom("your-email@gmail.com", "set-from-name");
  $mail->AddReplyTo("reply-to-email", "reply-to-name");
  $mail->AddCC("cc-recipient-email", "cc-recipient-name");
  $mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
  $content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";

  $mail->MsgHTML($content); 
  if(!$mail->Send()) {
    echo "Error while sending Email.";
    var_dump($mail);
  } else {
    echo "Email sent successfully";
  }
?>
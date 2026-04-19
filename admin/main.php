<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Include the root "config.php" and "main.php" files
include_once '../config.php';
include_once '../main.php';
// Check if the user is logged-in
check_loggedin($pdo, '../index.php');
// Fetch account details associated with the logged-in user
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
$stmt->execute([ $_SESSION['id'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Check if the user is an admin...
if ($account['role'] != 'Admin') {
    exit('You do not have permission to access this page!');
}
// Add/remove roles from the list
$roles_list = ['Admin', 'Member'];
// Template admin header
function template_admin_header($title, $selected = 'dashboard', $selected_child = '') {
    // Admin HTML links
    $admin_links = '
        <a href="index.php"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="accounts.php"' . ($selected == 'accounts' ? ' class="selected"' : '') . '><i class="fas fa-users"></i>Accounts</a>
        <div class="sub">
            <a href="accounts.php"' . ($selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Accounts</a>
            <a href="account.php"' . ($selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Account</a>
        </div>
        <a href="machines.php" ><i class="fa-solid fa-joystick"></i>Machines</a>
        <a href="machines_fullscreen.php"><i class="fa-solid fa-maximize"></i>Machines (Full Screen)</a>
        <a href="machines_prior.php"><i class="fa-light fa-joystick"></i>Machines Prior</a>
        <a href="gamelist_editor.php"><i class="fa-light fa-joystick"></i>Game Editor</a>
        <a href="processapproved.php"' . ($selected == 'processapproved' ? ' class="selected"' : '') . '><i class="fa-solid fa-envelope"></i>Send Emails</a>
        <a href="roles.php"' . ($selected == 'roles' ? ' class="selected"' : '') . '><i class="fas fa-list"></i>Roles</a>
        <a href="emailtemplate.php"' . ($selected == 'emailtemplate' ? ' class="selected"' : '') . '><i class="fa-solid fa-envelope-circle-check"></i>Email Templates</a>
        <a href="settings.php"' . ($selected == 'settings' ? ' class="selected"' : '') . '><i class="fas fa-tools"></i>Settings</a>
    ';
    // Indenting the below code may cause an error
echo <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>$title</title>
        <link href="admin.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <script src="https://kit.fontawesome.com/2a4ace1f1d.js" crossorigin="anonymous"></script>
    </head>
    <body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>Admin Panel</h1>
            $admin_links
        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="space-between"></div>
                <a href="about.php" class="right"><i class="fas fa-question-circle"></i></a>
                <a href="account.php?id={$_SESSION['id']}" class="right"><i class="fas fa-user-circle"></i></a>
                <a href="../logout.php" class="right"><i class="fas fa-sign-out-alt"></i></a>
            </header>
EOT;
}
// Template admin footer
function template_admin_footer() {
    // Indenting the below code may cause an error
echo <<<EOT
        </main>
        <script>
        let aside = document.querySelector("aside"), main = document.querySelector("main"), header = document.querySelector("header");
        let asideStyle = window.getComputedStyle(aside);
        if (localStorage.getItem("admin_menu") == "closed") {
            aside.classList.add("closed", "responsive-hidden");
            main.classList.add("full");
            header.classList.add("full");
        }
        document.querySelector(".responsive-toggle").onclick = event => {
            event.preventDefault();
            if (asideStyle.display == "none") {
                aside.classList.remove("closed", "responsive-hidden");
                main.classList.remove("full");
                header.classList.remove("full");
                localStorage.setItem("admin_menu", "");
            } else {
                aside.classList.add("closed", "responsive-hidden");
                main.classList.add("full");
                header.classList.add("full");
                localStorage.setItem("admin_menu", "closed");
            }
        };
        document.querySelectorAll(".tabs a").forEach((element, index) => {
            element.onclick = event => {
                event.preventDefault();
                document.querySelectorAll(".tabs a").forEach((element, index) => element.classList.remove("active"));
                document.querySelectorAll(".tab-content").forEach((element2, index2) => {
                    if (index == index2) {
                        element.classList.add("active");
                        element2.style.display = "block";
                    } else {
                        element2.style.display = "none";
                    }
                });
            };
        });
        if (document.querySelector(".filters a")) {
            let filtersList = document.querySelector(".filters .list");
            let filtersListStyle = window.getComputedStyle(filtersList);
            document.querySelector(".filters a").onclick = event => {
                event.preventDefault();
                if (filtersListStyle.display == "none") {
                    filtersList.style.display = "flex";
                } else {
                    filtersList.style.display = "none";
                }
            };
            document.onclick = event => {
                if (!event.target.closest(".filters")) {
                    filtersList.style.display = "none";
                }
            };
        }
        document.querySelectorAll(".msg").forEach(element => {
            element.querySelector(".fa-times").onclick = () => {
                element.remove();
                history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
                history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
            };
        });
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
        </script>
        <style>
        .ui-jqgrid .ui-jqgrid-htable th div {
            height:auto;
            height:40px; /* your own height in pixel */
            overflow:hidden;
            padding-right:4px;
            padding-top:2px;
            position:relative;
            vertical-align:text-top;
            white-space:normal !important;
            }
            .ui-pager-control .ui-icon, .ui-custom-icon { zoom: 125%; -moz-transform: scale(1.45); }
            .ui-jqgrid .ui-jqgrid-pager .ui-pg-div span.ui-icon { margin: 0px 2px; }
            .ui-jqgrid .ui-jqgrid-pager { height: 28px; }
            .ui-jqgrid .ui-jqgrid-pager .ui-pg-div { line-height: 25px; }
</style>
    </body>
</html>
EOT;
}
// Convert date to elapsed string function
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = ['y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second'];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// Send activation email function
function send_approved_email($name, $email, $machinename) {

$email_template = str_replace('%machinename%', $machinename, file_get_contents('approved_email.html'));
$email_template = str_replace('%fname%', $name, $email_template);

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
   $mail->Subject = 'SFGE: Machine Approved - ' . $machinename ;
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


// Send activation email function
function send_reminder_email($name, $email, $guid) {


$autologin_link = autologin_link . '?guid=' . $guid;
$email_template = str_replace('%link%', $autologin_link, file_get_contents('reminder_email.html'));
$email_template = str_replace('%fname%', $name, $email_template);



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
   $mail->Subject = 'Help make SFGE ' . CURRENT_YEAR . ' the best!';
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
?>

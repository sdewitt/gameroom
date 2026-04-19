<?php
include 'main.php';
// Output message
$msg = '';
// Now we check if the email from the resend activation form was submitted, isset() will check if the email exists.
if (isset($_POST['email'])) {
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
    $stmt->execute([ $_POST['email'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    // If the account exists with the email
    if ($account) {
        // Account exist, the $msg variable will be used to show the output message (on the HTML form)
        send_autologin_email($_POST['email'], $account['guid']);
        echo 'Email Sent!';
    } else {
        echo 'We do not have an account with that email!';
    }
}
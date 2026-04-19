<?php
function validate_email($email_address)
{
if( !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+
([a-zA-Z0-9\._-]+)+$/", $email_address))
{
return false;
}	
return true;
}

include 'main.php';
// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['phone'],  $_POST['email'])) {
	// Could not get the data that should have been sent.
	exit('Please complete the registration form!');
}
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
// Check to see if the email is valid.

// firstname must contain only characters.
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['firstname'])) {
    exit('First name must contain only letters!');
}
// lastname must contain only characters.
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['lastname'])) {
    exit('Last name must contain only letters!');
}
// Password must be between 5 and 20 characters long.
//if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
//	exit('Password must be between 5 and 20 characters long!');
//}
// Check if both the password and confirm password fields match
//if ($_POST['cpassword'] != $_POST['password']) {
//	exit('Passwords do not match!');
//}
// Check if the account with that username already exists
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
$stmt->execute([ $_POST['email'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Store the result, so we can check if the account exists in the database.
if ($account) {
	// Username already exists
	echo 'Email already exists!';
} else {
	// Username doesn't exist, insert new account
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	// Generate unique activation code
	$uniqid = account_activation ? uniqid() : 'activated';
	// Default role
	$role = 'Member';
	// Current date
	$date = date('Y-m-d\TH:i:s');
	// Prepare query; prevents SQL injection
	$guid = com_create_guid();
	$phone = preg_replace("/[^0-9]/", "", $_POST['phone']);
	$stmt = $pdo->prepare('INSERT INTO accounts (firstname, lastname, phone, password, email, activation_code, role, registered, last_seen, guid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
	$stmt->execute([ $_POST['firstname'], $_POST['lastname'], $phone, $password, $_POST['email'], $uniqid, $role, $date, $date, $guid ]);
	// If account activation is required, send activation email
	if (account_activation) {
		// Account activation required, send the user the activation email with the "send_activation_email" function from the "main.php" file
		send_activation_email($_POST['email'], $uniqid);
		echo 'Please check your email to activate your account!';
	} else {
		// Automatically authenticate the user if the option is enabled
		if (auto_login_after_register) {
			// Regenerate session ID
			session_regenerate_id();
			// Declare session variables
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $_POST['username'];
			$_SESSION['id'] = $pdo->lastInsertId();
			$_SESSION['role'] = $role;	
			$_SESSION['guid'] = $guid;		
			echo 'autologin';
		} else {
			echo 'You have successfully registered! You can now login!';
		}
	}
}
?>
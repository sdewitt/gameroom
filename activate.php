<?php
include 'main.php';
// Output message
$msg = '';
// First we check if the email and code exists...
if (isset($_GET['email'], $_GET['code']) && !empty($_GET['code'])) {
	$stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ? AND activation_code = ?');
	$stmt->execute([ $_GET['email'], $_GET['code'] ]);
	// Store the result so we can check if the account exists in the database.
	$account = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if ($account) {
		// Account exists with the requested email and code.
		$stmt = $pdo->prepare('UPDATE accounts SET activation_code = ? WHERE email = ? AND activation_code = ?');
		// Set the new activation code to 'activated', this is how we can check if the user has activated their account.
		$activated = 'activated';
		$stmt->execute([ $activated, $_GET['email'], $_GET['code'] ]);
		$msg = 'Your account is now activated! You can now <a href="index.php">Login</a>.';
		
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
$stmt->execute([ $_GET['email'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $account['username'];
			$_SESSION['id'] = $account['id'];
			$_SESSION['fname'] = $account['firstname'];
			$_SESSION['lname'] = $account['lastname'];
			$_SESSION['role'] = $account['role'];
			$_SESSION['guid'] = $account['guid'];

//send login email
send_autologin_email($_GET['email'], $account['guid']);
header('Location: https://gameroom.gameatl.com/home.php');
exit;

	} else {
		$msg = 'The account is already activated or doesn\'t exist!';
	}
} else {
	$msg = 'No code and/or email was specified!';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Activate Account</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="loggedin">
		<div class="content">
			<p><?=$msg?></p>
		</div>
	</body>
</html>
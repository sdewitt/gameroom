<?php
include 'main.php';
// Output message
$msg = '';
$guid = $_GET['guid'];
if (isset($_GET['guid'])) {
	$stmt = $pdo->prepare('SELECT * FROM accounts WHERE guid = ?');
	$stmt->execute([$_GET['guid']]);
	// Store the result so we can check if the account exists in the database.
	$account = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($account) {
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE guid = ?');
$stmt->execute([ $_GET['guid'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $account['username'];
			$_SESSION['id'] = $account['id'];
			$_SESSION['fname'] = $account['firstname'];
			$_SESSION['lname'] = $account['lastname'];
			$_SESSION['role'] = $account['role'];
			$_SESSION['guid'] = $account['guid'];
			$_SESSION['showyear'] = 2025;
			$_SESSION['showstatus'] = "open";

//send login email
send_autologin_email($_GET['email'], $account['guid']);
header('Location: https://gameroom.gameatl.com/home.php');
exit;

	} else {
		$msg = 'Sorry, something is wrong with this code!';
	}
} else {
	$msg = 'Please Click the link in your email!';
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
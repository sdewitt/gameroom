<?php
include 'main.php';
// No need for the user to see the login form if they're logged-in, so redirect them to the home page
if (isset($_SESSION['loggedin'])) {
	// If the user is not logged in, redirect to the home page.
    header('Location: home.php');
    exit;
}
// Also check if they are "remembered"
if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme'])) {
	// If the remember me cookie matches one in the database then we can update the session variables and the user will be logged-in.
	$stmt = $pdo->prepare('SELECT * FROM accounts WHERE rememberme = ?');
	$stmt->execute([ $_COOKIE['rememberme'] ]);
	$account = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($account) {
		// Authenticate the user
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $account['username'];
		$_SESSION['id'] = $account['id'];
        $_SESSION['role'] = $account['role'];
		$_SESSION['guid'] = $account['guid'];
		// Update last seen date
		$date = date('Y-m-d\TH:i:s');
		$stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
		$stmt->execute([ $date, $account['id'] ]);
		// Redirect to home page
        header('Location: home.php');
		exit;
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Register</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://unpkg.com/jquery-input-mask-phone-number@1.0.15/dist/jquery-input-mask-phone-number.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

		<script>

            $(document).ready(function () {
                $('#phone').usPhoneFormat({
                    format: '(xxx) xxx-xxxx',
                });
            });

        </script>
	</head>
	<body>
		<div class="register">

			<h1>Register</h1>

			<div class="links">
				<a href="resend.php">Login</a>
				<a href="register.php" class="active">Register</a>
			</div>

			<form action="register-process.php" method="post" autocomplete="off">

				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>

				<label for="firstname">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="firstname" placeholder="First Name" id="firstname" required>

				<label for="lastname">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="lastname" placeholder="Last Name" id="lastname" required>
				
				<label for="phone">
					<i class="fas fa-phone"></i>
				</label>
				<input type="text" name="phone" placeholder="Cell Phone" id="phone" required>

				<label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="email" name="email" placeholder="Email" id="email" required>

				<div class="msg"></div>

				<input type="submit" value="Register">

			</form>

		</div>

		<script>
		// AJAX code
		let registrationForm = document.querySelector('.register form');
		registrationForm.onsubmit = event => {
			event.preventDefault();
			fetch(registrationForm.action, { method: 'POST', body: new FormData(registrationForm) }).then(response => response.text()).then(result => {
				if (result.toLowerCase().includes("autologin")) {
					window.location.href = "home.php";
				} else {
					document.querySelector(".msg").innerHTML = result;
				}
			});
		};
		</script>		
	</body>
</html>
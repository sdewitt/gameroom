<?php
include 'main.php';

// Check logged-in
check_loggedin($pdo);
// output message (errors, etc)
$msg = '';
// Retrieve additional account info from the database because we don't have them stored in sessions
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
// In this case, we can use the account ID to retrieve the account info.
$stmt->execute([ $_SESSION['id'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
$phone = preg_replace("/[^0-9]/", "", $_POST['phone']);
$phonewformat = '('.substr($account['phone'], 0, 3).') '.substr($account['phone'], 3, 3).'-'.substr($account['phone'],6);

if (isset($_POST['firstname'], $_POST['email'])) {
	// Make sure the submitted registration values are not empty.
	if (empty($_POST['firstname']) || empty($_POST['email'])) {
		$msg = 'The input fields must not be empty!';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$msg = 'Please provide a valid email address!';
   	} else if (strlen($phone) <> 10)  {
		$msg = 'Phone number must be 10 digits long!';
	} else if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['firstname'])) {
	    $msg = 'Name must contain only letters and numbers!';
	}
	// No validation errors... Process update
		// Check if new username or email already exists in the database
		$stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ? and id != ?');
		$stmt->execute([$_POST['email'], $_SESSION['id']]);
        
		if ($result = $stmt->fetchColumn()) {
			$msg = 'Account already exists with that username and/or email!';
		} elseif ($msg <> null) {
			} else {
			// No errors occured, update the account...
			// If email has changed, generate a new activation code
			$uniqid = account_activation && $account['email'] != $_POST['email'] ? uniqid() : $account['activation_code'];
			$stmt = $pdo->prepare('UPDATE accounts SET firstname = ?, lastname = ?, phone=?, email = ?, activation_code = ? WHERE id = ?');
			$stmt->execute([ $_POST['firstname'], $_POST['lastname'], $phone, $_POST['email'], $uniqid, $_SESSION['id'] ]);
			// Update the session variables
			$_SESSION['fname'] = $_POST['firstname'];
			$_SESSION['lname'] = $_POST['lastname'];

			if (account_activation && $account['email'] != $_POST['email']) {
				// Account activation required, send the user the activation email with the "send_activation_email" function from the "main.php" file
				send_activation_email($_POST['email'], $uniqid);
				// Logout the user
				unset($_SESSION['loggedin']);
				$msg = 'You have changed your email address! You need to re-activate your account!';
			} else {
				// Profile updated successfully, redirect the user back to the profile page
				header('Location: profile.php?updated=true');
				exit;
			}
		}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Profile Page</title>
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
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Southern-Fried Gaming Expo - Game Registration</h1>
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<?php if ($_SESSION['role'] == 'Admin'): ?>
				<a href="admin/index.php" target="_blank"><i class="fas fa-user-cog"></i>Admin</a>
				<?php endif; ?>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
	
		<div class="content editprofile">

			<h2>Edit Profile Page</h2>

			<div class="block">

      <div class="edit">        
			<form id="profilefrm" action="profile.php" method="post" autocomplete=off>
				<label for="firstname">
					<i class="fas fa-user"></i>
				</label>
				<input class="form-control" type="text" name="firstname" value="<?=$account['firstname']?>" placeholder="First Name" id="firstname" required>

				<label for="lastname">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="lastname" value="<?=$account['lastname']?>" placeholder="Last Name" id="lastname" required>

				<label for="role">
					<i class="fas fa-dice-d20"></i>
				</label>
				<input type="text" name="role" value="<?=$account['role']?>" id="role" disabled>
				
				<label for="phone">
					<i class="fas fa-phone"></i>
				</label>
				<input type="text" name="phone" value="<?=$phonewformat?>" placeholder="Cell Phone" id="phone" required>

				<label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="email" name="email" value="<?=$account['email']?>" placeholder="Email" id="email" required>
				<?
				if ($_GET['updated']=="true") {
				echo "<div id='message' class='msg' style='min-height: 34px; height: 34px;  font-size: 22px; align-items:center; text-align:center;'><B>PROFILE UPDATED</b><br></div>";
				} elseif ($msg <> null) {
									echo "<div id='message' class='msg' style='min-height: 34px; height: 34px;  font-size: 22px; align-items:center; text-align:center;'><B>". $msg ."</b><br></div>";
				}
				?>

				<br>
			</form>
			<br>
		<center>	<button class="btn" type="button" onclick="document.getElementById('profilefrm').submit()">Update Profile</button>
</center>
			</div>

		</div>

	</body>
</html>
<script>
setTimeout(function() {
    $('#message').fadeOut(1000);
	}, 4000); // <-- time in milliseconds
</script>
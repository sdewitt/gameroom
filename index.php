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
		$total_count_sql = 'SELECT COUNT(*) AS total FROM gamelist WHERE id =' . $account['id'];
		$total_count = $pdo->query($total_count_sql)->fetchColumn();
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Southern-Fried Gaming Expo - Game Registration</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="css/iofrm-theme3.css">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

<script src="lib/js/jquery.min.js"></script>
<script src="lib/js/popper.min.js"></script>
<script src="lib/js/bootstrap.min.js"></script>
<script src="lib/js/main.js"></script>
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
    <div class="form-body">
        <div class="website-logo">
            <a href="index.php">
                <div class="logo">
                    <img class="logo-size" src="images/sfge-logo.svg" alt="">
                </div>
            </a>
        </div>
        <div class="row">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder">

                </div>
            </div>
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items">
                        <h3>Southern-Fried Gaming<br>Game Bringer Registration</h3>
                        <p>Signup and submit your games for access to all the perks.</p>
                        <div class="page-links">
                            <a href="resend.php">Login</a><a href="index.php" class="active">Register</a>
                        </div>
    <div class="register">                    
			<form id="regform" action="register-process.php" method="post" autocomplete="off">
				<label for="firstname">
					<i class="fas fa-user"></i>
				</label>
				<input class="form-control" type="text" name="firstname" placeholder="First Name" id="firstname" required>

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
  


  <div class="form-group form-check" >
    <input type="checkbox" class="form-check-input" name="concent" id="concent" required>
    <label class="form-check-label" for="concent" style="background-color: #ffffff; font-weight: 400;">I consent to receive text messages regarding the machines I am bringing to the event, exclusively during the event weekend. I can reply STOP at any time to opt out. I also agree to the SFGE SMS privacy policy located here: <a href="https://gameroom.gameatl.com/sms/" target="_blank">https://gameroom.gameatl.com/sms/</a></label>
 </div>


				<div class="msg"></div>

                <div class="form-button">
                    <button id="submit" type="submit" class="ibtn">Register</button>
                </div>

			</form>
      </div>
                        <div class="other-links">
                            <span>Follow us on</span><a href="https://www.facebook.com/southernfriedgamingexpo"><img alt="Facebook" border="0" height="50" src="https://gameroom.gameatl.com/images/facebook-01.png" style="padding: 10px;" width="50" /></a>
									<a target="_blank" rel="noopener noreferrer" href="https://bsky.app/profile/gameatl.com"><img style="padding:10px;" src="https://gameroom.gameatl.com/images/bluesky.png" alt="Twitter" width="50" height="50"></a> 
									<a href="https://www.instagram.com/sfgamingexpo/"><img alt="Instagram" border="0" height="50" src="https://gameroom.gameatl.com/images/instagram-01.png" style="padding: 10px;" width="50" /></a> <a href="https://www.youtube.com/c/Southernfriedgameroomexpo"><img alt="Youtube" border="0" height="50" src="https://gameroom.gameatl.com/images/youtube-01.png" style="padding: 10px;" width="50" /></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<?php
include 'main.php';
check_loggedin($pdo);
$date = date('Y-m-d\TH:i:s');
if ($_POST['voted']) 
{
$stmt = $pdo->prepare('UPDATE votes SET timestamp = ?, bis_em = ?, bis_ss = ?, bis_modern = ?, bis_restore = ?, bis_custom= ?, bis_arcade = ? WHERE userid = ?');
$stmt->execute([ $date, $_POST['bis_em'], $_POST['bis_ss'], $_POST['bis_modern'], $_POST['bis_restore'], $_POST['bis_custom'], $_POST['bis_arcade'], $_SESSION['id']  ]);
}
else {
$stmt = $pdo->prepare('INSERT INTO votes (userid, timestamp, bis_em, bis_ss, bis_modern, bis_restore, bis_custom, bis_arcade) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([ $_SESSION['id'], $date, $_POST['bis_em'], $_POST['bis_ss'], $_POST['bis_modern'], $_POST['bis_restore'], $_POST['bis_custom'], $_POST['bis_arcade'] ]);
};  

?>
<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<meta http-equiv="Refresh" content="5;url=home.php" />
		<title>SFGE - Best In Show</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        </head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Southern-Fried Gaming Expo - Best In Show</h1>
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<?php if ($_SESSION['role'] == 'Admin'): ?>
				<a href="admin/index.php" target="_blank"><i class="fas fa-user-cog"></i>Admin</a>
				<?php endif; ?>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content profile">

			<h2>BEST IN SHOW - VOTING</h2>
<?php

if($stmt) {
echo "<h2>Thanks for submitting or updating your vote.</h2><br> You can change your mind but all voting ends at 11:00am on Sunday July 30th.<br><b>This will redirect to the home page in 5 seconds.</b>";
   };
?>
<?php
include 'main.php';
// Check logged-in
check_loggedin($pdo);
// output message (errors, etc)
$msg = '';


$total_count_sql = 'SELECT COUNT(*) AS total FROM gamelist WHERE ownerid =' . $_SESSION["id"] . " and showyear=" . $_SESSION['showyear'] ;
$total_count = $pdo->query($total_count_sql)->fetchColumn();

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Profile Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
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

		<div class="content profile">

			<h2>Gamebringer - Reward Levels*</h2>*Rewards below assume that the machine(s) are approved, arrive at the event, and are in a playable condition. 
<div class="block">
<?
If ($total_count == 0) {
	echo "<div class='gamebringer_selected_header' align=center>Currently you are not signed up to bring any games!</div><br>";
}

If ($total_count == 1) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring one (1) game</strong>&nbsp;and receive</h3>
<ul>
<li><strong>One (1)&nbsp;FREE 3-Day Weekend Pass&nbsp;</strong></li>
<li><strong>Free</strong>&nbsp;event poster (1)</li>
<li><strong>PLUS&nbsp;early access</strong>&nbsp;to the&nbsp;game&nbsp;floor on<strong>&nbsp;Saturday AND Sunday&nbsp;</strong>mornings (9AM til 10AM exclusive to game bringers)</li>
<li><strong>PLUS</strong>&nbsp;(1) entry into our prize drawing (Tons of prizes, including&nbsp;our machine giveaway)</li>
</ul>
</div>
<br>
<?
If ($total_count == 2) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring two (2) games</strong>&nbsp;and receive</h3>
<ul>
<li><strong>Two</strong>&nbsp;(2)&nbsp;<strong>FREE 3-Day Weekend Passes</strong><strong>&nbsp;</strong></li>
<li><strong>Free</strong>&nbsp;event poster (1)</li>
<li><strong>PLUS&nbsp;early access</strong>&nbsp;to the&nbsp;game&nbsp;floor on<strong>&nbsp;Saturday AND Sunday&nbsp;</strong>mornings (9AM til 10AM exclusive to game bringers)</li>
<li><strong>PLUS</strong>&nbsp;(2) entries&nbsp;into our&nbsp;<strong>Prize Drawing</strong>&nbsp;(Tons of prizes, including&nbsp;our machine giveaway)</li>
</ul>
</div>
<br>
<?
If ($total_count == 3) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring three (3) games</strong>&nbsp;and receive&nbsp;</h3>
<ul>
<li><strong>Three</strong>&nbsp;(3)&nbsp;<strong>FREE 3-Day Weekend Passes</strong><strong>&nbsp;</strong></li>
<li><strong>Free</strong>&nbsp;event poster (1)</li>
<li><strong>PLUS&nbsp;early access</strong>&nbsp;to the&nbsp;game&nbsp;floor on<strong>&nbsp;Saturday AND Sunday&nbsp;</strong>mornings (9AM til 10AM exclusive to game bringers)</li>
<li><strong>PLUS</strong>&nbsp;one (1)&nbsp;<strong>Truly Awesome Shirt</strong>&nbsp;that will help you remember that&nbsp;weekend&nbsp;<strong>FOREVER</strong></li>
<li><strong>PLUS</strong>&nbsp;(3) entries&nbsp;into our&nbsp;<strong>Prize Drawing</strong>&nbsp;&nbsp;(Tons of prizes, including&nbsp;our machine giveaway)</li>
</ul>
</div>
<br>
<?
If ($total_count == 4 or $total_count == 5) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring four or five (4/5) games</strong>&nbsp;and receive&nbsp;</h3>
<ul>
<li><strong>Four</strong>&nbsp;(4)&nbsp;<strong>FREE 3-Day Weekend Passes</strong><strong>&nbsp;</strong></li>
<li><strong>Free</strong>&nbsp;event poster (1)</li>
<li><strong>PLUS&nbsp;early access</strong>&nbsp;to the&nbsp;game&nbsp;floor on<strong>&nbsp;Saturday AND Sunday&nbsp;</strong>mornings (9AM til 10AM exclusive to game bringers)</li>
<li><strong>PLUS</strong>&nbsp;one&nbsp;<strong>Truly Awesome Shirt</strong>&nbsp;that will help you remember that&nbsp;weekend&nbsp;<strong>FOREVER</strong></li>
<li><strong>PLUS</strong>&nbsp;(4 or 5) entries into our&nbsp;<strong>Prize Drawing</strong>&nbsp;(Tons of prizes, including&nbsp;our machine giveaway)</li>
<li><strong>PLUS</strong>&nbsp;access for&nbsp;<strong>one</strong>&nbsp;(1) to our All-Star&nbsp;<strong>VIP PARTY</strong>&nbsp;on Saturday Night</li>
</ul>
</div>
<br>
<?
If ($total_count == 6 or $total_count == 7) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring six or seven (6/7) games</strong>&nbsp;and receive&nbsp;</h3>
<ul>
<li><strong>All the perks of the 4/5-game level!</strong></li>
<li><strong>PLUS</strong>&nbsp;(6 or 7) entries into our&nbsp;<strong>Prize Drawing</strong>&nbsp;(Tons of prizes, including&nbsp;our machine giveaway)</li>
<li><strong>PLUS $175!*</strong></del><strong>&nbsp;</strong></li>
</ul>
</div>
<br>
<?
If ($total_count == 8 or $total_count == 9) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring eight or nine (8/9) games</strong>&nbsp;and receive&nbsp;</h3>
<ul>
<li><strong>All the perks of the 4/5-game level!</strong></li>
<li><strong>PLUS</strong>&nbsp;(8 or 9) entries into our&nbsp;<strong>Prize Drawing</strong>&nbsp;(Tons of prizes, including&nbsp;our machine giveaway)</li>
<li><strong>PLUS</strong>&nbsp;One additional VIP ticket!</li>
<li><strong>PLUS $250!*</strong></del><strong>&nbsp;</strong></li>
</ul>
</div>
<br>
<?
If ($total_count == 10) {
	echo "<div class='gamebringer_selected_header' align=center>CURRENT REWARD LEVEL</div><div class='gamebringer_selected'>";
} else {
	echo "<div class='gamebringer_notselected'>";
}
?>
<h3><strong>Bring ten (10) or more games</strong>&nbsp;and receive&nbsp;</h3>
<ul>
<li><strong>All the perks of the 4/5-game level plus!</strong></li>
<li><strong>PLUS</strong>&nbsp;(10 or more) entries into our&nbsp;<strong>Prize Drawing</strong>&nbsp;(Tons of prizes, including&nbsp;our machine giveaway)</li>
<li><strong>PLUS&nbsp;</strong>One additional VIP ticket!</li>
<li><strong>PLUS $400!*</strong></del><strong>&nbsp;</strong></li>
</ul>
</div>

<div>
<h3><strong>Want to bring more than 10 games?? <a href="https://gameatl.com/contact-us/" target=_blank>Contact Us</a></strong></h3>
</div>

</div>
</body>
</html>
<?php
include 'main.php';
check_loggedin($pdo);

//$stmt = $dbh->prepare('SELECT name, colour, calories FROM fruit');
//$stmt->execute();


$sqlstmt = "SELECT * FROM `votes` WHERE userid=".$_SESSION['id'];
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$voted = $stmt->fetch();

   
$sqlstmt = "SELECT * FROM `gamelist` WHERE showyear=".$_SESSION['showyear']." and awards=1 order by gametitle";
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$bis_em = $stmt->fetchAll();

$sqlstmt = "SELECT * FROM `gamelist` WHERE showyear=".$_SESSION['showyear']." and awards=2 order by gametitle";
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$bis_ss = $stmt->fetchAll();

$sqlstmt = "SELECT * FROM `gamelist` WHERE showyear=".$_SESSION['showyear']." and awards=3 order by gametitle";
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$bis_modern = $stmt->fetchAll();

$sqlstmt = "SELECT * FROM `gamelist` WHERE showyear=".$_SESSION['showyear']." and awards=4 order by gametitle";
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$bis_restore = $stmt->fetchAll();

$sqlstmt = "SELECT * FROM `gamelist` WHERE showyear=".$_SESSION['showyear']." and awards=5 order by gametitle";
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$bis_custom = $stmt->fetchAll();

$sqlstmt = "SELECT * FROM `gamelist` WHERE showyear=".$_SESSION['showyear']." and awards=6 order by gametitle";
$stmt = $pdo->prepare($sqlstmt);
$stmt->execute();
$bis_arcade = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
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
            
            <div class="block">     
            <form action="vote_submit.php" method="post">
			
            <?PHP
            echo "<input hidden name='userid' value='" . $_SESSION['id'] . "'>";
            ?>
            
            <label for="bis_em">Best In Show - EM Pinball</label>
            <select name="bis_em" id="bis_em" style='width: 500px;'>
            <Option></Option>
            <?php
            if ($bis_em)
            foreach ($bis_em as $bis_em) {
              echo "<option value=". $bis_em['gamelistid'];
              if ($bis_em['gamelistid'] == $voted['bis_em']) echo " selected";
              echo " >" . $bis_em['gametitle'] ." (".$bis_em['yearlistid'].")"."</Option>";
            } else {
            echo "<option>No Entries</option>";
            }
            ?>
            </select>
            
            <label for="$bis_ss">Best In Show - Solid State Pinball</label>
            <select name="bis_ss" id="bis_ss" style='width: 500px;'>
            <Option></Option>
            <?php
            if ($bis_ss)
            foreach ($bis_ss as $bis_ss) {
              echo "<option value=". $bis_ss['gamelistid'];
              if ($bis_ss['gamelistid'] == $voted['bis_ss']) echo " selected";
              echo " >" . $bis_ss['gametitle'] ." (".$bis_ss['yearlistid'].")". "</Option>";
              } else {
            echo "<option>No Entries</option>";
            }
            ?>
            </select>

            <label for="$bis_modern">Best In Show - Modern Pinball</label>
            <select name="bis_modern" id="bis_modern" style='width: 500px;'>
            <Option></Option>
            <?php
            if ($bis_modern)
            foreach ($bis_modern as $bis_modern) {
              echo "<option value=". $bis_modern['gamelistid'];
              if ($bis_modern['gamelistid'] == $voted['bis_modern']) echo " selected";
              echo " >" . $bis_modern['gametitle'] ." (".$bis_modern['yearlistid'].")". "</Option>";
            } else {
            echo "<option>No Entries</option>";
            }
            ?>
            </select>

            <label for="$bis_restore">Best In Show - Restoration</label>
            <select name="bis_restore" id="bis_restore" style='width: 500px;'>
            <Option></Option>
            <?php
            if ($bis_restore)
            foreach ($bis_restore as $bis_restore) {
              echo "<option value=". $bis_restore['gamelistid'];
              if ($bis_restore['gamelistid'] == $voted['bis_restore']) echo " selected";
              echo " >" . $bis_restore['gametitle'] ." (".$bis_restore['yearlistid'].")". "</Option>";
            } else {
            echo "<option>No Entries</option>";
            }
            ?>
            </select>

            <label for="$bis_custom">Best In Show - Custom Game</label>
            <select name="bis_custom" id="bis_custom" style='width: 500px;'>
            <Option></Option>
            <?php
            if ($bis_custom)
            foreach ($bis_custom as $bis_custom) {
              echo "<option value=". $bis_custom['gamelistid'];
              if ($bis_custom['gamelistid'] == $voted['bis_custom']) echo " selected";
              echo " >" . $bis_custom['gametitle'] ." (".$bis_custom['yearlistid'].")". "</Option>";
            } else {
            echo "<option>No Entries</option>";
            }
            ?>
            </select>
            <BR>

            <label for="$bis_arcade">Best In Show - Arcade Game</label>
            <select name="bis_arcade" id="bis_arcade" style='width: 500px;'>
            <Option></Option>
            <?php
            if ($bis_arcade)
            foreach ($bis_arcade as $bis_arcade) {
              echo "<option value=". $bis_arcade['gamelistid'];
              if ($bis_arcade['gamelistid'] == $voted['bis_arcade']) echo " selected";
              echo " >" . $bis_arcade['gametitle'] ." (".$bis_arcade['yearlistid'].")". "</Option>";
            } else {
            echo "<option>No Entries</option>";
            }
            ?>
            </select>
            <BR>
     
            <?PHP
            echo "<input hidden name='userid' value='" . $_SESSION['id'] . "'>";
            echo "<input hidden name='voted' value='" . $voted . "'>";
            ?>
            <div>
			<button type="button" class="profile-btn-cancel" onclick="window.history.back();">Cancel</button><input class="profile-btn" type="submit" value="VOTE NOW">
			</div>
        </p>
		</div>
        </form>
    </body>

</html>


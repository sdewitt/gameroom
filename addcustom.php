<?php
include 'main.php';
check_loggedin($pdo);
?>
<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>SFGE - Game Registration</title>
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

			<h2>Add Custom Machine</h2>
            
            <div class="block">     
            <form action="addcustom2.php" method="post">
			
            <label for="machinename">Machine Name</label>
            <input name="machinename" style='width: 500px;' autocomplete="off" autofocus>
            
            <label for="manufacturer">Built By</label>
            <input name="manufacturer" style='width: 500px;' value='<?=$_SESSION['fname'] . " " . $_SESSION['lname'];?>' autocomplete="off">

            <?PHP
            echo "<input hidden name='userid' value='" . $_SESSION['id'] . "'>";
            ?>
            
            <label for="awards">Awards Category</label>
            <select name="awards" id="awards" style='width: 500px;'>
                <Option value=5>Best In Show - Custom</Option>
                <Option></Option>
            </select>
            
            <label for="builtyear">Year Built</label>
            <select name="builtyear" id="builtyear" style='width: 500px;'>
            <Option value=2022>2024</Option>
            <Option value=2022>2023</Option>
            <Option value=2022>2022</Option>
            <Option value=2021>2021</Option>
            <Option value=2020>2020</Option>
            <Option value=2019>2019</Option>
            <Option value=2018>2018</Option>
            <Option value=2017>2017</Option>
            <Option value=2016>2016</Option>
            <Option value=2015>2015</Option>
            <Option value=2014>2014</Option>
            <Option value=2013>2013</Option>
            <Option value=2012>2012</Option>
            <Option value=2011>2011</Option>
            <Option value=2010>2010</Option>
            <Option value=2009>2009</Option>
            <Option value=2008>2008</Option>
            <Option value=2007>2007</Option>
            <Option value=2006>2006</Option>
            <Option value=2005>2005</Option>
            <Option value=2004>2004</Option>
            <Option value=2003>2003</Option>
            <Option value=2002>2002</Option>
            <Option value=2001>2001</Option>
            <Option value=2000>2000</Option>
            </select>
            <BR>
            <label id="noteslabel" for="notes">Anything special we need to know?</label>
            <textarea id="notes" name="notes" rows="3" style='width: 500px;'></textarea>
            <?PHP
            echo "<input hidden name='userid' value='" . $_SESSION['id'] . "'>";
            ?>
            <div>
			<button type="button" class="profile-btn-cancel" onclick="window.history.back();">Cancel</button><input class="profile-btn" type="submit" value="Add Custom Machine">
			</div>
        </p>
		</div>
        </form>
    </body>

</html>
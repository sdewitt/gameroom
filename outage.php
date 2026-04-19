<?php
include 'outage_inc.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>SFGE - Game Registration</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        </head>
	<body class="loggedin">
		<div class="content profile">

			<h2>Machine Down</h2>
            
            <div class="block">     
            <form action="outage2.php" method="post">
			<CENTER><h3>Please enter the ID of the arcade/pinball machine (located on the sticker) having an issue and one of our techs will look into the issue ASAP. THANKS!</h3></CENTER>
            <label for="machineid">Machine ID *</label>
            <input name="machineid" style='width: 100%;' autocomplete="off" autofocus>
			<p>Issue Description (Optional)</p>
            <textarea id="issue" name="issue" rows="6" style='width: 100%;'></textarea>
			<BR>
			<CENTER><input class="profile-btn" type="submit" value="REPORT MACHINE ISSUE"></CENTER>
		</div>
        </form>
		* Required
    </body>

</html>

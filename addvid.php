<?php
include 'main.php';
check_loggedin($pdo);
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

			<h2>Add Arcade Machine</h2>
            
            <div class="block">     
            <form action="addvid2.php" method="post">
			
            <label for="machinename">Machine Name</label>
            <input name="machinename" style='width: 500px;' autocomplete="off" autofocus>
			<label for="machinename">Manufacturer (if known)</label>
            <input name="manufacturer" style='width: 500px;' autocomplete="off" autofocus>
            <?PHP
            echo "<input hidden name='userid' value='" . $_SESSION['id'] . "'>";
            ?>
			<label for="machinename">Year Built (if known)</label>
			<select name="year" id='date-dropdown'></select>
			<script>
			let dateDropdown = document.getElementById('date-dropdown'); 
			let currentYear = new Date().getFullYear();    
			let earliestYear = 1950;     
			while (currentYear >= earliestYear) {      
				let dateOption = document.createElement('option');          
				dateOption.text = currentYear;      
				dateOption.value = currentYear;        
				dateDropdown.add(dateOption);      
				currentYear -= 1;    
			}
			</script>
<BR>
            <label id="noteslabel" for="notes">Anything special we need to know?</label>
            <textarea id="notes" name="notes" rows="3" style='width: 500px;'></textarea>
<BR><BR>
<input id="awards_question" type="checkbox" name="awards_question" value="1" /> I would like to enter this game in consideration for a "Best in Show" category

			<label id="awardslabel" for="awards">Awards Category*</label>
            <select name="awards" id="awards" style='width: 500px;'>
                <Option></Option>
				<Option value=6>Best In Show - Arcade</Option>
                <Option value=4>Best In Show - Restoration</Option>
            </select>

            <div>
			<button type="button" class="profile-btn-cancel" onclick="window.history.back();">Cancel</button><input class="profile-btn" type="submit" value="Add Arcade Machine">
			</div>
        </p>
		</div>
        </form>
    </body>

</html>
<script>
$(function(){

    $('#awards').hide();  //Hide the elements onload
    $('#awardslabel').hide();          //Hide the elements onload
    $("#date-dropdown").prepend("<option value=' ' selected='selected'></option>");

    $('#awards_question').click(function(){
          if($(this).is(':checked')){
              $('#awards').show();
              $('#awardslabel').show();
          } else {
              $('#awards').hide();
              $('#awardslabel').hide();
          }
    });
});
</script>
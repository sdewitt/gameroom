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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
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

			<h2>Add Pinball</h2>

            <div class="block"> 
            <form action="addpin2.php" method="post">
	
            <label for="opdbid">Machine Name</label>
            <select id="opdbid" name="opdbid" class='game-select form-control' style='width: 500px;'>
                <option value='0'>- Search pinballs -</option>
            </select>
            <label id="noteslabel" for="notes">Anything special we need to know?</label>
            <textarea id="notes" name="notes" rows="3" style='width: 500px;'></textarea>
<BR><BR>
<input id="awards_question" type="checkbox" name="awards_question" value="1" disabled/> I would like to enter this game in consideration for a "Best in Show" category

            <label id="awardslabel" for="awardslabel">Awards Category</label>            
            <select name="awards" id="awards" style='width: 500px;' disabled>
                <Option></Option>
                <Option value=1>Best In Show - EM Pinball</Option>
                <Option value=2>Best In Show - Solid State Pinball</Option>
                <Option value=3>Best In Show - Modern Pinball</Option>
                <Option value=4>Best In Show - Restoration</Option>
            </select>
<br><br>
<input id="tournamentpin" type="checkbox" name="tournamentpin" value="1" disabled/> This pinball has been approved as a Tournament Pinball Machine

            <div>
			<button type="button" class="profile-btn-cancel" onclick="window.history.back();">Cancel</button><input class="profile-btn" type="submit" value="Add Pinball Machine" disabled>
			</div>
        </p>
		</div>
        </form>
    </body>
    <script>

$(document).ready(function() {
    let endpoint = 'https://opdb.org/api/machines/'
    let apiKey = 'sRLmbCeVBkpAmdSVkoO3vDnIf4qb20IQM1XQf4VeDj9ZWx18zmF07Lnge8m9'
    $(".game-select").select2({
        ajax: {
            url: "https://opdb.org/api/search/typeahead",
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term,
                    type: 'public'
                };
            },
            processResults: function(data, params) {
                var resData = [];
                data.forEach(function(value) {
                    if (value.name.toLowerCase().indexOf(params.term.toLowerCase()) > -1)
                        resData.push(value)
                })
                return {
                    results: $.map(resData, function(item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    })
});
$('select').change(function() {
        $('input[type="submit"]').removeAttr('disabled');
        $('#awards').attr('disabled', false);
        $('#awards_question').attr('disabled', false);
        $('#tournamentpin').attr('disabled', false);
        $('#notes').show();  //Hide the elements onload
        $('#noteslabel').show();          //Hide the elements onload

    });
</script>
</html>

<script>
$(function(){

    $('#awards').hide();  //Hide the elements onload
    $('#awardslabel').hide();          //Hide the elements onload
    $('#notes').hide();  //Hide the elements onload
    $('#noteslabel').hide();          //Hide the elements onload

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
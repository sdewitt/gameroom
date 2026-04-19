<html>
<body>

	<script>

		var popUpObj;

		function showModalPopUp(){

			popUpObj=window.open("roles.php","ModalPopUp","width=400," + "height=300");

			popUpObj.focus();

			LoadModalDiv();

		}

	</script>

	<input id="Button1" type="button" value="button" onclick="showModalPopUp()" />

<?php
date_default_timezone_set('America/New_York');
$now = new DateTime();
$future_date = new DateTime('2023-03-25 19:00:00');
$interval = $future_date->diff($now);
echo $interval->format("%a days, %h hours, %i minutes");
?>
</body>
</html>
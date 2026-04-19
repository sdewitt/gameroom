<?php
include 'main.php';

$cleannotes = htmlentities(strip_tags(trim($_POST['notes'])));

send_newmachine_email($_POST['machinename'], $cleannotes);

$date = date('Y-m-d\TH:i:s');
$stmt = $pdo->prepare('INSERT INTO gamelist (ownerid, gametype, gametitle, gameid, manufacturer, builtyear, awards, notes, dateadded, showyear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([ $_POST['userid'], 'c', $_POST['machinename'], '' , $_POST['manufacturer'] , $_POST['builtyear'] , $_POST['awards'], $cleannotes, $date, $_SESSION['showyear'] ]);

if($stmt) {
    header('Location: home.php');
   };
?>
<?php
include 'main.php';
check_loggedin($pdo);
$url = "https://opdb.org/api/machines/" . $_POST['opdbid'] . "?api_token=Q3IWln7wCeBozR40ItPIJKsZ7AsLpWQG81dXIUl173smIhiEJ7KOVIZnXhbB";
$obj = json_decode(file_get_contents($url), true);

$cleannotes = htmlentities(strip_tags(trim($_POST['notes'])));

send_newmachine_email($obj['name'], $cleannotes);

$date=$obj['manufacture_date'];
$ipdb_id=$obj['ipdb_id'];
$yearmade = substr($date, 0, 4);

$date = date('Y-m-d\TH:i:s');
$stmt = $pdo->prepare('INSERT INTO gamelist (ownerid, tournamentpin, gametype, gametitle, gameid, ipdbid, manufacturer, builtyear, awards, notes, dateadded, showyear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([ $_SESSION['id'], $_POST['tournamentpin'], 'p', $obj['name'], $_POST['opdbid'], $ipdb_id, $obj['manufacturer']['name'], $yearmade, $_POST['awards'], $cleannotes, $date, $_SESSION['showyear'] ]);

if($stmt) {
    header('Location: home.php');
   };
?>


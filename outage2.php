<?php
include 'outage_inc.php';

$cleanissue = htmlentities(strip_tags(trim($_POST['issue'])));

send_outage_email($_POST['machinesid'], $cleanissue);

$date = date('Y-m-d\TH:i:s');
$stmt = $pdo->prepare('INSERT INTO machinesissues (machineid, issue, opentime) VALUES (?, ?, ?)');
$stmt->execute([ $_POST['machineid'], $cleanissue, $date ]);

if($stmt) {
    header('Location: outage-thanks.php');
   };
?>
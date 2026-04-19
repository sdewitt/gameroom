<?php
include_once '../config.php';
require '_db.php';

// Find next available yearlistid for prior showyear
$idsPriorYear = $pdo->query("SELECT yearlistid FROM gamelist WHERE showyear = '" . PRIOR_YEAR . "' ORDER BY yearlistid")
               ->fetchAll(PDO::FETCH_COLUMN);
foreach ($idsPriorYear as $id) {

$ids2025 = $pdo->query("SELECT yearlistid FROM gamelist WHERE showyear = '2025' ORDER BY yearlistid")
               ->fetchAll(PDO::FETCH_COLUMN);

$nextID = 1;
foreach ($ids2025 as $id) {
    if ((int)$id == $nextID) {
        $nextID++;
    } else {
        break;
    }
}

echo $nextID;

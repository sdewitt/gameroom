<?php
require '_db.php';

// Find next available yearlistid for showyear 2025
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

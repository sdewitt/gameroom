<?php
include 'main.php';
date_default_timezone_set('America/New_York');
//echo "The time is " . date("h:i:sa");
$date_now = new DateTime();
$date_votingopens = '2023-07-28 16:00:00';
$date_votingcloses = '2023-07-30 11:00:00';
if (new $date_now > new DateTime($date_votingcloses)) {
echo "<button type='button' class='btn-gold' disabled><i class='fa-duotone fa-check-to-slot'></i> Awards Voting</button> (Voting NOW CLOSED)";
} elseif ((new $date_now > new DateTime($date_votingopens)) || ($_SESSION['role'] == 'Admin')) {
echo "<button type='button' class='btn-gold'><i class='fa-duotone fa-check-to-slot'></i> Awards Voting</button> (Voting Closes July 30th at 11:00am)";
} else {
echo "<button type='button' class='btn-gold' disabled><i class='fa-duotone fa-check-to-slot'></i> Awards Voting</button> (Voting Opens July 28th at 4:00pm)";
}



?>
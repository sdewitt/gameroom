<?php
session_start(); // Ensure sessions are enabled if using $_SESSION

date_default_timezone_set("America/New_York"); // Set Eastern Time explicitly

$date_now = new DateTime(); // Now in Eastern
$date_votingopens = new DateTime('2025-06-20 15:00:00'); // 3:00 PM Eastern
$date_votingcloses = new DateTime('2025-06-22 11:00:00'); // 11:00 AM Eastern

if ($date_now > $date_votingcloses) {
    echo "<button type='button' class='btn-gold' disabled><i class='fa-duotone fa-check-to-slot'></i> Awards Voting</button> (Voting NOW CLOSED)";
} elseif (($date_now > $date_votingopens) || ($_SESSION['role'] == 'Admin')) {
    $interval = $date_now->diff($date_votingcloses);
    $time_remaining = $interval->format('%d days, %h hours, %i minutes');
    echo "<a class='btn-gold' href='vote.php'><i class='fa-duotone fa-check-to-slot'></i> Awards Voting</a> (Voting Closes June 22nd at 11:00am — $time_remaining remaining)";
} else {
    echo "<button type='button' class='btn-gold' disabled><i class='fa-duotone fa-check-to-slot'></i> Awards Voting</button> (Voting Opens June 20th at 3:00pm)";
}
?>

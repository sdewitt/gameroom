<?php
include 'main.php';

template_admin_header('Dashboard', 'dashboard');

//Real SQL -- CHANGE THE YEAR
$stmt = $pdo->prepare('select * from accounts where exists (select * from gamelist where accounts.id = gamelist.ownerid and gamelist.showyear=' . PRIOR_YEAR . ') order by firstname');

//Test SQL
//$stmt = $pdo->prepare('select * from accounts where id=2');

$stmt->execute();
$email = $stmt->fetchAll();

// Store the result, so we can check if the account exists in the database.
if ($email) {
	// Username already exists
	echo "Processing Emails to Gamebringers...<br><br>";
  foreach ($email as $email) {
    echo "Processed ". $email['firstname'] ." ". $email['email'] . " " . $email['guid'] . "<br>";
  send_reminder_email($email['firstname'], $email['email'], $email['guid'] );
//    $sqlstmt = "UPDATE gamelist SET emailed = 1 WHERE gamelistid = " . $email['gamelistid'];
//    $affectedRows = $pdo->exec($sqlstmt);
  	}
  echo "Processing Complete<br>"; 
} else {
  echo "Nothing to process";
}

?>

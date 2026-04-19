<?php
include 'main.php';

template_admin_header('Dashboard', 'dashboard');

//$stmt = $pdo->prepare('select accounts.id, gamelist.ownerid, accounts.firstname, accounts.lastname, accounts.email, gamelist.gamelistid, gamelist.gametitle, gamelist.gametype, gamelist.approved from accounts inner join gamelist on accounts.id = 63 where gamelist.approved = 0 order by accounts.email, gamelist.gametype DESC');
$stmt = $pdo->prepare('select accounts.id, gamelist.ownerid, accounts.firstname, accounts.lastname, accounts.email, gamelist.gamelistid, gamelist.emailed, gamelist.gametitle, gamelist.gametype, gamelist.approved from accounts inner join gamelist on accounts.id = gamelist.ownerid where gamelist.approved = 1 and gamelist.emailed = 0 order by accounts.email, gamelist.gametype DESC');

$stmt->execute();
$email = $stmt->fetchAll();

// Store the result, so we can check if the account exists in the database.
if ($email) {
	// Username already exists
	echo "Processing Approvals...<br><br>";
  foreach ($email as $email) {
    echo "Processed ". $email['gametitle'] ." from " . $email['firstname'] . " " . $email['lastname'] . "<br>";
	  send_approved_email($email['firstname'], $email['email'], $email['gametitle'] );
    $sqlstmt = "UPDATE gamelist SET emailed = 1 WHERE gamelistid = " . $email['gamelistid'];
    $affectedRows = $pdo->exec($sqlstmt);
  	}
  echo "Processing Complete<br>"; 
} else {
  echo "Nothing to process";
}

?>
<?php
// Required if your environment does not handle autoloading
require __DIR__ . '/vendor/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

include 'main.php';
$pquery = "SELECT phone FROM accounts where id = " . $_GET['user'];
$phone = $pdo->query($pquery)->fetchColumn();
$pquery = "SELECT lastname FROM accounts where id = " . $_GET['user'];
$lastname = $pdo->query($pquery)->fetchColumn();
$pquery = "SELECT firstname FROM accounts where id = " . $_GET['user'];
$firstname = $pdo->query($pquery)->fetchColumn();
?>
<?php
$message=intval($_GET['message']);
if ($message == 1) {
  $textbody="SFGE Gamebringer: We are having some issues with your machine can you return to check in desk";
} elseif ($message == 2) {
  $textbody="SFGE Gamebringer: We cannot locate the key to your machine, please see desk";
} elseif ($message == 3) {
  $textbody="SFGE Gamebringer: SFGE Gamebringer: Just a TEST MESSAGE";
};


$number = '+1'.$phone;
$screen= "<BR><BR><BR><BR><BR><BR><BR><BR><center><font size=+3><b>Message Sent to ".$firstname." ".$lastname."</b></font>";
echo $screen;
echo "<br>Screen closing in 3 seconds</center>";

$sid = 'ACd13f5914efb6b71d1e9a7e1a3693719b';
$token = '0da9c3a09b7bc0ca9ab8a7a2817788c5';
$client = new Client($sid, $token);

// Use the client to do fun stuff like send text messages!
$client->messages->create(
    // the number you'd like to send the message to
    $number,
    [
        // A Twilio phone number you purchased at twilio.com/console
        'from' => '+14702767343',
        // the body of the text message you'd like to send
        'body' => $textbody
    ]
);
?>
<script type="text/javascript">
setTimeout(
function ( )
{
  self.close();
}, 3000 );
</script>
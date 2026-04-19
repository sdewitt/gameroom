<?php
// Required if your environment does not handle autoloading
include 'main.php';

$pquery = "SELECT phone FROM accounts where id = " . $_GET['user'];
$phone = $pdo->query($pquery)->fetchColumn();
$pquery = "SELECT lastname FROM accounts where id = " . $_GET['user'];
$lastname = $pdo->query($pquery)->fetchColumn();
$pquery = "SELECT firstname FROM accounts where id = " . $_GET['user'];
$firstname = $pdo->query($pquery)->fetchColumn();
?>
<link href="buttons.css" rel="stylesheet" type="text/css">
<center>
<font size=+3><b>Send Message to: <?=$firstname?> <?=$lastname?> <?=formatPhoneNumber($phone)?></font></b><hr>
<div id="button-container">
<a href='message2.php?user=<?=$_GET['user']?>&message=1'>
<button class="mui-btn mui-btn--primary full-width">We are having some issues with your machine can you return to check in desk</button></a>
</div>
<div id="button-container">
<a href='message2.php?user=<?=$_GET['user']?>&message=2'>
<button class="mui-btn mui-btn--primary full-width">We cannot locate the key to your machine, please see desk</button></a>
</div>
<div id="button-container">
<a href='message2.php?user=<?=$_GET['user']?>&message=3'>
<button class="mui-btn mui-btn--primary full-width">Just a TEST MESSAGE</button></a>
</div>
</center>

<?php
function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.'-'.$lastFour;
    }

    return $phoneNumber;
}
?>
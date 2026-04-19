<?php

$url = "https://api.geekdo.com/xmlapi2/thing?id=208766";
$obj = json_decode(file_get_contents($url), true);

$cleannotes = htmlentities(strip_tags(trim($_POST['notes'])));


$name=$obj['boardgame.name'];
echo $name 




?>

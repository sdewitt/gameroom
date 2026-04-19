<?php
include 'main.php';
    session_start();
    echo "<h3> PHP List All Session Variables</h3>";
    foreach ($_SESSION as $key=>$val)
    echo $key." ".$val."<br/>";
   $guid = com_create_guid();
   echo $guid;
?>
<?php
$mysqli = new mysqli('127.0.0.1','azure','6#vWHD_$','localdb');
if($mysqli->connect_errno){
    echo $mysqli->connect_errno.": ".$mysqli->connect_error;
}
?>

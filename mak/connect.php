<?php
$link = mysqli_connect('localhost','root','','localdb');
mysqli_set_charset($link, "utf8");
if($link->connect_errno){
	echo $link->connect_errno.": ".$link->connect_errno;
}
?>
<?php
echo "test";
$login = '$NIAwork';
$password = 'APmT0NxhDw4Xti8Fjx0876qdSixM2jhrZyy9FYvRMvRKx7fk4BwFDE9kpRtQ';
$url = 'https://niawork.scm.azurewebsites.net/api/triggeredwebjobs/test/run';
$process = curl_init();
curl_setopt($process, CURLOPT_URL,$url);
curl_setopt($process, CURLOPT_HEADER, 1);
curl_setopt($process, CURLOPT_USERPWD, $login . ":" . $password);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Length:0'));
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_POST, TRUE);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$return = curl_exec($process);
curl_close($process);
echo($return);
?>
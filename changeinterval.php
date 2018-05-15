<?php
session_start();
require_once('connect.php');
date_default_timezone_set('Asia/Bangkok');
set_time_limit(0);

function change_time_interval($hour){

    //Change Time_interval in pantip_config file
    $str = file_get_contents($rootdir . "/wwwroot/file/pantip_config.json");
    echo "Read file : " . $rootdir . "/wwwroot/file/pantip_config.json";
    $json = json_decode($str, true);
    $json['time_interval'] = $hour;
    $newJson = json_encode($json);
    file_put_contents($rootdir . "/wwwroot/file/pantip_config.json", $newJson);


    echo "Trigger the update_script";
    $login = '$NIAwork';
    $password = 'APmT0NxhDw4Xti8Fjx0876qdSixM2jhrZyy9FYvRMvRKx7fk4BwFDE9kpRtQ';
    $url = 'https://niawork.scm.azurewebsites.net/api/triggeredwebjobs/updateScript/run';
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

}

mysqli_close($link);
?>

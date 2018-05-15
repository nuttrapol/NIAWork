<?php
session_start();
$connectstr_dbhost = '';
$connectstr_dbname = '';
$connectstr_dbusername = '';
$connectstr_dbpassword = '';
$rootdir = $_SERVER['DOCUMENT_ROOT'];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
        continue;
    }
    
    $connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
    $connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
    $connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
    $connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
}

//$link = mysqli_connect($connectstr_dbhost, $connectstr_dbusername, $connectstr_dbpassword,$connectstr_dbname);
$link = mysqli_connect("127.0.0.1:53388","azure", "6#vWHD_$", "localdb");

echo $connectstr_dbhost . "<br>";
echo $connectstr_dbusername . "<br>";
echo $connectstr_dbpassword . "<br>";
echo $connectstr_dbname . "<br>";

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
date_default_timezone_set('Asia/Bangkok');

/*echo "connectdbhost: " . $connectstr_dbhost. "\n";
echo "connectdbname: " . $connectstr_dbname. "\n";
echo "connectdbusername: " . $connectstr_dbusername. "\n";
echo "connectdbpw: " . $connectstr_dbpassword. "\n";

$q = "CREATE TABLE Products (`Id` INT NOT NULL AUTO_INCREMENT ,`ProductName` VARCHAR(200) NOT NULL ,`Color` VARCHAR(50) NOT NULL ,
`Price` DOUBLE NOT NULL ,PRIMARY KEY (`Id`));";
$result=$link->query($q);                    
if(!$result){
    echo "Select failed. Error: ".$link->error ;
}else{
    echo "OK " ;
}
mysqli_close($link);*/
?>
<html>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<body>

    <?php
    echo "token = 5c124659ff8dc666396a0088c7751d2b1f29deae<br>";
    echo "tid = 31600762";
    //include "changeinterval.php";
    if ((isset($_POST['tid'])) & (isset($_POST['tokenid'])) & (isset($_POST['submit']))) {
        $searchtid = $_POST['tid'];
        $searchtoken = $_POST['tokenid'];
        $submit = $_POST['submit'];
    //echo $submit; 
        $tidstatus = true;
    //echo "System Report: TID found!";
    } else {
        $searchtid = null;
        $searchtoken = null;
        $tidstatus = false;
    //echo "System Report: TID missed!";
    }
    ?>
    <br>
    <h1>Pantip Retrieval System</h1>
    <hr>

    <form action="index1.php" method="post">
        <h4>Search Filter (beta)</h4>

        Enter TID from Pantip:<br>
        <input type="text" name="tid" placeholder="Enter TID" value="<?php if ($searchtid != null) {
            echo $searchtid;
        } ?>"><br>
        Enter Your Token:<br>
        <input type="text" name="tokenid" placeholder="Enter Token" value="<?php if ($searchtoken != null) {
            echo $searchtoken;
        } ?>"><br>
        <br>
        <input type="submit" name="submit" value="Search">
        <input type="submit" name="submit" value="Download JSON">
    </form>
    <hr>

    <h4>Result:</h4>

    <?php
    //change_time_interval(1);
    if ($tidstatus == true) {
        $url = "https://service.pantip.com/api/get_full_topic_by_id?tid=" . $searchtid . "&access_token=" . $searchtoken;
        echo "Query: " . $url . "<br><br>";
        $response = file_get_contents($url);

        $row = json_decode($response, true);

        /*if ($submit == "Search") {
            echo $response;
        }*/        

        //Combine tags array into one string
        $tags = "";
        foreach ($row['tags'] as $t){
            $tags = $tags . "," . $t;
        }

            /*foreach ($row['emotion'] as $e){
                echo $e;
            }*/
            if(!isset($row["emotion"]["source"])){
                $likes = 0;
                $laugh = 0;
                $love = 0;
                $impress = 0;
                $scary = 0;
                $wow = 0;
            }else{
                $likes = $row["emotion"]["source"]["like"];
                $laugh = $row["emotion"]["source"]["laugh"];
                $love = $row["emotion"]["source"]["love"];
                $impress = $row["emotion"]["source"]["impress"];
                $scary = $row["emotion"]["source"]["scary"];
                $wow = $row["emotion"]["source"]["wow"];
            }

            if(!isset($row["admin_message_close"])){
                $adminmessage = "";
            }else{
                $adminmessage = $row["admin_message_close"];
            }

            //Insert User table
            $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $row["uid"] . "', '" . mysqli_real_escape_string($link,$row["nickname"]) . "', '" . $row["avatar"] . "')";
            if(!$link->query($sqlu)){
                echo("Error description: " . mysqli_error($link)) . "<br>";
            }
            echo $sqlu . "<br>";
            
            //Convert Unix TimeStamps
            $ct = Date('Y-m-d H:i:s',$row['created_time']);
            $ut = Date('Y-m-d H:i:s',$row['updated_time']);

            //Insert Topic table
            $sqlt = "INSERT INTO topic (tid,uid,type,status,title,description,created_time,updated_time,tag,club,permalink,points,admin_message,admin_message_close,liked,laugh,love,impress,scary,wow) VALUES ('" . $row["tid"] . "', '" . $row["uid"] . "', '" . $row["type"] . "', '" . $row["status"] . "', '" . mysqli_real_escape_string($link,$row["title"]) . "', '" . mysqli_real_escape_string($link,$row["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . mysqli_real_escape_string($link,$tags) . "', '" . $row["club"] . "', '" . $row["permalink"] . "', '" . $row["point"] . "', '" . mysqli_real_escape_string($link,$row["admin_message"]) . "', '" . mysqli_real_escape_string($link,$adminmessage) . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
                //echo $sqlt;
            if(!$link->query($sqlt)){
                echo("Error description: " . mysqli_error($link)) . "<br>";
            }

            //Insert Comment
            foreach ($row['comments'] as $c){
                //Insert User table
                $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $c["uid"] . "', '" . mysqli_real_escape_string($link,$c["nickname"]) . "', '" . $c["avatar"] . "')";
                if(!$link->query($sqlu)){
                    echo("Error description: " . mysqli_error($link)) . "<br>";
                }
                echo $sqlu . "<br>";

                if(!isset($c["emotion"]["source"])){
                    $likes = 0;
                    $laugh = 0;
                    $love = 0;
                    $impress = 0;
                    $scary = 0;
                    $wow = 0;
                }else{
                    $likes = $c["emotion"]["source"]["like"];
                    $laugh = $c["emotion"]["source"]["laugh"];
                    $love = $c["emotion"]["source"]["love"];
                    $impress = $c["emotion"]["source"]["impress"];
                    $scary = $c["emotion"]["source"]["scary"];
                    $wow = $c["emotion"]["source"]["wow"];
                }

                //Convert Unix TimeStamps
                $ct = Date('Y-m-d H:i:s',$c['created_time']);
                $ut = Date('Y-m-d H:i:s',$c['updated_time']);

                //Insert Comment table
                $sqlt = "INSERT INTO comment (cid,uid,tid,status,title,description,created_time,updated_time,permalink,points,haschild,comment_no,liked,laugh,love,impress,scary,wow) VALUES ('" . $c["cid"] . "', '" . $c["uid"] . "', '" . $row['tid'] . "', '" . $c["status"] . "', '" . $c["title"] . "', '" . mysqli_real_escape_string($link,$c["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . $c["permalink"] . "', '" . $c["point"] . "', '" . $c["has_child"] . "', '" . $c["comment_no"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
                if(!$link->query($sqlt)){
                    echo("Error description: " . mysqli_error($link)) . "<br>";
                }
                echo $sqlt . "<br>";

                //Get reply for each comment
                $urlr = "https://service.pantip.com/api/get_reply_by_comment_id?tid=" . $searchtid . "&access_token=" . $searchtoken . "&cid=" . $c["comment_no"];
                echo "Query: " . $urlr . "<br><br>";
                $response2 = file_get_contents($urlr);
                $reply = json_decode($response2, true);

                if(!isset($reply['error'])){
                    $rr = $reply['reply'];
                    foreach ($rr as $r) {
                        //echo $r . "<br>";

                    //Insert User table
                        $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $r["uid"] . "', '" . mysqli_real_escape_string($link,$r["nickname"]) . "', '" . $r["avatar"] . "')";
                        if(!$link->query($sqlu)){
                            echo("Error description: " . mysqli_error($link)) . "<br>";
                        }

                        if(!isset($c["emotion"]["source"])){
                            $likes = 0;
                            $laugh = 0;
                            $love = 0;
                            $impress = 0;
                            $scary = 0;
                            $wow = 0;
                        }else{
                            $likes = $c["emotion"]["source"]["like"];
                            $laugh = $c["emotion"]["source"]["laugh"];
                            $love = $c["emotion"]["source"]["love"];
                            $impress = $c["emotion"]["source"]["impress"];
                            $scary = $c["emotion"]["source"]["scary"];
                            $wow = $c["emotion"]["source"]["wow"];
                        }

                    //Convert Unix TimeStamps
                        $ct = Date('Y-m-d H:i:s',$r['created_time']);
                        $ut = Date('Y-m-d H:i:s',$r['updated_time']);

                            //Insert Reply table
                        $sqlr = "INSERT INTO reply (rid,uid,cid,status,description,created_time,updated_time,permalink,points,reply_no,liked,laugh,love,impress,scary,wow) VALUES ('" . $r["rid"] . "', '" . $r["uid"] . "', '" . $c['cid'] . "', '" . $r["status"] . "', '" . mysqli_real_escape_string($link,$r["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . $r["permalink"] . "', '" . $r["point"] . "', '" . $r["reply_no"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
                        if(!$link->query($sqlr)){
                            echo("Error description: " . mysqli_error($link)) . "<br>";
                        }
                        echo $sqlr . "<br>";
                    }
                }                              
            }

            if ($submit == "Download JSON") {
                $json = $response;
                echo "true";
                header('Content-disposition: attachment; filename=jsonFile.json');
                header('Content-type: application/json');
                echo $json;
                echo $response;
            }
        } else {
            echo "Please insert TID and TokenID above correctly.";
        }
        mysqli_close($link);
        ?>

    </body>
    </html>

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

$link = mysqli_connect($connectstr_dbhost, $connectstr_dbusername, $connectstr_dbpassword,$connectstr_dbname);

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
<body>

    <?php
    echo "token = 5c124659ff8dc666396a0088c7751d2b1f29deae";
    echo "tid = 31600762";
    include "changeinterval.php";
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

    <form action="index.php" method="post">
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
    $str = file_get_contents($rootdir . "/file/pantip_config.json");
    echo "Read file : " . $rootdir . "/file/pantip_config.json";
    $json = json_decode($str, true);
    echo $json['time_interval'];
    change_time_interval(1);
    if ($tidstatus == true) {
        $url = "https://service.pantip.com/api/get_full_topic_by_id?tid=" . $searchtid . "&access_token=" . $searchtoken;
        echo "Query: " . $url . "<br><br>";
        $response = file_get_contents($url);

        $data = json_decode($response, true);

        $array_data = $data['Topic'];

        if ($submit == "Search") {
            echo $response;
        }

        foreach ($array_data as $row) {

            //Combine Tag array into one string
            $tags = "";
            foreach ($row['tag'] as $t){
                $tags = $tags . "," . $t;
            }

            /*foreach ($row['emotion'] as $e){
                foreach ($row[])
            }*/

            //Insert User table
            $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $row["uid"] . "', '" . $row["nickname"] . "', '" . $row["avatar"] . "')";
            $link->query($sqlu);

            //Insert Emotion table of Topic
            $sqle = "INSERT INTO emotion (e_of,liked,laugh,love,impress,scary,wow) VALUES ('" . $row["tid"] . "', '" . $row["emotion"]["source"]["like"] . "', '" . $row["emotion"]["source"]["laugh"] . "', '" . $row["emotion"]["source"]["love"] . "', '" . $row["emotion"]["source"]["impress"] . "', '" . $row["emotion"]["source"]["scary"] . "', '" . $row["emotion"]["source"]["wow"] . "')";
            $link->query($sqle);

            //Get emotion ID
            $sqle = "SELECT MAX(eid) FROM emotion";
            $eid = $link->query($sqle);

            //Convert Unix TimeStamps
            $ct = new DateTime($row['created_time']);
            $ct->setTimeZone(new DateTimeZone('Asia/Bangkok'));
            $ct->format('YYYY-MM-DD HH:MM:SS');
            $ut = new DateTime($row['updated_time']);
            $ut->setTimeZone(new DateTimeZone('Asia/Bangkok'));
            $ut->format('YYYY-MM-DD HH:MM:SS');

            //Insert Topic table
            $sqlt = "INSERT INTO topic (tid,uid,eid, type,status,title,description,created_time,updated_time,tag,club,permalink,points,admin_message,admin_message_close) VALUES ('" . $row["tid"] . "', '" . $row["uid"] . "', '" . $eid . "', '" . $row["type"] . "', '" . $row["status"] . "', '" . $row["title"] . "', '" . $row["description"] . "', '" . $ct . "', '" . $ut . "', '" . $tags . "', '" . $row["club"] . "', '" . $row["permalink"] . "', '" . $row["point"] . "', '" . $row["admin_message"] . "', '" . $row["admin_message_close"] . "')";
            $link->query($sqlt);

            //Insert Comment
            foreach ($row['comment'] as $c){
                //Insert User table
                $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $c["uid"] . "', '" . $c["nickname"] . "', '" . $c["avatar"] . "')";
                $link->query($sqlu);

                //Insert Emotion table of Topic
                $sqle = "INSERT INTO emotion (e_of,liked,laugh,love,impress,scary,wow) VALUES ('" . $c["tid"] . "', '" . $c["emotion"]["source"]["like"] . "', '" . $c["emotion"]["source"]["laugh"] . "', '" . $c["emotion"]["source"]["love"] . "', '" . $c["emotion"]["source"]["impress"] . "', '" . $c["emotion"]["source"]["scary"] . "', '" . $c["emotion"]["source"]["wow"] . "')";
                $link->query($sqle);

                //Get emotion ID
                $sqle = "SELECT MAX(eid) FROM emotion";
                $eid = $link->query($sqle);

                //Convert Unix TimeStamps
                $ct = new DateTime($c['created_time']);
                $ct->setTimeZone(new DateTimeZone('Asia/Bangkok'));
                $ct->format('YYYY-MM-DD HH:MM:SS');
                $ut = new DateTime($c['updated_time']);
                $ut->setTimeZone(new DateTimeZone('Asia/Bangkok'));
                $ut->format('YYYY-MM-DD HH:MM:SS');

                //Insert Comment table
                $sqlt = "INSERT INTO topic (cid,uid,tid,eid,status,title,description,created_time,updated_time,permalink,points,haschild,comment_no) VALUES ('" . $c["cid"] . "', '" . $c["uid"] . "', '" . $row['tid'] . "', '" . $eid . "', '" . $c["status"] . "', '" . $c["title"] . "', '" . $c["description"] . "', '" . $ct . "', '" . $ut . "', '" . $c["permalink"] . "', '" . $c["point"] . "', '" . $c["haschild"] . "', '" . $c["comment_no"] . "')";
                $link->query($sqlt);

                //Get reply for each comment
                $urlr = "https://service.pantip.com/api/get_reply_by_comment_id?tid=" . $searchtid . "&access_token=" . $searchtoken . "&cid=" . $c["cid"];
                echo "Query: " . $url . "<br><br>";
                $response2 = file_get_contents($urlr);
                $data2 = json_decode($response2, true);
                $array_reply = $data2['reply'];
                foreach($array_reply as $r){
                    //Insert User table
                    $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $r["uid"] . "', '" . $r["nickname"] . "', '" . $r["avatar"] . "')";
                    $link->query($sqlu);

                    //Insert Emotion table of Reply
                    $sqle = "INSERT INTO emotion (e_of,liked,laugh,love,impress,scary,wow) VALUES ('" . $r["tid"] . "', '" . $r["emotion"]["source"]["like"] . "', '" . $r["emotion"]["source"]["laugh"] . "', '" . $r["emotion"]["source"]["love"] . "', '" . $r["emotion"]["source"]["impress"] . "', '" . $r["emotion"]["source"]["scary"] . "', '" . $r["emotion"]["source"]["wow"] . "')";
                    $link->query($sqle);

                    //Get emotion ID
                    $sqle = "SELECT MAX(eid) FROM emotion";
                    $eid = $link->query($sqle);

                    //Convert Unix TimeStamps
                    $ct = new DateTime($r['created_time']);
                    $ct->setTimeZone(new DateTimeZone('Asia/Bangkok'));
                    $ct->format('YYYY-MM-DD HH:MM:SS');
                    $ut = new DateTime($r['updated_time']);
                    $ut->setTimeZone(new DateTimeZone('Asia/Bangkok'));
                    $ut->format('YYYY-MM-DD HH:MM:SS');

                    //Insert Comment table
                    $sqlr = "INSERT INTO reply (rid,uid,,cid,eid,status,title,description,created_time,updated_time,permalink,points,reply_no) VALUES ('" . $r["rid"] . "', '" . $r["uid"] . "', '" . $c['tid'] . "', '" . $eid . "', '" . $r["status"] . "', '" . $r["title"] . "', '" . $r["description"] . "', '" . $ct . "', '" . $ut . "', '" . $r["permalink"] . "', '" . $r["point"] . "', '" . $r["haschild"] . "', '" . $r["comment_no"] . "')";
                    $link->query($sqlr);
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
    ?>

</body>
</html>

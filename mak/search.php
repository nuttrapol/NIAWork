<?php
session_start();
require_once('connect.php');
date_default_timezone_set('Asia/Bangkok');
set_time_limit(0);
/*if(!isset($_SESSION["tidstart"])and!isset($_SESSION["tcount"])){
    $_SESSION["tidstart"] = "";
    $_SESSION["tcount"] = 0;
}*/
?>
<html>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<body>

    <?php
    echo "token = 5c124659ff8dc666396a0088c7751d2b1f29deae<br>";
    echo "tid = 31600762";
    if ((isset($_POST['tokenid'])) & (isset($_POST['submit'])) & (isset($_POST['keyword']))) {
        $roomName = $_POST['roomName'];
        $searchtoken = $_POST['tokenid'];
        $sSearch = $_POST['sSearch'];
        $eSearch = $_POST['eSearch'];
        $keyword = $_POST['keyword'];
        $submit = $_POST['submit'];        
    //echo $submit; 
        $tidstatus = true;
    //echo "System Report: TID found!";
    } else {
        $roomName = null;
        $searchtoken = null;
        $sSearch = null;
        $eSearch = null;
        $keyword = null;
        $submit = null; 
        $tidstatus = false;
    //echo "System Report: TID missed!";
    }
    ?>
    <br>
    <h1>Pantip Retrieval System</h1>
    <hr>

    <form action="search.php" method="post">
        <h4>Search Filter (beta)</h4>

        Enter keyword:<br>
        <input type="text" name="keyword" placeholder="Enter Keyword" value="<?php if ($keyword != null) {
            echo $keyword;
        } ?>"><br>
        Enter period starting:<br>
        <input type="text" name="sSearch" placeholder="Enter time" value="<?php if ($sSearch != null) {
            echo $sSearch;
        } ?>"><br>
        Enter period ending:<br>
        <input type="text" name="eSearch" placeholder="Enter time" value="<?php if ($eSearch != null) {
            echo $eSearch;
        } ?>"><br>
        Enter room Name:<br>
        <input type="text" name="roomName" placeholder="Enter Room" value="<?php if ($roomName != null) {
            echo $roomName;
        } ?>"><br>
        Enter Your Token:<br>
        <input type="text" name="tokenid" placeholder="Enter Token" value="<?php if ($searchtoken != null) {
            echo $searchtoken;
        } ?>"><br>
        <br>
        <input type="submit" name="submit" value="Import">
        <input type="submit" name="submit" value="Download JSON">
    </form>
    <hr>

    <h4>Result:</h4>

    <?php
    if ($tidstatus == true) {

        $sqlst = "SELECT tid,title,description,created_time FROM topic WHERE ((title like '%" . $keyword . "%') or (description like '%" . $keyword . "%') or (tag like '%" . $keyword . "%')) and (created_time>='" . $sSearch . " 00:00:00' and created_time<='" . $eSearch . " 23:59:59')";                
        $result = $link->query($sqlst);
        echo "Query: " . $sqlst . "<br>";
        echo "Number of rows: " . $result->num_rows . "<br><br>";
       while ($array = $result->fetch_assoc()) {
        echo "Topic :" . $array['tid'] . "<br>Create At : " . $array['created_time'] ."<br>Title : " . $array['title'] . "<br>Desc : " . $array['description'] . "<br><br>";
        }



        // while($endloop == false){
        //     echo "Topic Start Add = " . $tidstarting . "<br>";            
        //     if($tidstarting == null){
        //         $stidStart = "";
        //     }else{
        //         $stidStart = "&tid_start=" . $tidstarting;
        //     }
        //     $url = "https://service.pantip.com/api/list_topic_by_room?room=" . $roomName . $stidStart . "&access_token=" . $searchtoken;
        //     echo "Query: " . $url . "<br><br>";
        //     $response = file_get_contents($url);

        //     $gettid1 = json_decode($response, true);
        //     $gettid2 = $gettid1['topics'];
        //     if (!isset($gettid1['topics'][0])) {
        //         $endloop = true;
        //         break;
        //     }
        //     foreach ($gettid2 as $gettid) {
        //         /*if(!isset($gettid['tid'])){
        //             $endloop = true;
        //             break;
        //         }*/
        //         echo "Topic in " . $roomName . " = " . $tcount . " for " . $gettid['tid'] . "<br>";
        //         $url = "https://service.pantip.com/api/get_full_topic_by_id?tid=" . $gettid['tid'] . "&access_token=" . $searchtoken;
        //         //echo "Query: " . $url . "<br><br>";
        //         $response = file_get_contents($url);

        //         $row = json_decode($response, true);

        //         /*if ($submit == "Search") {
        //             echo $response;
        //         }*/

        //         //Combine tags array into one string
        //         $tags = "";
        //         foreach ($row['tags'] as $t){
        //             $tags = $tags . "," . $t;
        //         }

        //         if(!isset($row["emotion"]["source"])){
        //             $likes = 0;
        //             $laugh = 0;
        //             $love = 0;
        //             $impress = 0;
        //             $scary = 0;
        //             $wow = 0;
        //         }else{
        //             $likes = $row["emotion"]["source"]["like"];
        //             $laugh = $row["emotion"]["source"]["laugh"];
        //             $love = $row["emotion"]["source"]["love"];
        //             $impress = $row["emotion"]["source"]["impress"];
        //             $scary = $row["emotion"]["source"]["scary"];
        //             $wow = $row["emotion"]["source"]["wow"];
        //         }

        //         if(!isset($row["admin_message_close"])){
        //             $adminmessage = "";
        //         }else{
        //             $adminmessage = $row["admin_message_close"];
        //         }

        //         //Insert User table
        //         $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $row["uid"] . "', '" . mysqli_real_escape_string($link,$row["nickname"]) . "', '" . $row["avatar"] . "')";                
        //         if(!$link->query($sqlu)){
        //             echo("Error description: " . mysqli_error($link)) . "<br>";
        //         }                

        //         //Insert Emotion table of Topic
        //         $sqle = "INSERT INTO emotion (e_of,liked,laugh,love,impress,scary,wow) VALUES ('" . $row["tid"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
        //         if(!$link->query($sqle)){
        //             echo("Error description: " . mysqli_error($link)) . "<br>";
        //         }

        //         /*//Get emotion ID
        //         $sqle = "SELECT MAX(eid) as eid FROM emotion";
        //         $result = $link->query($sqle);  
        //         $array = $result->fetch_assoc();
        //         $eid = $array['eid']; */         

        //         //Convert Unix TimeStamps
        //         $ct = Date('Y-m-d H:i:s',$row['created_time']);
        //         $ut = Date('Y-m-d H:i:s',$row['updated_time']);

        //         //Insert Topic table
        //         $sqlt = "INSERT INTO topic (tid,uid,type,status,title,description,created_time,updated_time,tag,club,permalink,points,admin_message,admin_message_close) VALUES ('" . $row["tid"] . "', '" . $row["uid"] . "', '" . $row["type"] . "', '" . $row["status"] . "', '" . mysqli_real_escape_string($link,$row["title"]) . "', '" . mysqli_real_escape_string($link,$row["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . mysqli_real_escape_string($link,$tags) . "', '" . $row["club"] . "', '" . $row["permalink"] . "', '" . $row["point"] . "', '" . mysqli_real_escape_string($link,$row["admin_message"]) . "', '" . mysqli_real_escape_string($link,$adminmessage) . "')";
        //         //echo $sqlt;
        //         if(!$link->query($sqlt)){
        //             echo("Error description: " . mysqli_error($link)) . "<br>";
        //         }

        //         //Insert Comment
        //         foreach ($row['comments'] as $c){
        //             //Insert User table
        //             $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $c["uid"] . "', '" . mysqli_real_escape_string($link,$c["nickname"]) . "', '" . $c["avatar"] . "')";
        //             if(!$link->query($sqlu)){
        //                 echo("Error description: " . mysqli_error($link)) . "<br>";
        //             }

        //             if(!isset($c["emotion"]["source"])){
        //                 $likes = 0;
        //                 $laugh = 0;
        //                 $love = 0;
        //                 $impress = 0;
        //                 $scary = 0;
        //                 $wow = 0;
        //             }else{
        //                 $likes = $c["emotion"]["source"]["like"];
        //                 $laugh = $c["emotion"]["source"]["laugh"];
        //                 $love = $c["emotion"]["source"]["love"];
        //                 $impress = $c["emotion"]["source"]["impress"];
        //                 $scary = $c["emotion"]["source"]["scary"];
        //                 $wow = $c["emotion"]["source"]["wow"];
        //             }

        //             //Insert Emotion table of Comment
        //             $sqle = "INSERT INTO emotion (e_of,liked,laugh,love,impress,scary,wow) VALUES ('" . $c["cid"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
        //             if(!$link->query($sqle)){
        //                 echo("Error description: " . mysqli_error($link)) . "<br>";
        //             }

        //             /*//Get emotion ID
        //             $sqle = "SELECT MAX(eid) as eid FROM emotion";
        //             $result = $link->query($sqle);  
        //             $array = $result->fetch_assoc();
        //             $eid = $array['eid']; */

        //             //Convert Unix TimeStamps
        //             $ct = Date('Y-m-d H:i:s',$c['created_time']);
        //             $ut = Date('Y-m-d H:i:s',$c['updated_time']);

        //             //Insert Comment table
        //             $sqlt = "INSERT INTO comment (cid,uid,tid,status,title,description,created_time,updated_time,permalink,points,haschild,comment_no) VALUES ('" . $c["cid"] . "', '" . $c["uid"] . "', '" . $row['tid'] . "', '" . $c["status"] . "', '" . $c["title"] . "', '" . mysqli_real_escape_string($link,$c["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . $c["permalink"] . "', '" . $c["point"] . "', '" . $c["has_child"] . "', '" . $c["comment_no"] . "')";
        //             if(!$link->query($sqlt)){
        //                 echo("Error description: " . mysqli_error($link)) . "<br>";
        //             }
        //             //echo $sqlt . "<br>";

        //             //Get reply for each comment
        //             $urlr = "https://service.pantip.com/api/get_reply_by_comment_id?tid=" . $gettid['tid'] . "&access_token=" . $searchtoken . "&cid=" . $c["comment_no"];
        //             //echo "Query: " . $urlr . "<br><br>";
        //             $response2 = file_get_contents($urlr);
        //             $reply = json_decode($response2, true);

        //             if(!isset($reply['error'])){
        //                 $rr = $reply['reply'];
        //                 foreach ($rr as $r) {
        //                 //echo $r . "<br>";

        //                     //Insert User table
        //                     $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $r["uid"] . "', '" . mysqli_real_escape_string($link,$r["nickname"]) . "', '" . $r["avatar"] . "')";
        //                     if(!$link->query($sqlu)){
        //                         echo("Error description: " . mysqli_error($link)) . "<br>";
        //                     }

        //                     if(!isset($c["emotion"]["source"])){
        //                         $likes = 0;
        //                         $laugh = 0;
        //                         $love = 0;
        //                         $impress = 0;
        //                         $scary = 0;
        //                         $wow = 0;
        //                     }else{
        //                         $likes = $c["emotion"]["source"]["like"];
        //                         $laugh = $c["emotion"]["source"]["laugh"];
        //                         $love = $c["emotion"]["source"]["love"];
        //                         $impress = $c["emotion"]["source"]["impress"];
        //                         $scary = $c["emotion"]["source"]["scary"];
        //                         $wow = $c["emotion"]["source"]["wow"];
        //                     }

        //                     //Insert Emotion table of Reply
        //                     $sqle = "INSERT INTO emotion (e_of,liked,laugh,love,impress,scary,wow) VALUES ('" . $r["rid"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
        //                     if(!$link->query($sqle)){
        //                         echo("Error description: " . mysqli_error($link)) . "<br>";
        //                     }

        //                     /*//Get emotion ID
        //                     $sqle = "SELECT MAX(eid) as eid FROM emotion";
        //                     $result = $link->query($sqle);  
        //                     $array = $result->fetch_assoc();
        //                     $eid = $array['eid']; */

        //                     //Convert Unix TimeStamps
        //                     $ct = Date('Y-m-d H:i:s',$r['created_time']);
        //                     $ut = Date('Y-m-d H:i:s',$r['updated_time']);

        //                     //Insert Reply table
        //                     $sqlr = "INSERT INTO reply (rid,uid,cid,status,description,created_time,updated_time,permalink,points,reply_no) VALUES ('" . $r["rid"] . "', '" . $r["uid"] . "', '" . $c['cid'] . "', '" . $r["status"] . "', '" . mysqli_real_escape_string($link,$r["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . $r["permalink"] . "', '" . $r["point"] . "', '" . $r["reply_no"] . "')";
        //                     if(!$link->query($sqlr)){
        //                         echo("Error description: " . mysqli_error($link)) . "<br>";
        //                     }
        //                     //echo $sqlr . "<br>";
        //                 }
        //             }                                               
        //         }
        //         $tcount = $tcount+1;                
        //     }
        //     $tidstarting = $gettid['tid'];            
        // }
       //echo "Room " . $roomName . " Adding is finish.";
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

<?php
session_start();
require_once('connect.php');
date_default_timezone_set('Asia/Bangkok');
set_time_limit(0);
?>
<html>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<body>

    <?php
    echo "token = 5c124659ff8dc666396a0088c7751d2b1f29deae<br>";
    echo "tid = 31600762";
    if ((isset($_POST['tokenid'])) & (isset($_POST['submit']))) {
        $searchtoken = $_POST['tokenid'];
        $roomName = $_POST['roomName'];
        $submit = $_POST['submit'];
    //echo $submit; 
        $tidstatus = true;
    //echo "System Report: TID found!";
    } else {
        $searchtoken = null;
        $roomName = null;
        $tidstatus = false;
    //echo "System Report: TID missed!";
    }
    ?>
    <br>
    <h1>Pantip Retrieval System</h1>
    <hr>

    <form action="update_script.php" method="post">
        <h4>Search Filter (beta)</h4>
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
        $endloop = false;
        $tcount = 1;

        echo "Root directory " . $rootdir . "<br>";

        $str = file_get_contents($rootdir . "/NIAWork/file/pantip_config.json");
        echo "file " . $rootdir . "/NIAWork/file/pantip_config.json <br>";
        $json = json_decode($str, true);
        $ti = $json['time_interval'];

        $currenttime = time();
        $yearago = $currenttime - (365*24*60*60);

        //Delete old record
        $cot = Date('Y-m-d H:i:s',$yearago);    
        $sqldot = "DELETE FROM topic WHERE created_time <= '". $cot . "'";                
        if(!$link->query($sqldot)){
            //echo("dot Error description: " . mysqli_error($link)) . "<br>";
        }else{
            //echo ("dot deleted: " . mysqli_affected_rows($link) . "<br>");
        }
        //echo "dot " . $sqldot . "<br>";
        $sqldoc = "DELETE FROM comment WHERE created_time <= '". $cot . "'";                
        if(!$link->query($sqldoc)){
            //echo("doc Error description: " . mysqli_error($link)) . "<br>";
        }else{
            //echo ("doc deleted: " . mysqli_affected_rows($link) . "<br>");
        }
        //echo "doc " . $sqldoc . "<br>";
        $sqldor = "DELETE FROM reply WHERE created_time <= '". $cot . "'";                
        if(!$link->query($sqldor)){
            //echo("dor Error description: " . mysqli_error($link)) . "<br>";
        }else{
            //echo ("dor deleted: " . mysqli_affected_rows($link) . "<br>");
        }
        //echo "dor " . $sqldor . "<br>";

        //Timer Start
        $time_pre = microtime(true);        

        for ($i=0; $i < count($json['room']); $i++) {
            $pretime = $currenttime;
            while ($pretime != strtotime($json['room'][$i]['last_updated'])) {
                $lastsix = $pretime - ($ti*60*60);
                //echo "Util : " . Date('Y-m-d H:i:s',$pretime) . "<br>";
                //echo "Since : " . Date('Y-m-d H:i:s',$lastsix) . "<br>";                
                if($lastsix < strtotime($json['room'][$i]['last_updated'])){
                    $lastsix = strtotime($json['room'][$i]['last_updated']);
                }
                
                $url = "https://service.pantip.com/api/list_topic_by_updated_time?room=" . $json['room'][$i]['name'] . "&last_update_since=" . $lastsix . "&last_update_until=" . $pretime . "&access_token=" . $searchtoken;
                echo "Query: " . $url . "<br><br>";

                $response = file_get_contents($url);
                $gettid1 = json_decode($response, true);
                $gettid2 = $gettid1['topics'];
                if (!isset($gettid1['topics'][0]['tid'])) {
                    $endloop = true;
                }
                foreach ($gettid2 as $gettid) {
                    if($endloop == true){
                        echo "break";
                        break;
                    }
                    echo "Topic in ". $i . $roomName . " = " . $tcount . " for " . $gettid['tid'] . "<br>";

                    //Delete old record            
                    $sqldt = "DELETE FROM topic WHERE tid = ". $gettid['tid'];                
                    if(!$link->query($sqldt)){
                        //echo("dt Error description: " . mysqli_error($link)) . "<br>";
                    }
                    //echo "dt " . $sqldt . "<br>";

                    //Insert new record
                    $url = "https://service.pantip.com/api/get_full_topic_by_id?tid=" . $gettid['tid'] . "&access_token=" . $searchtoken;
                    echo "<br>Query: " . $url . "<br>";
                    $response = file_get_contents($url);

                    $row = json_decode($response, true);

                    //Combine tags array into one string
                    $tags = "";
                    foreach ($row['tags'] as $t){
                        $tags = $tags . "," . $t;
                    }

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
                    //echo("Error description: " . mysqli_error($link)) . "<br>";
                    }                

                //Convert Unix TimeStamps
                    $ct = Date('Y-m-d H:i:s',$row['created_time']);
                    $ut = Date('Y-m-d H:i:s',$row['updated_time']);

                //Insert Topic table
                    $sqlt = "INSERT INTO topic (tid,uid,type,room,status,title,description,created_time,updated_time,tag,club,permalink,points,admin_message,admin_message_close,liked,laugh,love,impress,scary,wow) VALUES ('" . $row["tid"] . "', '" . $row["uid"] . "', '" . $row["type"] . "', '" . $roomName . "', '" . $row["status"] . "', '" . mysqli_real_escape_string($link,$row["title"]) . "', '" . mysqli_real_escape_string($link,$row["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . mysqli_real_escape_string($link,$tags) . "', '" . $row["club"] . "', '" . $row["permalink"] . "', '" . $row["point"] . "', '" . mysqli_real_escape_string($link,$row["admin_message"]) . "', '" . mysqli_real_escape_string($link,$adminmessage) . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
                //echo $sqlt;
                    if(!$link->query($sqlt)){
                    //echo("Error description: " . mysqli_error($link)) . "<br>";
                    }

                //Insert Comment
                    foreach ($row['comments'] as $c){
                    //Convert Unix TimeStamps
                        $ct = Date('Y-m-d H:i:s',$c['created_time']);
                        $ut = Date('Y-m-d H:i:s',$c['updated_time']);

                        if(!($ut >= Date('Y-m-d H:i:s',$lastsix) and $ut <= Date('Y-m-d H:i:s',$currenttime))){
                        //echo "break comment <br>";
                        }else{
                            $sqldc = "DELETE FROM comment WHERE comment.cid = ". $c['cid'];               
                            if(!$link->query($sqldc)){
                                echo("dc Error description: " . mysqli_error($link)) . "<br>";
                            }
                        //echo "dc <br>";
                        //echo "dc " . $sqldc . "<br>";

                        //Insert User table
                            $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $c["uid"] . "', '" . mysqli_real_escape_string($link,$c["nickname"]) . "', '" . $c["avatar"] . "')";
                            if(!$link->query($sqlu)){
                        //echo("Error description: " . mysqli_error($link)) . "<br>";
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

                    //Insert Comment table
                            $sqlt = "INSERT INTO comment (cid,uid,tid,status,title,description,created_time,updated_time,permalink,points,haschild,comment_no,liked,laugh,love,impress,scary,wow) VALUES ('" . $c["cid"] . "', '" . $c["uid"] . "', '" . $row['tid'] . "', '" . $c["status"] . "', '" . $c["title"] . "', '" . mysqli_real_escape_string($link,$c["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . $c["permalink"] . "', '" . $c["point"] . "', '" . $c["has_child"] . "', '" . $c["comment_no"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
                            if(!$link->query($sqlt)){
                        //echo("Error description: " . mysqli_error($link)) . "<br>";
                            }
                        //echo $sqlt . "<br>";
                        }                

                    //Get reply for each comment
                        $urlr = "https://service.pantip.com/api/get_reply_by_comment_id?tid=" . $gettid['tid'] . "&access_token=" . $searchtoken . "&cid=" . $c["comment_no"];
                    //echo "Query: " . $urlr . "<br><br>";
                        $response2 = file_get_contents($urlr);
                        $reply = json_decode($response2, true);

                        if(!isset($reply['error'])){
                            $rr = $reply['reply'];
                            foreach ($rr as $r) {
                            //Convert Unix TimeStamps
                                $ct = Date('Y-m-d H:i:s',$r['created_time']);
                                $ut = Date('Y-m-d H:i:s',$r['updated_time']);

                                if(!($ut >= Date('Y-m-d H:i:s',$lastsix) and $ut <= Date('Y-m-d H:i:s',$currenttime))){
                                //echo "break reply <br>";
                                    continue;
                                }else{
                                    $sqldr = "DELETE FROM reply WHERE reply.rid = " . $r["rid"];               
                                    if(!$link->query($sqldr)){
                                    //echo("dr Error description: " . mysqli_error($link)) . "<br>";
                                    }
                                //echo "dr " . $sqldr . "<br>";
                                //echo "dr <br>";
                                //Insert User table
                                    $sqlu = "INSERT INTO user (uid, nickname,avatar) VALUES ('" . $r["uid"] . "', '" . mysqli_real_escape_string($link,$r["nickname"]) . "', '" . $r["avatar"] . "')";
                                    if(!$link->query($sqlu)){
                                //echo("Error description: " . mysqli_error($link)) . "<br>";
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

                            //Insert Reply table
                                    $sqlr = "INSERT INTO reply (rid,uid,cid,status,description,created_time,updated_time,permalink,points,reply_no,liked,laugh,love,impress,scary,wow) VALUES ('" . $r["rid"] . "', '" . $r["uid"] . "', '" . $c['cid'] . "', '" . $r["status"] . "', '" . mysqli_real_escape_string($link,$r["desc"]) . "', '" . $ct . "', '" . $ut . "', '" . $r["permalink"] . "', '" . $r["point"] . "', '" . $r["reply_no"] . "', '" . $likes . "', '" . $laugh . "', '" . $love . "', '" . $impress . "', '" . $scary . "', '" . $wow . "')";
                                    if(!$link->query($sqlr)){
                                //echo("Error description: " . mysqli_error($link)) . "<br>";
                                    }
                                //echo $sqlr . "<br>";
                                }

                            }
                        }                                            
                    }
                    $tcount = $tcount+1;                
                }
                $pretime = $lastsix;
            }
            //Edit last_updated time
            $currentupdate = time();
            $cu = Date('Y-m-d H:i:s',$currentupdate);
            $json['room'][$i]['last_updated'] = $cu;
            $newJson = json_encode($json);
            file_put_contents($rootdir . "/NIAWork/file/pantip_config.json", $newJson);            
        }

    //Timer Stop
        $time_post = microtime(true);
        $exec_time = $time_post - $time_pre;
        echo "Time usage =" . $exec_time . " microsec. <br>";
    } else {
        echo "Please insert TID and TokenID above correctly.";
    }
    mysqli_close($link);
    ?>

</body>
</html>
<html>
<header>
    <title>Pantip Retrieval System</title>
    <?php
    require_once('connect.php');
    date_default_timezone_set('Asia/Bangkok');
    set_time_limit(0);
    session_start();
    if (isset($_POST['tokenid'])) {
        $_SESSION['tokenid'] = $_POST['tokenid'];
    }
    if (!isset($_SESSION['tokenid'])) {
        header('Location: index.php');
        die();
    }

    if (!isset($_POST['q'])) {
        header('Location: index.php');
        die();
    } else {
        $query = $_POST['q'];
    }


    function checkTypeQ($string)
    {
        $string2 = " !@#temptag#@!" . $string;
        if (strpos($string2, '!@#temptag#@!search')) {
            searchQ($string2);
        } elseif (strpos($string2, '!@#temptag#@!addRoom')) {
            addRoomQ($string2);
        } else {
            echo "---> Mismatch type<br>";
        }
    }

    function searchQ($curQ)
    {
        global $link;
        $key = extractString($curQ, "{key=", ",");
        $key_array = explode("&", $key);
        $sdate = extractString($curQ, "sdate=", ",");
        $edate = extractString($curQ, "edate=", ",");
        $room = extractString($curQ, "room=", "}");

        echo "---> Query type: SEARCH<br>";
        echo "---> Keyword is: ";
        for ($k = 1; $k <= count($key_array); $k++) {
            echo $key_array[$k - 1];
            if (isset($key_array[$k])) {
                echo " , ";
            }
        }
        echo "<br>";
        echo "---> StartTime is: " . $sdate . "<br>";
        echo "---> Endtime is: " . $edate . "<br>";
        echo "---> Room is: " . $room . "<br>";

        $topicRet_array = [];
        $topicQ = "SELECT tid,title,description,created_time 
                  FROM topic 
                  
                  WHERE ((title like '%" . $key_array[0] . "%') 
                  or (description like '%" . $key_array[0] . "%') 
                  or (tag like '%" . $key_array[0] . "%')) 
                  
                  and (created_time>='" . $sdate . " 00:00:00' 
                  and created_time<='" . $edate . " 23:59:59')";
        $result = $link->query($topicQ);
        //echo "Database Topic Query: " . $topicQ . "<br>";
        echo "---> Number of topics: " . $result->num_rows . "<br><br>";
        echo "-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-<br><br>";
        while ($array = $result->fetch_assoc()) {
            echo "Topic :" . $array['tid'] . "<br>Create At : " . $array['created_time'] . "<br>Title : " . $array['title'] . "<br>";
            array_push($topicRet_array, $array['tid']);
            $commentQ =  "SELECT cid,title,description,created_time,comment_no 
                          FROM comment 
                  
                          WHERE tid ='" . $array['tid'] . "'
                        
                          order by comment_no asc";
            $resultComment = $link->query($commentQ);
            //echo "Database Comment Query: " . $commentQ . "<br>";
            echo "Number of comments: " . $resultComment->num_rows . "<br><br>";
            while ($array = $resultComment->fetch_assoc()) {
                echo "--------> CommentID :" . $array['cid'] . "<br>--------> Title : " . $array['title'] . "<br>";
                $replyQ =  "SELECT rid,status,description,created_time 
                          FROM reply
                  
                          WHERE cid ='" . $array['cid'] . "'
                          AND status = 'normal'
                        
                          order by created_time asc";
                $resultReply = $link->query($replyQ);
                //echo "--------> Database Reply Query: " . $replyQ . "<br>";
                echo "-------->Number of replies: " . $resultReply->num_rows . "<br>";
                while ($array = $resultReply->fetch_assoc()) {
                    echo "----------------> ReplyID :" . $array['rid'] . "<br>";
                }
                echo "<br><br>";
            }
            //echo "FFF: ".$topicRet_array[0];
            echo "<br>-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-<br><br>";
        }


    }

    function addRoomQ($curQ)
    {
        $room = extractString($curQ, "{name=", "}");
        echo "---> Query type: ADD ROOM<br>";
        echo "---> Room Name is: " . $room . "<br>";
        echo "<br><hr><br>";
    }

    ?>


</header>
<body>
<a href="clearSession.php">Clear Session</a>
<a href="search.php">Back to Search</a>

<hr>
<h1>Pantip Retrieval System</h1>
<hr>

<form action="result.php" method="post">
    <h4>Result</h4>

    Your queries:<br>
    <?php

    echo $query . "<br><hr><br>";
    //split lines
    $query_array = explode("\n", $query);


    function extractString($string, $start, $end)
    {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }


    for ($line = 1; $line <= count($query_array); $line++) {
        echo "Query number " . $line . " is: ";
        $curQ = $query_array[$line - 1];
        echo $curQ . "<br>";
        checkTypeQ($curQ);
    }


    ?>
</form>
<hr>

</body>
</html>
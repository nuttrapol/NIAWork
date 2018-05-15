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

    function extractString($string, $start, $end)
    {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
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
        $mydirectory = '/tempTextFiles/'. date("Y-m-d") .'/'. date("h:i:sa");
        //$mydirectory = '';
        if (!file_exists($mydirectory)) {
            mkdir($mydirectory, 0777, true);
        }


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

        function multiTitle($key_array)
        {
            //echo "test0".$key_array[0];
            $strTitle = " ";
            for ($k = 2; $k <= count($key_array); $k++) {
                //echo "test".$key_array[$k-1];
                $strTitle .= " OR (title like '%" . $key_array[$k - 1] . "%') ";
            }
            return $strTitle;
        }

        $mTitle = multiTitle($key_array);
        //echo "here is my mistake".$mTitle;

        function multiDesc($key_array)
        {
            //echo "test0".$key_array[0];
            $strDesc = " ";
            for ($k = 2; $k <= count($key_array); $k++) {
                //echo "test".$key_array[$k-1];
                $strDesc .= " OR (description like '%" . $key_array[$k - 1] . "%') ";
            }
            return $strDesc;
        }

        $mDesc = multiDesc($key_array);

        function multiTag($key_array)
        {
            //echo "test0".$key_array[0];
            $strTag = " ";
            for ($k = 2; $k <= count($key_array); $k++) {
                //echo "test".$key_array[$k-1];
                $strTag .= " OR (tag like '%" . $key_array[$k - 1] . "%') ";
            }
            return $strTag;
        }

        $mTag = multiTag($key_array);


        $topicQ = "SELECT tid,title,description,created_time 
                   FROM topic 
                  
                   WHERE ((title like '%" . $key_array[0] . "%')" . $mTitle . " 
                   or (description like '%" . $key_array[0] . "%') " . $mDesc . "
                   or (tag like '%" . $key_array[0] . "%')) " . $mTag . "
                  
                   and (created_time>='" . $sdate . " 00:00:00' 
                   and created_time<='" . $edate . " 23:59:59')
                   ORDER BY created_time ASC";

        $result = $link->query($topicQ);
        //echo "Database Topic Query: " . $topicQ . "<br>";
        echo "---> Number of topics: " . $result->num_rows . "<br><br>";
        echo "-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-<br><br>";
        while ($array = $result->fetch_assoc()) {
            echo "TopicID :" . $array['tid'] . "<br>Create At : " . $array['created_time'] . "<br>Title : " . $array['title'] . "<br>";
            array_push($topicRet_array, $array['tid']);

            //add topic data to text file
            //$qfile = fopen($mydirectory . "myText.txt", "w");
            $qfile = fopen($mydirectory . "tid".$array['tid'].".txt", "w");
            $txt = "<topic>\n<tid>" . $array['tid'] . "</tid>\n<title>" . $array['title'] . "</title>\n<createdTime>" . $array['created_time'] . "</createdTime>\n<description>" . $array['description'] . "</description>\n";
            fwrite($qfile, $txt);


            $commentQ = "SELECT cid,title,description,created_time,comment_no 
                          FROM comment 
                  
                          WHERE tid ='" . $array['tid'] . "'
                          AND status='normal'
                        
                          order by comment_no asc";
            $resultComment = $link->query($commentQ);
            //echo "Database Comment Query: " . $commentQ . "<br>";
            echo "Number of comments: " . $resultComment->num_rows . "<br><br>";
            while ($array = $resultComment->fetch_assoc()) {
                echo "--------> CommentID :" . $array['cid'] . "<br>--------> Title : " . $array['title'] . "<br>";

                //add comment data to text file
                $txt = "<comment>\n<cid>" . $array['cid'] . "</cid>\n<title>" . $array['title'] . "</title>\n<createdTime>" . $array['created_time'] . "</createdTime>\n<description>" . $array['description'] . "</description>\n";
                fwrite($qfile, $txt);


                $replyQ = "SELECT rid,status,description,created_time 
                          FROM reply
                  
                          WHERE cid ='" . $array['cid'] . "'
                          AND status = 'normal'
                        
                          order by created_time asc";
                $resultReply = $link->query($replyQ);
                //echo "--------> Database Reply Query: " . $replyQ . "<br>";
                echo "-------->Number of replies: " . $resultReply->num_rows . "<br>";
                while ($array = $resultReply->fetch_assoc()) {
                    echo "----------------> ReplyID :" . $array['rid'] . "<br>";

                    //add comment data to text file
                    $txt = "<reply>\n<rid>" . $array['rid'] . "</rid>\n<createdTime>" . $array['created_time'] . "</createdTime>\n<description>" . $array['description'] . "</description>\n</reply>\n";
                    fwrite($qfile, $txt);
                }
                $txt = "</comment>\n";
                fwrite($qfile, $txt);
                echo "<br><br>";
            }
            $txt = "</topic>\n";
            fwrite($qfile, $txt);
            fclose($qfile);
            echo "<a href=\"../html-link.htm\"><img src=\"images\doc_icon.png\" style=\"width:20px; height:20px\" title=\"Download Text File\" alt=\"Download\"></a>";
            echo "<a href='myText.txt'><button type=\"button\">download txt file here</button></a><br><br>";
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
<?php
echo getcwd();

//$content = "some text here";
//$fp = fopen( "myText.txt","w");
//fwrite($fp,$content);
//fclose($fp);
//
//
//$myfile = fopen("myText.txt", "w");
//$txt = "John Doe\n";
//fwrite($myfile, $txt);
//$txt = "Jane Doe\n";
//fwrite($myfile, $txt);
//fclose($myfile);
?>

<a href="clearSession.php">Clear Session</a>
<a href="search.php">Back to Search</a>

<hr>
<h1>Pantip Retrieval System</h1>
<hr>

<form action="result.php" method="post">
    <h4>Result</h4>

    Your queries:<br>
    <?php

    echo $query . "<br>";
    //split lines
    $query_array = explode("\n", $query);
    echo "<br>Total number of quries is " . count($query) . ".<br><br><hr><br>";


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
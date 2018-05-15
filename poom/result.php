<html>
<header>
    <title>Pantip Retrieval System</title>
    <?php
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
        $key = extractString($curQ, "{key=", ",");
        $key_array = explode("&", $key);
        $sdate = extractString($curQ, "sdate=", ",");
        $edate = extractString($curQ, "edate=", ",");
        $room = extractString($curQ, "room=", "}");

        echo "---> Query type: SEARCH<br>";
        echo "---> Keyword is: ";
        for ($k = 1; $k <= count($key_array); $k++) {
            echo $key_array[$k-1];
            if (isset($key_array[$k])) {
                echo " , ";
            }
        }
        echo "<br>";
        echo "---> StartTime is: " . $sdate . "<br>";
        echo "---> Endtime is: " . $edate . "<br>";
        echo "---> Room is: " . $room . "<br>";
        echo "<br>";
    }

    function addRoomQ($curQ)
    {
        $room = extractString($curQ, "{name=", "}");
        echo "---> Query type: ADD ROOM<br>";
        echo "---> Room Name is: " . $room . "<br>";
        echo "<br>";
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

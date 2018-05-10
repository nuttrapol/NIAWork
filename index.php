<?php
$connectstr_dbhost = '';
$connectstr_dbname = '';
$connectstr_dbusername = '';
$connectstr_dbpassword = '';

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

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
echo "connectdbhost: " . $connectstr_dbhost. "\n";
echo "connectdbname: " . $connectstr_dbname. "\n";
echo "connectdbusername: " . $connectstr_dbusername. "\n";
echo "connectdbpw: " . $connectstr_dbpassword. "\n";
/*echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
echo "Host information: " . mysqli_get_host_info($link) . PHP_EOL;*/


if (mysqli_query($links, '
CREATE TABLE Products (
`Id` INT NOT NULL AUTO_INCREMENT ,
`ProductName` VARCHAR(200) NOT NULL ,
`Color` VARCHAR(50) NOT NULL ,
`Price` DOUBLE NOT NULL ,
PRIMARY KEY (`Id`)
);
')) {
echo "Table created\n";
}else{
echo "Error\n";
}

mysqli_close($link);
?>
<html>
<body>

<?php
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
if ($tidstatus == true) {
    $url = "https://service.pantip.com/api/get_full_topic_by_id?tid=" . $searchtid . "&access_token=" . $searchtoken;
    echo "Query: " . $url . "<br><br>";
    $response = file_get_contents($url);

    if ($submit == "Search") {
        echo $response;
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

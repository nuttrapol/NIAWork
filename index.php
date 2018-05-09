<html>
<body>

<?php
if ((isset($_POST['tid']))&(isset($_POST['tokenid']))) {
    $searchtid = $_POST['tid'];
    $searchtoken = $_POST['tokenid'];
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
<h1>Pantip Retreival System</h1>
<hr>

<form action="index.php" method="post">
    <h4>Search Filter (beta)</h4>

    Enter TID from Pantip:<br>
    <input type="text" name="tid" placeholder="Enter TID" value="<?php if($searchtid!=null) { echo $searchtid; }?>"><br>
    Enter Your Token:<br>
    <input type="text" name="tokenid" placeholder="Enter Token" value="<?php if($searchtoken!=null) { echo $searchtoken; }?>"><br>
    <br>
    <input type="submit">
</form>
<hr>

<h4>Result:</h4>

<?php
if ($tidstatus == true) {
    $url = "https://service.pantip.com/api/get_full_topic_by_id?tid=" . $searchtid . "&access_token=".$searchtoken;
    echo "Query: ".$url."<br><br>";
    $response = file_get_contents($url);
    echo $response;

} else {
    echo "Please insert TID and TokenID above correctly.";
}
?>

</body>
</html>

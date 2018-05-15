<html>
<header>
    <title>Pantip Retrieval System</title>
    <?php
    session_start();
    if ($_SESSION['tokenid'] == true) {
        header('Location: search.php');
        die();
    }
    ?>

</header>
<body>
<a href="clearSession.php">Clear Session</a>
<hr>
<h1>Pantip Retrieval System</h1>
<hr>

<form action="search.php" method="post">
    <h4>Enter Your Token</h4>

    <input type="text" name="tokenid" placeholder="Enter Token"><br>
    <br>
    <input type="submit" name="submit">
</form>
</body>
</html>

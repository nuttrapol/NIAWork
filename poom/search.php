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
    ?>

</header>
<body>
<a href="clearSession.php">Clear Session</a>

<hr>
<h1>Pantip Retrieval System</h1>
<hr>

<form action="result.php" method="post">
    <h4>Search Filter</h4>

    Enter batch queries:<br>
    <textarea name="q" rows="30" cols="150" placeholder="Enter your queries"></textarea>
    <br>
    <input type="submit" name="submit" value="Search">
</form>
<hr>

</body>
</html>

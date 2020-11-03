<?php
session_start();

if (!isset($_SESSION['username']))
{
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
}

if (isset($_GET['logout']))
{
        session_destroy();
        unset($_SESSION['username']);
        header("location: login.php");
}

require_once('scripts.php');
?>

<html>

<!-- Nav options -->
<h1><?php echo $_SESSION['username']; ?></h1>
<p><a href="index.php">home</a></p>
<p><a href="profile.php">profile</a></p>
<p><a href="social.php">social</a><p>
<p><a href="index.php?logout='1'" style="color: red;">logout</a></p>

<!-- Add Button -->
<div id="addButton"> <button onclick="EditPost()">Add Post</button> </div>

<hr>

<!-- Body Content -->
<body onload="SendGetRequest('getcomm');">

<h2>Community</h2>
        <div id="comm"></div>

</body>
</html>

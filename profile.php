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

<!--
Profile page content
-->
<html>
<style>
input{width:100%;}
div{word-wrap: break-word;}
#listResponse{
	overflow-y:scroll; 
	height:500px;
}

table
{
	border: 1px solid black;
	width:100%;
}

#listResponse
{
	width:33%;
}
</style>



<body onload="SendGetRequest('getbio'); SendGetRequest('getcabinet'); SendGetRequest('getlikes');">
<h1><?php echo $_SESSION['username']; ?></h1>
<p><a href="index.php">home</a></p>
<p><a href="index.php?logout='1'" style="color: red;">logout</a></p>

<div id="editButton"> <button onclick="EditMode()">Edit Profile</button> </div>

<hr>

<h2>Bio</h2>
<div id="bio"></div>

<hr>

<h2>Favorites / Liked / Disliked(?)</h2>
<div id="favorites"></div>

<hr>

<h2>Cabinet</h2>
<div id="cabinet"></div>
<br>
<div id="listResponse"></div>

</body>
</html>

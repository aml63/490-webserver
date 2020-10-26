<?php 
session_start(); 

// Check if username is set, if not, go to login page.
if (!isset($_SESSION['username'])) 
{
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}

// Handles logout, sends back to login page
if (isset($_GET['logout'])) 
{
	session_destroy();
	unset($_SESSION['username']);
	header("location: login.php");
}

require_once('scripts.php');
?>

<html>

<!-- CSS -->

<style>
input{ width:100%; }
div{ word-wrap: break-word; }
#listResponse{ overflow-y:scroll; height:500px; width:33%}
table{ border: 1px solid black; width:100%; }
</style>

<!-- Profile page content -->

<!-- 
Bios are kind of pointless right now because nobody else can see them
TODO: Make it so other user's profiles can be viewed, obviosuly without edit options, just pull up the other user's info
-->

<!-- Nav options -->
<h1><?php echo $_SESSION['username']; ?></h1>
<p><a href="index.php">home</a></p>
<p><a href="index.php?logout='1'" style="color: red;">logout</a></p>

<!-- Edit Button - transform the page to allow editing. TODO: add option to remove liked drinks? -->
<div id="editButton"> <button onclick="EditMode()">Edit Profile</button> </div>

<hr>

<!-- Body content / User related info - Fetch our user's info from the database onload. TODO: Specify html element in second paramter? -->
<body onload="SendGetRequest('getbio'); SendGetRequest('getcabinet'); SendGetRequest('getlikes');">

<h2>Bio</h2>
	<div id="bio"></div>
<hr>
<h2>Favorites / Liked / Disliked(?)</h2>
	<div id="favorites"></div> <!-- TODO: Make it so favorites show up as drinks, not just ID numbers -->
<hr>
<h2>Cabinet</h2>
	<div id="cabinet"></div>
<br>
	<div id="listResponse"></div>

</body>
</html>

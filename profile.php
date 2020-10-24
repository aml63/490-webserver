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
?>

<!--
Profile page content
-->
<html>
<style>
input
{
width:100%;
}
</style>

<script>
function EditMode()
{
	// Get current html 
	// Ideally these will be first set/filled by fetching from the database when loading the page
	var currBio = document.getElementById("bio").innerHTML;
	var currCab = document.getElementById("cabinet").innerHTML;
	var currFav = document.getElementById("favorites").innerHTML;
	
	// Swap the button
	document.getElementById("editButton").innerHTML = "<button onclick='SaveEdit()'>Save Profile</button>";
	
	// Transform! Turn into an input box!
	document.getElementById("bio").innerHTML = "<input type='text' value='" + currBio + "' id='bioEdit'/>"
	document.getElementById("cabinet").innerHTML = "<input type='text' value='" + currCab + "' id='cabEdit'/>"
	document.getElementById("favorites").innerHTML = "<input type='text' value='" + currFav + "' id='favEdit'/>"
}

function SaveEdit()
{
	// Get values of input boxes so we can turn it back to content
	// This needs to get sent to the database and saved, obviously. This is just placeholder stuff.
	var newBio = document.getElementById("bioEdit").value;
	var newCab = document.getElementById("cabEdit").value;
	var newFav = document.getElementById("favEdit").value;
	
	// Swap the button.
	document.getElementById("editButton").innerHTML = "<button onclick='EditMode()'>Edit Profile</button>";
	
	// Transform! Back into content!
	document.getElementById("bio").innerHTML = newBio;
	document.getElementById("cabinet").innerHTML = newCab;
	document.getElementById("favorites").innerHTML = newFav;
}
</script>

<h1><?php echo $_SESSION['username']; ?></h1>
<p><a href="index.php">home</a></p>
<p><a href="index.php?logout='1'" style="color: red;">logout</a></p>

<div id="editButton"> <button onclick="EditMode()">Edit Profile</button> </div>

<hr>

<h2>Bio</h2>
<div id="bio">profile description \ other info here</div>

<hr>

<h2>Cabinet</h2>
<div id="cabinet">Add drinks to your cabinet!</div>

<hr>

<h2>Favorites</h2>
<div id="favorites">Keep track of favorites here</div>

</html>

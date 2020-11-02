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

<script>

</script>

<!-- Nav options -->
<?php  if (isset($_SESSION['username'])) : ?>
	<p>Logged in as: <strong><?php echo $_SESSION['username']; ?></strong></p>
	<p><a href="index.php">home</a></p>
	<p><a href="profile.php">your profile</a></p>
	<p><a href="index.php?logout='1'" style="color: red;">logout</a></p>
<?php endif ?>
<hr>
<body onload="SendGetRequest('getlikestats');">

<h3>Liked Drinks</h3>
<div id="drinkLikes"> </div>

</body>
</html>

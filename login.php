<?php
session_start();

if (isset($_SESSION['username'])) 
{
	$_SESSION['msg'] = "Already logged in.";
	header('location: index.php');
}

require_once('scripts.php');
?>

<html>

<?php  if (isset($_SESSION['msg'])) : ?>
    	<p style="color: red;"><strong><?php echo $_SESSION['msg']; ?></strong></p>
<?php endif ?>
<hr>
<!--
Login & Registration is here
Might be wise to incorporate some sort of static siderbar\navbar for this.
-->
<h3>Login</h3>
<div class="log-form">
	<input type="text" id="usr" placeholder="username" />
	<input type="password" id="psw" placeholder="password" />
	<button onclick="SendLoginRequest()">Login</button>
</div>
<p id="loginResponse">  </p>
<hr>
<div class ="reg-form">

<h3>Register</h3>
	<input type="text" id="regusr" placeholder="username" />
	<input type="password" id="regpsw" placeholder="password" />
	<button onclick="SendRegisterRequest()">Register</button>
</div>
<p id="regResponse">  </p>

</html>




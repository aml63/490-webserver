<html>

<?php
session_start();
?>

<script>
// LOGIN & REGISTRATION STUFF
function HandleLoginResponse(response)
{
	var text = JSON.parse(response);
	
	// Send them to a new page if they were logged in
	if (response == 1)
		document.location.href = "index.php"
	else
		document.getElementById("loginResponse").innerHTML = "Bad Credentials (response: "+text+")<p>";
}

function SendLoginRequest()
{
	console.log("Doing login request");
	username = document.getElementById("usr").value;
	password = document.getElementById("psw").value;
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			console.log("Handling login response");
			HandleLoginResponse(this.responseText);
		}		
	}
	request.send("type=login&uname="+username+"&pword="+password); // request we're sending thru rabbit?
}


function HandleRegisterResponse(response)
{
	var text = JSON.parse(response);
	
	// Give them a confirmation that their credentials were registered
	if (response == 1)
		document.getElementById("regResponse").innerHTML = "Registered! (code: "+text+")<p>";
	else if (response == 0)
		document.getElementById("regResponse").innerHTML = "Already Registered? (code: "+text+")<p>";
}

function SendRegisterRequest()
{
	username = document.getElementById("regusr").value;
	password = document.getElementById("regpsw").value;
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleRegisterResponse(this.responseText);
		}
	}
	request.send("type=register&uname="+username+"&pword="+password); // request we're sending thru rabbit?
}

</script>

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




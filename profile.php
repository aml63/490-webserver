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
input{width:100%;}
div{word-wrap: break-word;}
#listResponse{
	overflow-y:scroll; 
	height:500px;
}

table{
width:100%;
border:5px;
}

</style>

<script>
// Edit info on page
function EditMode()
{
	// Swap the button
	document.getElementById("editButton").innerHTML = "<button onclick='SaveEdit()'>Save Profile</button>";

	// Get current html 
	var currBio = document.getElementById("bio").innerHTML;
	var currCab = document.getElementById("cabinet").innerHTML;
	
	// Transform!
	document.getElementById("bio").innerHTML = "<textarea rows='5' cols='90' maxlength='280' id='bioEdit'/>" + currBio + "</textarea>"
	
	SendListRequest();
	document.getElementById("cabinet").innerHTML = "<div id='cabdisc'><strong>**Only add ingredients from list! Don't add spaces! Don't remove tailling comma!**</strong></p><textarea rows='5' cols='90' maxlength='280' id='cabEdit'/>" + currCab + "</textarea>"
	
}

// Save what was put in the boxes
function SaveEdit()
{
	// Get values of input boxes so we can turn it back to content
	// This needs to get sent to the database and saved, obviously. This is just placeholder stuff.
	var newBio = document.getElementById("bioEdit").value;
	var newCab = document.getElementById("cabEdit").value;
	
	// Swap the button.
	document.getElementById("editButton").innerHTML = "<button onclick='EditMode()'>Edit Profile</button>";
	
	SendSetRequest(newBio, "setbio"); // save bio to database
	SendSetRequest(newCab, "setcabinet");
	
	// Transform! Back into content!
	document.getElementById("bio").innerHTML = newBio;
	document.getElementById("cabinet").innerHTML = newCab;
	document.getElementById("listResponse").innerHTML = ""; // get rid of search results
}


// GET & SET
// Bios are kind of pointless right now because nobody else can see them
// Can fix this by setting a variable in the post / url when loading profile.php, and get the info from that username instead, but don't allow editing.
function HandleSetResponse(response)
{
	// if bio, if cabinet, but we're dealing with strings anyways so might not need to differentiate?
	var text = JSON.parse(response);
}
function SendSetRequest(info, type)
{
	// types: setbio->newbio or setcabinet -> newcabinet
	
	username = "<?php echo $_SESSION['username']; ?>";
	
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleSetResponse(this.responseText);
		}		
	}
	
	if (type == "setbio")
		request.send("type="+type+"&uname="+username+"&newbio="+info);
	else if (type == "setcabinet")
		request.send("type="+type+"&uname="+username+"&newcabinet="+info);
}
function HandleGetResponse(response, type)
{
	var text = JSON.parse(response);
	
	console.log(text);

	if (type == "getbio")
		if (text.bio != null)
			document.getElementById("bio").innerHTML = text.bio;
	if (type == "getcabinet")
		if (text.cabinet != null)
			document.getElementById("cabinet").innerHTML = text.cabinet;
}
function SendGetRequest(type)
{
	var username = "<?php echo $_SESSION['username']; ?>";
	
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleGetResponse(this.responseText, type);
		}		
	}
	request.send("type="+String(type)+"&uname="+username);
}


// Handle the response to format it into the page
function HandleListResponse(response) // Handle the data we got from the API
{
	var data = JSON.parse(response); 	// parse the response into json array: data
	console.log(data);			// print to console for testing
	var txt = "";				// The text variable we'll be manipulating to insert our data into the page
	
	txt += "<table id='SearchResults' border='1'>"
	for (drink in data.drinks)
	{	
		for (obj in data.drinks[drink]) 
		{	
			if (data.drinks[drink][String(obj)] != null) // Only include keys with values
			{
					// honey, my brain hurts. thank fuck backticks work, also
					txt += "<tr><td>"+drink+"</td><td>"+data.drinks[drink][String(obj)]+"</td><td><button onclick='addIngredient(`"+data.drinks[drink][String(obj)]+"`)'>add</button></td></tr>";
			}
		}
	}
	txt += "</table>"    
      	document.getElementById("listResponse").innerHTML = txt;
}
// Fetch ingredient list from API
function SendListRequest() // Send the request to the API
{
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/list.php?i=list";
	var request = new XMLHttpRequest();
	request.open("GET", url, true);
	request.onreadystatechange = function()
	{
		if(this.readyState==4 && this.status==200)
		{
			HandleListResponse(this.responseText)
		}
	}
	request.send();
}
// Add an ingredient to local page cabinet
function addIngredient(id)
{
	var cabinet = document.getElementById("cabEdit");
	
	if (cabinet.value.includes(id))
		alert("You already have one of those, buddy. I think you've got a problem.");
	else
		cabinet.value += id + ",";
}
</script>

<body onload="SendGetRequest('getbio'); SendGetRequest('getcabinet');">
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

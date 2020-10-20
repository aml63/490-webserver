<html>

<script>
// LOGIN STUFF
function HandleLoginResponse(response)
{
	var text = JSON.parse(response);
	//document.getElementById("textResponse").innerHTML = response+"<p>";	
	document.getElementById("textResponse").innerHTML = "response: "+text+"<p>";
}

function SendLoginRequest()
{
	username = document.getElementById("usr").value;
	password = document.getElementById("psw").value;
	var request = new XMLHttpRequest();
	request.open("POST","login.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange= function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleLoginResponse(this.responseText);
		}		
	}
	request.send("type=login&uname="+username+"&pword="+password); // request we're sending to rabbit?
}


// Handle the data we got from the API
function HandleSearchResponse(response)
{
	var data = JSON.parse(response);
	var txt = "";
	txt += "<table border='1'>"
	for (drink in data.drinks) // 012345
	{	
		txt += "<tr><td>Drink " + drink + "</td></tr>";
		for (obj in data.drinks[drink]) // data->drinks->drink->obj
		{	
			txt += "<tr><td>"+ obj +"</td></tr>";
		}
	}
	txt += "</table>"    
      	document.getElementById("searchResponse").innerHTML = txt;
}

// Get the data from the API
function DoSearchRequest()
{
	var request = new XMLHttpRequest();
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/search.php?s=margarita";
	request.open("GET", url);
	request.send();

	request.onreadystatechange = function()
	{
		if(this.readyState==4 && this.status==200)
		{
			HandleSearchResponse(this.responseText)
		}
	}
}
</script>


<body>

<input type="text" placeholder="Enter your search keywords" id="mysearch" />
<button onclick="DoSearchRequest()">Search</button>
<div id="searchResponse"></div>

<h1>register</h1>
it ain't done
<h1>login</h1>
<div class="log-form">
	<input type="text" id="usr" placeholder="username" />
	<input type="password" id="psw" placeholder="password" />
	<button onclick="SendLoginRequest()">Login</button>
</div>

<br>

<div id="textResponse"> awaiting response </div>

</body>
</html>

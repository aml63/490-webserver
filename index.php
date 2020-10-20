<html>

<script>
// LOGIN STUFF
function HandleLoginResponse(response)
{
	var text = JSON.parse(response);	
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
	request.send("type=login&uname="+username+"&pword="+password); // request we're sending thru rabbit?
}


// Handle the data we got from the API
function HandleSearchResponse(response)
{
	var data = JSON.parse(response); 	// parse the response into json array: data
	console.log(data);			// print to console for testing
	var txt = "";				// The text variable we'll be manipulating to insert our data to the page
	txt += "<table border='1'>"
	for (drink in data.drinks)
	{	
		txt += "<tr><th>Drink " + drink + "</th></tr>";	// The drink # we're on
		for (obj in data.drinks[drink]) 
		{	
			if (data.drinks[drink][String(obj)] != null) // If there's no info, don't include it
				txt += "<tr><td>"+obj+"</td><td>"+data.drinks[drink][String(obj)]+"</td></tr>"; // Insert keys & values for drink #
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
	//var thesearch = document.getElementById("mysearch").value;
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

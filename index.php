<html>
<style>
#SearchResults {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#SearchResults td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#SearchResults tr:nth-child(even){background-color: #f2f2f2;}

#SearchResults th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>

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

// Send the request to the API
function SendRequest(url)
{
	var request = new XMLHttpRequest();
	request.open("GET", url, true);
	request.onreadystatechange = function()
	{
		if(this.readyState==4 && this.status==200)
		{
			HandleResponse(this.responseText)
		}
	}
	request.send();
}

// Handle the data we got from the API
function HandleResponse(response)
{
	var data = JSON.parse(response); 	// parse the response into json array: data
	console.log(data);			// print to console for testing
	var txt = "";				// The text variable we'll be manipulating to insert our data to the page
	txt += "<table id='SearchResults' border='1'>"
	
	if (data.drinks)
	{
		for (drink in data.drinks)
		{	
			txt += "<tr><th>Drink "+drink+"</th></tr>";	// The drink # we're on
			for (obj in data.drinks[drink]) 
			{	
				if (data.drinks[drink][String(obj)] != null) // If there's no info, don't include it
				{
					txt += "<tr><td>"+obj+"</td><td>"+data.drinks[drink][String(obj)]+"</td></tr>"; // Insert keys & values for drink #
				}
			}
		}
	}
	else if (data.ingredients)
	{
		for (ingredient in data.ingredients)
		{
			for (obj in data.ingredients[ingredient])
			{
				txt += "<tr><td>"+obj+"</td><td>"+data.ingredients[ingredient][String(obj)]+"</td></tr>"; // Insert keys & values for
			}
		}
	}
	
	
	txt += "</table>"    
      	document.getElementById("searchResponse").innerHTML = txt;
}

function ClearResults()
{
	document.getElementById("searchResponse").innerHTML = "";
}

// Do a search - Get info from HTML then send request
function DoSearch() 
{
	var type = document.getElementById("searchType").value;		// the type of search
	var input = document.getElementById("mySearch").value;		// what the user typed in the search input
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/search.php?" + type + "=" + input; // the url to use to for the request, using input
	SendRequest(url);
}

function DoFilter()
{
	var type = document.getElementById("filterType").value;		// the type of search
	var input = document.getElementById("myFilter").value;		// what the user typed in the search input
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/filter.php?" + type + "=" + input; // the url to use to for the request, using input
	SendRequest(url);
}

function DoList()
{
	var type = document.getElementById("listType").value;		// the type of search
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/" + type; // the url to use to for the request, using input
	SendRequest(url);
}

function DoLookup()
{
	var type = document.getElementById("lookupType").value;		// the type of search
	var input = document.getElementById("myLookup").value;		// what the user typed in the search input
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/lookup.php?" + type + "=" + input; // the url to use to for the request, using input
	SendRequest(url);
}
</script>


<body>
<h3>Search</h3>
<select name="search type" id="searchType">
<option value="s">Cocktail</option>
<option value="f">First letter</option>
<option value="i">Ingredient</option>
</select>
<input type="text" placeholder="Enter your search keywords" id="mySearch" />
<button onclick="DoSearch()">Search</button>

<hr>

<h3>Filter</h3>
<select name="filter type" id="filterType">
<option value="i">Ingredient</option>
<option value="i">Ingredient</option>
</select>
<input type="text" placeholder="Enter your search keywords" id="myFilter" />
<button onclick="DoFilter()">Search</button>

<hr>

<h3>List</h3>
<select name="list type" id="listType">
<option value="random.php">1 Random</option>
<option value="randomselection.php">10 Random</option>
<option value="popular.php">Popular</option>
<option value="latest.php">Latest</option>
<option value="filter.php?a=Alcoholic">Alcoholic</option>
<option value="filter.php?a=Non_Alcoholic">Non-Alcoholic</option>
</select>
<button onclick="DoList()">List</button>

<hr>

<h3>ID Lookup</h3>
<select name="lookup type" id="lookupType">
<option value="i">Cocktail ID</option>
<option value="iid">Ingredient ID</option>
</select>
<input type="text" placeholder="Enter your search keywords" id="myLookup" />
<button onclick="DoSearch()">Search</button>

<hr>

<button onclick="ClearResults()">Clear Results</button>
<div id="searchResponse"></div>

<hr>

<h3>Register</h3>
it ain't done
<h3>Login</h3>
<div class="log-form">
	<input type="text" id="usr" placeholder="username" />
	<input type="password" id="psw" placeholder="password" />
	<button onclick="SendLoginRequest()">Login</button>
</div>

<br>

<div id="textResponse"> awaiting response </div>

</body>
</html>

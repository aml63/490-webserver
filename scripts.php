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
	else if (type == "addlike")
		request.send("type="+type+"&uname="+username+"&addlike="+info);
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
	if (type == "getlikes")
		if (text.likes != null)
			document.getElementById("favorites").innerHTML = text.likes;
}
function SendGetRequest(type, special)
{
	var username = "<?php echo $_SESSION['username']; ?>";
	
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleGetResponse(this.responseText, type, special);
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










// INDEX.PHP functions below

// API SEARCH REQUEST STUFF
function SendRequest(url) // Send the request to the API
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

function likeDrink(like)
{
	SendSetRequest(like, "addlike");
}

function HandleResponse(response) // Handle the data we got from the API
{
	var data = JSON.parse(response); 	// parse the response into json array: data
	console.log(data);			// print to console for testing
	var txt = "";				// The text variable we'll be manipulating to insert our data into the page
	
	txt += "<table id='SearchResults' border='1'>"
	
	if (data.drinks)
	{
		for (drink in data.drinks)
		{	
			txt += "<tr><th>"+drink+"</th><td><button onclick=likeDrink(`"+data.drinks[drink]['idDrink']+"`)>Like</button></td></tr>";	// The obj # we're on, usually a drink
			for (obj in data.drinks[drink]) 
			{	
				if (data.drinks[drink][String(obj)] != null) // Only include keys with values
				{
					if (String(obj) == "strCreativeCommonsConfirmed" || String(obj) == "strInstructionsDE") // Skip these
						continue;
					else if (String(obj) == "strDrinkThumb")
						txt += "<tr><td>"+obj+"</td><td><img class='center' src='"+data.drinks[drink][String(obj)]+"' width='300'></td></tr>"; // Insert keys & values for obj
					else
						txt += "<tr><td>"+obj+"</td><td>"+data.drinks[drink][String(obj)]+"</td></tr>"; // Insert keys & values for obj
				}
			}
		}
	}
	else if (data.ingredients)
	{
		for (ingredient in data.ingredients)
		{
			txt += "<tr><th>"+drink+"</th></tr>";	// The obj # we're on, in this case an ingredient
			for (obj in data.ingredients[ingredient])
			{
				txt += "<tr><td>"+obj+"</td><td>"+data.ingredients[ingredient][String(obj)]+"</td></tr>"; // Insert keys & values for obj
			}
		}
	}
	
	
	txt += "</table>"    
      	document.getElementById("searchResponse").innerHTML = txt;
}


// SEARCH FUNCTIONS
// Do a search - Get info from HTML then send request
function DoSearch() 
{
	var type = document.getElementById("searchType").value;		// the type of search
	var input = document.getElementById("mySearch").value;		// what the user typed in the search input
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/" + type + input; // the url to use to for the request, using input
	SendRequest(url);
}
function DoFilter()
{
	// myFilter
	// alcoholicFilter
	// categoryFilter
	// glassFilter
	
	//var type 		= document.getElementById("filterType").value;		// the type of search
	//var input 		= document.getElementById("myFilter").value;		// what the user typed in the search input
	var alcoholic 	= document.getElementById("alcoholicFilter").value;
	var category 	= document.getElementById("categoryFilter").value;
	var glass 		= document.getElementById("glassFilter").value;
	
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/filter.php?" + alcoholic + "&" + category + "&" + glass;
	
	SendRequest(url);
	console.log(url);
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


// Clear search results from the page - Not super necessary, but tidy
function ClearResults()
{
	document.getElementById("searchResponse").innerHTML = "";
}
</script>

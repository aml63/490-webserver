<script>

// profile.php functions (mainly)

// EDIT AND SAVE
function EditMode() // Swap to an edit mode
{
	// Swap the button to SAVE
	document.getElementById("editButton").innerHTML = "<button onclick='SaveEdit()'>Save Profile</button>";

	// Get current values right from the HTML, so we can insert it into an editable textarea
	var currBio = document.getElementById("bio").innerHTML;
	var currCab = document.getElementById("cabinet").innerHTML;
	
	// Transform!
	document.getElementById("bio").innerHTML = "<textarea rows='5' cols='90' maxlength='280' id='bioEdit'/>" + currBio + "</textarea>"
	
	SendListRequest();
	document.getElementById("cabinet").innerHTML = "<div id='cabdisc'><strong>**Only add ingredients from list! Don't add spaces! Don't remove tailling comma!**</strong></p><textarea rows='5' cols='90' maxlength='280' id='cabEdit'/>" + currCab + "</textarea>"
	
}
function SaveEdit() // Save edits, swap back to default view of the page with new values
{
	// Swap the button back to EDIT
	document.getElementById("editButton").innerHTML = "<button onclick='EditMode()'>Edit Profile</button>";

	// Get current values of input boxes, so we can turn it back to content
	var newBio = document.getElementById("bioEdit").value;
	var newCab = document.getElementById("cabEdit").value;
	
	// Save the edits to db
	SendSetRequest(newBio, "setbio");
	SendSetRequest(newCab, "setcabinet");
	
	// Transform! Back into content!
	document.getElementById("bio").innerHTML = newBio;
	document.getElementById("cabinet").innerHTML = newCab;
	document.getElementById("listResponse").innerHTML = ""; // get rid of search results
}




// CABINET EDITING
// Fetch an ingredient list and allow users to add stuff to their cabinet while in edit mode.
function HandleListResponse(response) // Handle the response to format it into the page
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
					// TODO: Make this skip results already found in the user's cabinet
					txt += "<tr><td>"+drink+"</td><td>"+data.drinks[drink][String(obj)]+"</td><td><button onclick='addIngredient(`"+data.drinks[drink][String(obj)]+"`)'>add</button></td></tr>";
			}
		}
	}
	txt += "</table>"    
      	document.getElementById("listResponse").innerHTML = txt;
}
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
function addIngredient(id) // Add an ingredient to user's cabinet, this is called from the "add" button on the ingredient list
{
	var cabinet = document.getElementById("cabEdit");
	
	if (cabinet.value.includes(id))
		alert("You already have one of those, buddy. I think you've got a problem.");
	else
		cabinet.value += id + ",";
}




// DATABASE INTERACTION FUNCTIONS
// Use these to set & get user info like bio, cabinet, likes, etc.

// SET
function HandleSetResponse(response)
{
	var text = JSON.parse(response); // We don't really need a response when setting things.
}
function SendSetRequest(info, type)
{
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
// GET 
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




// index.php functions (mainly)

// API SEARCH REQUEST STUFF
// SEARCH FUNCTIONS
// Do a search - Get info from HTML then send request
function DoSearch() // Basic search - Do a search for recipes
{
	var type = document.getElementById("searchType").value;		// the type of search
	var input = document.getElementById("mySearch").value;		// what the user typed in the search input
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/" + type + input; // the url to use to for the request, using input
	SendRequest(url);
}
function DoFilter() // Category search - Get the items in a particular category
{
	var alcoholic 	= document.getElementById("alcoholicFilter").value;
	var category 	= document.getElementById("categoryFilter").value;
	var glass 		= document.getElementById("glassFilter").value;
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/filter.php?" + alcoholic + "&" + category + "&" + glass;
	SendRequest(url);
}
function DoList() // List search - Catered lists like random, 10 random, popular, etc.
{
	var type = document.getElementById("listType").value;		// the type of search
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/" + type; // the url to use to for the request, using input
	SendRequest(url);
}
function DoLookup() // Lookup drink from 
{
	var type = document.getElementById("lookupType").value;		// the type of search
	var input = document.getElementById("myLookup").value;		// what the user typed in the search input
	var url = "https://www.thecocktaildb.com/api/json/v2/9973533/lookup.php?" + type + "=" + input; // the url to use to for the request, using input
	SendRequest(url);
}


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


function likeDrink(like) // This is called when the button "like" button in the search results is pressed
{
	SendSetRequest(like, "addlike");  // Appends a drink to the user's likes column
}


// Clear search results from the page - Not super necessary, but tidy
function ClearResults()
{
	document.getElementById("searchResponse").innerHTML = "";
}




// login.php functions (mainly)

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
	username = document.getElementById("usr").value;
	password = document.getElementById("psw").value;
	
	if (!username || !password)
	{
		alert("Username and password must be a value!");
		return;
	}
	
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
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
	
	if (!username || !password)
	{
		alert("Username and password must be a value!");
		return;
	}
	
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

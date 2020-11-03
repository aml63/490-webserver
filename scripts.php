<script>

// profile.php functions (mainly)

// Profile - EDIT AND SAVE
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


//Community Posting
function EditPost()
{

        //swap the button to SAVE
        document.getElementById("addButton").innerHTML = "<button onclick='SavePost()'>Save Post</button>";

        //get current values
        var currComm = document.getElementById("comm").innerHTML;

        //transform
        document.getElementById("comm").innerHTML = "<textarea rows='5' cols='90' maxlength='280' id='commEdit'/>" + currComm + "</textarea";
}
function SavePost()
{
        //swap back to add
        document.getElementById("addButton").innerHTML = "<button onclick='EditPost()'>Add Post</button>";

        //get values
        var newComm = document.getElementById("commEdit").value;

	// save edits to db
	SendSetRequest(newComm, "setComm";
        //transform
        document.gtElementByID("comm").innerHTML = newComm;
}

// Profile - CABINET EDITING
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
function HandleGetResponse(response, type, special)
{
	var text = JSON.parse(response);
	
	console.log(text);

	if (type == "getbio")
		if (text.bio != null)
			document.getElementById("bio").innerHTML = text.bio;
	if (type == "getcabinet")
		//if (text.cabinet != null)
			document.getElementById("cabinet").innerHTML = text.cabinet;
	if (type == "getlikes")
		if (text.likes != null)
		{
			if (special==1)
				document.getElementById("favorites").innerHTML = text.likes;
			else
				ID2Name(text.likes);
		}
	if (type == "getlikestats") // TODO: Sort this list by likes before filling the table
		if (text != null)
		{
			var txt = "<table border='1'><th>Drink ID</th><th>Likes</th>";
			for (index in text)
			{
				txt += "<tr><td>"+text[index].id+"</td><td>"+text[index].likes+"</td></tr>"; // TODO: text.dislikes 
			}
			txt += "</table>"
			document.getElementById("drinkLikes").innerHTML=txt;
		}
}
function SendGetRequest(type, special)
{
	var username = "<?php echo $_SESSION['username']; ?>"; // username or some other ID, like a drink
	
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

// API SEARCH/REQUEST STUFF
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
	
	SendLog("API Request: " + url);
}
function HandleResponse(response) // Handle the data we got from the API
{
	var data = JSON.parse(response); 	// parse the response into json array: data
	console.log(data);					// print to console for testing
	var txt = "";						// The text variable we'll be manipulating to insert our HTML into our div
	
	txt += "<table id='SearchResults' border='1'>"
	
	if (data.drinks)
	{
		for (drink in data.drinks) // This is where we begin iterating through the JSON data and stuffing it into an HTML table
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
					else if (String(obj) == "idDrink")
						txt += "<tr><td><button onclick=SendRequest(`https://www.thecocktaildb.com/api/json/v2/9973533/lookup.php?i="+data.drinks[drink][String(obj)]+"`)>"+obj+"</button></td><td>"+data.drinks[drink][String(obj)]+"</td></tr>";
					else
						txt += "<tr><td>"+obj+"</td><td>"+data.drinks[drink][String(obj)]+"</td></tr>"; // Insert keys & values for obj
				}
			}
		}
	}
	else if (data.ingredients) // For ingredients, the data comes back in "ingredients" as opposed to drinks, sometimes.
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
      	document.getElementById("searchResponse").innerHTML = txt; // Set div content to txt
}


function likeDrink(like) // This is called when the button "like" button in the search results is pressed
{
	SendSetRequest(like, "addlike");  // Appends a drink to the user's likes column
}

function ID2Name(id) // Translate individual likes from database and pulling info from API to get drink name and image
{
	var idArr = id.split(','); // translate commas in database to arrays
	var txt = "";
	txt += "<table border='1'>"
	txt += "<tr><th>ID</th><th>Name</th><th>Image</th></tr>";
	for (i = 0; i < idArr.length; i++) // array using for loop
	{
		var url = "https://www.thecocktaildb.com/api/json/v2/9973533/lookup.php?i=" + idArr[i];
		var request = new XMLHttpRequest();
		request.open("GET", url, false); // syncronous xhr request
		request.send();
		if(request.status==200)
		{ 
			var data = JSON.parse(request.responseText); 	// parse the response into json array: data
			console.log(data);				// print to console for testing

			if (data.drinks)
			{
				for (drink in data.drinks)
				{	
					txt += "<tr><td>" + idArr[i] + "</td>";
					for (obj in data.drinks[drink]) 
					{	
						if (data.drinks[drink][String(obj)] != null) // Only include keys with values
						{
							if (String(obj) == "strCreativeCommonsConfirmed" || String(obj) == "strInstructionsDE") // Skip these
								continue;
							else if (String(obj) == "strDrinkThumb")
								txt += "<td style='text-align: center'><img class='center' src='"+data.drinks[drink][String(obj)]+"' width='150'></td>"; // Insert drink image
							else if (String(obj) == "strDrink")
								txt += "<td>"+data.drinks[drink]['strDrink']+"</td>"; // Insert drink name
						}
					}
					txt += "</tr>";
				}
			}
		}
	}
	txt += "</table>";
	document.getElementById("favorites").innerHTML = txt;
}

// Clear search results from the page - Not super necessary, but tidy
function ClearResults()
{
	document.getElementById("searchResponse").innerHTML = "";
}




// login.php functions (mainly)

// LOGIN & REGISTRATION STUFF
function HandleLoginResponse(response, username)
{
	var text = JSON.parse(response);
	
	// Send them to a new page if they were logged in
	if (response == 1)
	{
		document.location.href = "index.php"
		SendLog("succesful login by: " + username);
	}
	else
	{
		document.getElementById("loginResponse").innerHTML = "Bad Credentials (response: "+text+")<p>";
		SendLog("failed login by: " + username);
	}
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
			HandleLoginResponse(this.responseText, username);
		}		
	}
	
	url = "type=login&uname="+username+"&pword="+password;
	
	request.send(url); // request we're sending thru rabbit?
	
	SendLog("login attempt by: " + username);
}


function HandleRegisterResponse(response, username)
{
	var text = JSON.parse(response);
	
	// Give them a confirmation that their credentials were registered
	if (response == 1)
	{
		document.getElementById("regResponse").innerHTML = "Registered! (code: "+text+")<p>";
		SendLog("succesful registration by: " + username);
	}
	else if (response == 0)
	{
		document.getElementById("regResponse").innerHTML = "Already Registered? (code: "+text+")<p>";
		SendLog("failed registration by: " + username + ". Code: " + text);
	}
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
			HandleRegisterResponse(this.responseText, username);
		}
	}
	
	url = "type=register&uname="+username+"&pword="+password;
	
	request.send(url); // request we're sending thru rabbit?
	
	SendLog("registration attempt by: " + username);
}




// Logs
function HandleLog(response)
{
	var text = JSON.parse(response);
}
function SendLog(msg)
{	
	if (!msg)
		return;
	
	var request = new XMLHttpRequest();
	request.open("POST","auth.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange = function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleLog(this.responseText);
		}		
	}
	
	request.send("type=log&msg="+msg); // request we're sending thru rabbit?
}




// Just a helper function for random numbers
function rangeRand(max) 
{
  return parseInt(Math.random() * (max+1));
}
</script>

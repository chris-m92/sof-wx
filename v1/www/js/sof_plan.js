//-------------------------------------------------------------------------------------------------------------------------
// Reads a user's cookie to get the saved ICAO list
//-------------------------------------------------------------------------------------------------------------------------
function readCookie() {
	
	// Cookie name
	var name = "icao-list=";
	
	// Decode the cookie
	var decodedCookie = decodeURIComponent(document.cookie);
	
	// Split the saved cookies
	var ca = decodedCookie.split(';');
	
	// Loop through the cookie array
	for (var i = 0; i < ca.length; i++) {
		// Get the cookie
		var c = ca[i];
		
		// Trim any whitespace
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		
		// Once we're at the name of the cookie, get the data of the cookie
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	
	// Cookie  doesn't exist
	return "";
}
//-------------------------------------------------------------------------------------------------------------------------
// Checks to see if the cookie exists and if it does, redirects the user using the saved list	//-------------------------------------------------------------------------------------------------------------------------
function checkCookie() {
	// Call the readCookie() function
	var icao_list = readCookie();
	
	// If there is a cookie
	if (icao_list != "") {	
		// Show the loading spinner
		$("#loading-card").removeClass("d-none");
		
		// Hide the form
		$("#form-card").addClass("d-none");
		
		// Redirect the user
		window.location.replace("http://59.13.133.91/?icao=" + icao_list + "&fromSaved=true");					
	}
}

//-------------------------------------------------------------------------------------------------------------------------
// Stuff that happens as soon as the page loads	//-------------------------------------------------------------------------------------------------------------------------
$(document).ready(function() {
	
	//======================
	// CUSTOM FUNCTIONS 
	//======================
	// Get any parameters from the URL (Expected icao)
	$.urlParam = function(name) {
		var results = new RegExp('[\?&*]' + name + '=([^&#]*)').exec(window.location.href);
		
		if (results == null) {
			return null;
		} else {
			return results[1] || 0;
		}
	}
		
	// Display the body (javaScript is enabled)
	$("#body-container").removeClass("d-none");
	$('[data-toggle="tooltip"]').tooltip()
	
	// Listen for the submit button to be clicked (If displayed for submitting data)
	$("#submit-button").click(function() {
		$("#loading-card").removeClass("d-none");
		$("#form-card").addClass("d-none");
	});
	//-------------------------------------------------------------------------------------------------------------------------
	// Listen for the save / delete ICAO list buttons to be clicked
	//-------------------------------------------------------------------------------------------------------------------------
	$("#save-icao-list-btn").click(function() {
		// Set the cookie and have it never expire
		document.cookie = "icao-list=" + $.urlParam("icao") + "; expires=Fri, 31 Dec 9999 23:59:59 UTC; path=/";				

		// Show an alert that the ICAO list was saved
		$("#alert").addClass("alert-success");
		$("#alert").removeClass("alert-warning");
		$("#alert").text("ICAO List saved successfully.");
	});
	
	
	$("#delete-icao-list-btn").click(function() {
		// Set the cookie to expire at UNIX Epoch, next refresh, it will be deleted
		document.cookie = "icao-list=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
		
		$("#alert").addClass("alert-warning");
		$("#alert").removeClass("alert-success");
		$("#alert").text("ICAO List deleted successfully.");
	});		
	
	// If there is no ICAO in the URL, check to see if there is a saved cookie
	if ($.urlParam("icao") == null) {
		checkCookie();
	}		
});
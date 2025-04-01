// This is a place for some tools required in the application
import { Popover } from "bootstrap"


window.UTILS = window.UTILS || { };

window.UTILS.helloWorld = function() {
	console.log( "Hello World" );
};

// Form hints.
window.UTILS.formHints = function() {
	$( ".help-hint" ).each( function() {
		var $this = $( this ),
			$field = $this.closest( ".form-group" ).find( ".form-control" ),
			title = $this.data( "title" ) || "",
			content = $this.html(),
			popoverOptions = {
				html: true,
				trigger: "focus",
				title: title,
				content: content
			};

		//$field.popover( popoverOptions );
		new Popover( $field.get(0), popoverOptions );
	} );
}

// Check whether login is available.
window.UTILS.loginAvaliabilityChecker = function() {
	/*
	 * Check whether login is available.
	 * Simple demo of working with an API.
	 */
	
	let login = document.querySelector( "#id_login" ),
	m = "Username is already taken.",
	h = "<p class=\"alert alert-danger\" style=\"display: none\" id=\"login_availability_status\">" + m + "</p>";
	login.closest( ".form-group" ).insertAdjacentHTML( "beforeend", h );
	let status = document.querySelector( "#login_availability_status" );

	login.addEventListener( "change", async () => {

		// Login input value to check.
		let value = login.value,
		lang = document.querySelector( "html").getAttribute( "lang" ),
		// API URL.
		url = new URL( "/api/" + lang + "/login_availabilities/detail/", window.location.origin ),
		// GET values for API. Available formats: xml, json, yaml, jsonp.
		data = {
			login: value,
			format: "json"
		};

		url.search = new URLSearchParams( data ).toString();


		if ( value !== "" ) {
			try {
				const response = await fetch( url );
				const responseData = await response.json();
				if( responseData.status !== "available" ) {
					window.UTILS.show( status );
				} else {
					window.UTILS.hide( status );
				}
			} catch ( error ){
        console.log( "Error Fetching data ",error);
    	}
		} else {
			window.UTILS.hide( status );
		}

	} );

	login.dispatchEvent(new Event( "change" ));

}

// Restores email addresses misted by the no_spam helper
window.UTILS.unobfuscateEmails = function() {
	$( ".atk14_no_spam" ).unobfuscate( {
		atstring: "[at-sign]",
		dotstring: "[dot-sign]"
	} );
}

// Links with the "blank" class are pointing to new window
window.UTILS.linksTargetBlank = function() {
	[ ...document.querySelectorAll( "a.blank" ) ].forEach( (elem) => {
		elem.setAttribute( "target", "_blank" )
	} );
}
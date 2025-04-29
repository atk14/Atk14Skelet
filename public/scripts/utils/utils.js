// This is a place for some tools required in the application
import { Popover } from "bootstrap"
import UnobfuscateEmails from "../nojquery/unobfuscate_v2.js";


window.UTILS = window.UTILS || { };

window.UTILS.helloWorld = function() {
	console.log( "Hello World" );
};

// Form hints.
window.UTILS.formHints = function() {
	[ ...document.querySelectorAll( ".help-hint" ) ].forEach( (elem) => {
		let field = elem.closest( ".form-group" ).querySelector( ".form-control" );
		let title = elem.dataset.title || "";
		let content = elem.innerHTML;
		let popoverOptions = {
			html: true,
			trigger: "focus",
			title: title,
			content: content
		};
		// Popover
		new Popover( field, popoverOptions );
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
	UnobfuscateEmails.unobfuscate( {
		atstring: "[at-sign]",
		dotstring: "[dot-sign]",
		selector: ".atk14_no_spam"
	} );
}

// Links with the "blank" class are pointing to new window
window.UTILS.linksTargetBlank = function() {
	[ ...document.querySelectorAll( "a.blank" ) ].forEach( (elem) => {
		elem.setAttribute( "target", "_blank" )
	} );
}
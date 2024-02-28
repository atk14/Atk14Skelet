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
	var $login = $( "#id_login" ),
	m = "Username is already taken.",
	h = "<p class='alert alert-danger'>" + m + "</p>",
	$status = $( h ).hide().appendTo( $login.closest( ".form-group" ) );

	$login.on( "change", function() {

		// Login input value to check.
		var value = $login.val(),
			lang = $( "html" ).attr( "lang" ),

		// API URL.
			url = "/api/" + lang + "/login_availabilities/detail/",

		// GET values for API. Available formats: xml, json, yaml, jsonp.
			data = {
				login: value,
				format: "json"
			};

		// AJAX request to the API.
		if ( value !== "" ) {
			$.ajax( {
				dataType: "json",
				url: url,
				data: data,
				success: function( json ) {
					if ( json.status !== "available" ) {
						$status.fadeIn();
					} else {
						$status.fadeOut();
					}
				}
			} );
		}
	} ).change();
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
SKELET = {
	common: {
		init: function() {
			// application-wide code
		}
	},
	 
	logins: {
		init: function() {
			// controller-wide code
		},
	 
		create_new: function() {
			// action-specific code

			/*
			 * Check whether login is available.
			 * Simple demo of working with an API..
			 */
			var id = "id_login",
				$login = $( "#" + id ),
				$label = $( "label[for='" + id + "']" ),
				label = $label.text();

			$login.on( "blur", function(ev) {
				// Login input value to check.
				var value = $login.val(),
				// API URL.
					url = "/api/" + $( "html" ).attr( "lang" ) + "/login_availabilities/detail/",
				// GET values for API. Available formats: xml, json, yaml, jsonp.
					data = {
						login: value,
						format: "json"
					};

				// AJAX request to the API.
				$.ajax({
					dataType: "json",
					url: url,
					data: data,
					success: function( json ) {
						$label.text( label + ": " + json.status );
					},
					error: function( jqXHR, status, errorThrown ) {
						$label.text( label + ": " + status + "(" + errorThrown + ")" );
					}
				});
			});
		}
	}
};


/*
 * Garber-Irish DOM-based routing.
 * See: http://goo.gl/z9dmd
 */
SKELET.UTIL = {
	exec: function( controller, action ) {
		var ns = SKELET,
			action = ( action === undefined ) ? "init" : action;

		if ( controller !== "" && ns[controller] && typeof ns[controller][action] == "function" ) {
			ns[controller][action]();
		}
	},

	init: function() {
		var body = document.body,
		controller = body.getAttribute( "data-controller" ),
		action = body.getAttribute( "data-action" );

		SKELET.UTIL.exec( "common" );
		SKELET.UTIL.exec( controller );
		SKELET.UTIL.exec( controller, action );
	}
};

$( document ).ready( SKELET.UTIL.init );

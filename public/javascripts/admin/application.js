/* global window */
(function( window, $, undefined ) {
	var document = window.document,

	ADMIN = {
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
			}
		}
	};


	/*
	 * Garber-Irish DOM-based routing.
	 * See: http://goo.gl/z9dmd
	 */
	ADMIN.UTIL = {
		exec: function( controller, action ) {
			var ns = ADMIN;

			if ( action === undefined ) {
				action = "init";
			}

			if ( controller !== "" && ns[controller] && typeof ns[controller][action] === "function" ) {
				ns[controller][action]();
			}
		},

		init: function() {
			var body = document.body,
			controller = body.getAttribute( "data-controller" ),
			action = body.getAttribute( "data-action" );

			ADMIN.UTIL.exec( "common" );
			ADMIN.UTIL.exec( controller );
			ADMIN.UTIL.exec( controller, action );
		}
	};

	// Expose ADMIN to the global object.
	window.ADMIN = ADMIN;

	// Initialize application.
	$( document ).ready( ADMIN.UTIL.init );
})( window, window.jQuery );

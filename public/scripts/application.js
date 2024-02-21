/* global window */
( function( window, $, undefined ) {
	"use strict";
	var document = window.document,
	UTILS = window.UTILS, // Uncomment this if you need something from UTILS

	APPLICATION = {
		common: {

			// Application-wide code.
			init: function() {

				// Restores email addresses misted by the no_spam helper
				UTILS.unobfuscateEmails();

				// Links with the "blank" class are pointing to new window
				UTILS.linksTargetBlank();

				// Form hints.
				UTILS.formHints();

				// Init Swiper
				UTILS.initSwiper();
			}
		},

		logins: {
			create_new: function() {
				document.getElementById( "id_login" ).focus();
			}
		},

		users: {

			// Controller-wide code.
			init: function() {
			},

			// Action-specific code
			create_new: function() {

				// Check whether login is available.
				UTILS.loginAvaliabilityChecker();
				
			}
		},

		// In this json, the actions for namespace "api" can be defined
		api: {
			common: {

				// Application-wide code.
				init: function() {

					// Restores email addresses misted by the no_spam helper
					UTILS.unobfuscateEmails();

					// Links with the "blank" class are pointing to new window
					UTILS.linksTargetBlank();

					// Form hints.
					UTILS.formHints();
				}
			}

		}

	};

	/*
	 * Garber-Irish DOM-based routing.
	 * See: http://goo.gl/z9dmd
	 */
	APPLICATION.INITIALIZER = {
		exec: function( namespace, controller, action ) {
			var ns = APPLICATION,
				c = controller,
				a = action;

			if( namespace && namespace.length > 0 && ns[ namespace ] ) {
				ns = ns[ namespace ];
			}

			if ( a === undefined ) {
				a = "init";
			}

			if ( c !== "" && ns[ c ] && typeof ns[ c ][ a ] === "function" ) {
				ns[ c ][ a ]();
			}
		},

		init: function() {
			var body = document.body,
			namespace = body.getAttribute( "data-namespace" ),
			controller = body.getAttribute( "data-controller" ),
			action = body.getAttribute( "data-action" );

			APPLICATION.INITIALIZER.exec( namespace, "common" );
			APPLICATION.INITIALIZER.exec( namespace, controller );
			APPLICATION.INITIALIZER.exec( namespace, controller, action );
		}
	};

	// Expose APPLICATION to the global object.
	window.APPLICATION = APPLICATION;

	// Initialize application.
	APPLICATION.INITIALIZER.init();
} )( window, window.jQuery );

/* global window */
( function( window, $, undefined ) {
	"use strict";
	var document = window.document,
	UTILS = window.UTILS, // Uncomment this if you need something from UTILS

	APPLICATION = {
		common: {			
			// Application-wide code.
			init: function() {

				// Detect Bootstrap version
				if( typeof bootstrap.Tooltip.VERSION !== "undefined" ){
					window.bootstrapVersion = parseInt( Array.from( bootstrap.Tooltip.VERSION )[0] );
				} else {
					window.bootstrapVersion = parseInt( Array.from( $.fn.tooltip.Constructor.VERSION )[0] );
				}

				// Restores email addresses misted by the no_spam helper
				$( ".atk14_no_spam" ).unobfuscate( {
					atstring: "[at-sign]",
					dotstring: "[dot-sign]"
				} );

				// Links with the "blank" class are pointing to new window
				$( "a.blank" ).attr( "target", "_blank" );

				// Form hints.
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

						if( window.bootstrapVersion === 5 ){
							new bootstrap.Popover( $field.get(0), popoverOptions );
						}else{
							$field.popover( popoverOptions );
						}
				} );

				// Init Swiper
				UTILS.initSwiper();

			}
		},

		logins: {
			create_new: function() {
				$( "#id_login" ).focus();
			}
		},

		users: {

			// Controller-wide code.
			init: function() {
			},

			// Action-specific code
			create_new: function() {
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
		},

		// In this json, the actions for namespace "api" can be defined
		api: {

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

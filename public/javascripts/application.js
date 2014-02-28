/* global window */
(function( window, $, undefined ) {
	var document = window.document,

	SKELET = {
		common: {
			init: function() {
				// application-wide code

				// Form hints.
				$( ".help-hint" ).each( function() {
					var $this = $( this ),
						$field = $this.closest( ".form-group" ).find( ".form-control" ),
						title = $this.data( "title" ) || "",
						content = $this.html();

					$field.popover({
						html: true,
						trigger: "focus",
						title: title,
						content: content
					});
				});
			}
		},

		users: {
			init: function() {
				// controller-wide code
			},

			create_new: function() {
				// action-specific code

				/*
				 * Check whether login is available.
				 * Simple demo of working with an API.
				 */
				var $login = $( "#id_login" ),
					$status = $( "<p class='alert alert-danger col-sm-4 col-sm-offset-2'>Username is already taken.</p>" ).hide().appendTo( $login.closest(".form-group") );

				$login.on( "change", function() {
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
					if ( value !== "" ) {
						$.ajax({
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
						});
					}
				}).change();
			}
		}
	};


	/*
	 * Garber-Irish DOM-based routing.
	 * See: http://goo.gl/z9dmd
	 */
	SKELET.UTIL = {
		exec: function( controller, action ) {
			var ns = SKELET;

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

			SKELET.UTIL.exec( "common" );
			SKELET.UTIL.exec( controller );
			SKELET.UTIL.exec( controller, action );
		}
	};

	// Expose SKELET to the global object.
	window.SKELET = SKELET;

	// Initialize application.
	$( document ).ready( SKELET.UTIL.init );
})( window, window.jQuery );

/* global window */
( function( window, $, undefined ) {
	"use strict";
	var document = window.document,
		//UTILS = window.UTILS, // Uncomment this if you need something from UTILS

	APPLICATION = {
		common: {

			// Application-wide code.
			init: function() {

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

					$field.popover( popoverOptions );
				} );
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

		tests: {
			async_file_upload: function() {
					var fileuploadOptions,
						removeButtonHandler,
						confirmButtonHandler,
						escapeHtml = function( unsafe ) {
							return unsafe
								.replace( /&/g, "&amp;" )
								.replace( /</g, "&lt;" )
								.replace( />/g, "&gt;" )
								.replace( /"/g, "&quot;" )
								.replace( /'/g, "&#039;" );
						};

					fileuploadOptions = {
						dataType: "json",
						url: "/api/en/temporary_file_uploads/create_new/?format=json",
						maxChunkSize: 1024 * 1024,
						multipart: false,
						sequentialUploads: true,
						dropZone: $(this),
						start: function() {
							this.$wrap = $( this ).parents( ".js--async-file" );
							this.$wrap.html( this.$wrap.data( "template_loading" ) );
							this.$progress = this.$wrap.find( ".progress-bar" );
						},
						progressall: function( e, data ) {
							var progress = parseInt( data.loaded / data.total * 100, 10 );

							this.$progress.css(
								"width",
								progress + "%"
							);
						},
						done: function( e, data ) {
							var $wrap = this.$wrap;
							console.log( data );
							var template = $wrap.data( "template_done" );
							template = template
								.replace( "%filename%", escapeHtml( data.result.filename ) )
								.replace( "%fileext%", escapeHtml( data.result.filename.split( "." ).pop().toLowerCase() ) )
								.replace( "%filesize_localized%", escapeHtml( data.result.filesize_localized ) )
								.replace( "%token%", escapeHtml( data.result.token ) )
								.replace( "%name%", escapeHtml( $wrap.data( "name" ) ) )
								.replace( "%destroy_url%", escapeHtml( data.result.destroy_url ) );

							$wrap.html( template );

							$wrap.find( ".js--remove" ).on( "click",  removeButtonHandler );
						},
						fail: function( e, data ) {
							var $wrap = this.$wrap;
							var errMsg = "Error occurred";
							if( data._response && data._response.jqXHR && data._response.jqXHR.responseJSON && data._response.jqXHR.responseJSON[ 0 ] ) {
								errMsg = data._response.jqXHR.responseJSON[ 0 ];
							}
							var template = $wrap.data( "template_error" );
							template = template
								.replace( "%error_message%", escapeHtml( errMsg ) );

							$wrap.html( template );
							$wrap.find( ".js--confirm" ).on( "click",  confirmButtonHandler );
						}
					};

					removeButtonHandler = function() {
						var $button = $( this );
						var $wrap = $button.parents( ".js--async-file" );
						$.ajax( {
							url: $button.data( "destroy_url" ),
							method: "POST",
						} );
						$wrap.html( $wrap.data( "input" ) );
						$wrap.find( "input[type=file]" ).fileupload( fileuploadOptions );
					};

					confirmButtonHandler = function() {
						var $button = $( this );
						var $wrap = $button.parents( ".js--async-file" );
						$wrap.html( $wrap.data( "input" ) );
						$wrap.find( "input[type=file]" ).fileupload( fileuploadOptions );
					};

					$( ".js--async-file input[type=file]" ).fileupload( fileuploadOptions );
					$( ".js--async-file .js--remove" ).on( "click", removeButtonHandler );
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

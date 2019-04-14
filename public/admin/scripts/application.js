/* global window */
( function( window, $, undefined ) {
	var document = window.document,
		ace = window.ace,
		markdown = window.markdown,
		//UTILS = window.UTILS, // Uncomment this if you need something from UTILS

	ADMIN = {

		common: {

			// Application-wide code.
			init: function() {

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

				// Markdown Editor requires Ace
				ace.config.set( "basePath", "/public/admin/dist/scripts/ace/" );
				$.each( $( "textarea[data-provide=markdown]" ), function( i, el ) {
					$( el ).markdownEditor( {
						preview: true,
						onPreview: function( content, callback ) {
							var html = markdown.toHTML( content );
							callback( html );
						}
					} );
				} );
			}
		},

		articles: {
			create_new: function() {
				ADMIN.utils.tagsSuggest( "#id_tags" );
			},
			edit: function() {
				ADMIN.utils.tagsSuggest( "#id_tags" );
			}
		},

		utils: {
			tagsSuggest: function( selector ) {
				var $input = $( selector ),
					lang = $( "html" ).attr( "lang" ),
					url = "/api/" + lang + "/tags_suggestions/?format=json&q=",
					cache = {},
					term, terms;

				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( t ) {
					return split( t ).pop();
				}

				if ( !$input.length ) {
					return;
				}

				$input.autocomplete( {
					minLength: 1,
					source: function( request, response ) {
						term = extractLast( request.term );

						if ( term in cache ) {
							response( cache[ term ] );
						} else {
							$.getJSON( url + term, function( data ) {
								cache[ term ] = data;
								response( data );
							} );
						}
					},
					search: function() {
						term = extractLast( this.value );

						if ( term.length < 1 ) {
							return false;
						}
					},
					focus: function() {
						return false;
					},
					select: function( event, ui ) {
						terms = split( this.value );
						terms.pop();
						terms.push( ui.item.value );
						terms.push( "" );
						this.value = terms.join( " , " );
						return false;
					}
				} );
			}
		}
	};

	/*
	 * Garber-Irish DOM-based routing.
	 * See: http://goo.gl/z9dmd
	 */
	ADMIN.INITIALIZER = {
		exec: function( controller, action ) {
			var ns = ADMIN,
				c = controller,
				a = action;

			if ( a === undefined ) {
				a = "init";
			}

			if ( c !== "" && ns[ c ] && typeof ns[ c ][ a ] === "function" ) {
				ns[ c ][ a ]();
			}
		},

		init: function() {
			var body = document.body,
			controller = body.getAttribute( "data-controller" ),
			action = body.getAttribute( "data-action" );

			ADMIN.INITIALIZER.exec( "common" );
			ADMIN.INITIALIZER.exec( controller );
			ADMIN.INITIALIZER.exec( controller, action );
		}
	};

	// Expose ADMIN to the global object.
	window.ADMIN = ADMIN;

	// Initialize application.
	ADMIN.INITIALIZER.init();
} )( window, window.jQuery );

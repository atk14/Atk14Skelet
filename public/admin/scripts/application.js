/* global window */
( function( window, $, undefined ) {
	var document = window.document,

	ADMIN = {
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
	ADMIN.UTIL = {
		exec: function( controller, action ) {
			var ns = ADMIN,
				c = controller,
				a = action;

			if ( a === undefined ) {
				a = "init";
			}

			if ( c !== "" && ns[c] && typeof ns[c][a] === "function" ) {
				ns[c][a]();
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
} )( window, window.jQuery );

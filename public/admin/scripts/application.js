/* Imports */
const bootstrap = require ( "bootstrap" );
window.$ = window.jQuery = require( "jquery" );
const jqueryUI = require ( "jquery-ui-bundle" ); // eslint-disable-line
//const blueimp = require ( "blueimp-file-upload/js/jquery.fileupload.js" );
//const fileupload = require ( "blueimp-file-upload" );
import "blueimp-file-upload/js/vendor/jquery.ui.widget.js";
import "blueimp-file-upload/js/jquery.iframe-transport.js";
import "blueimp-file-upload/js/jquery.fileupload.js";
import "blueimp-file-upload/js/jquery.fileupload-image.js";
const mde = require ( "bootstrap-markdown-editor-4/dist/js/bootstrap-markdown-editor.min.js" ); // eslint-disable-line
const ATK14 = require( "atk14js" ); // eslint-disable-line
import "bootstrap4-notify";
import autocomplete from "autocompleter";

/* global window */
( function( window, $, undefined ) {
	var document = window.document,
		ace = window.ace,
		UTILS = window.UTILS,

	ADMIN = {

		common: {

			// Application-wide code.
			init: function() {
				// Detect Bootstrap version
				if( typeof bootstrap.Tooltip.VERSION !== "undefined" ){
					window.bootstrapVersion = parseInt( Array.from( bootstrap.Tooltip.VERSION )[0] );
				} else {
					window.bootstrapVersion = parseInt( Array.from( $.fn.tooltip.Constructor.VERSION )[0] );
				}

				ADMIN.utils.handleSortables();
				ADMIN.utils.handleSuggestions();
				ADMIN.utils.handleTagsSuggestions();
				ADMIN.utils.initializeMarkdonEditors();
				ADMIN.utils.handleXhrImageUpload();
				ADMIN.utils.handleCopyIobjectCode();

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

				UTILS.leaving_unsaved_page_checker.init(); // TODO zprovoznit

				// Back to top button display and handling
				$( window ).on( "scroll", function(){
					var backToTopBtn = $ ( "#js-scroll-to-top" );
					if( $( window ).scrollTop() > 100 ) {
						backToTopBtn.addClass( "active" );
					} else {
						backToTopBtn.removeClass( "active" );
					}
				} );
				$( window ).trigger( "scroll" );

				$ ( "#js-scroll-to-top" ).on( "click", function( e ){
					e.preventDefault();
					$( "html, body" ).animate( { scrollTop: 0 }, "fast" );
				} );

				UTILS.async_file_upload.init(); // TODO zprovoznit

				// Admin menu toggle on small devices
				$( ".nav-section__toggle" ).on( "click", function( e ) {
					e.preventDefault();
					$( this ).closest( ".nav-section" ).toggleClass( "expanded" );
				} );

				// Dark mode toggle 
				$( "#js--darkmode-switch" ).on( "click", function(){
					var mode;
					if( $(this).prop( "checked" ) ) {
						$( "body" ).addClass( "dark-mode" ); // BS4
						$( "body" ).attr( "data-bs-theme", "dark" ); // BS5
						mode = "dark";
						document.cookie = "dark_mode=1;path=/";
					} else {
						$( "body" ).removeClass( "dark-mode" ); // BS4
						$( "body" ).attr( "data-bs-theme", "light" ); // BS5
						mode = "light";
						document.cookie = "dark_mode=;path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT";
					}

					$( "body" ).addClass( "dark-mode-transition" );
					setTimeout( function(){ $( "body" ).removeClass( "dark-mode-transition" ); }, 1000 );

					// darkModeChange event is triggered on dark mode de/activation
					var evt = new CustomEvent( "darkModeChange", { detail: mode } );
					document.dispatchEvent(evt);
				} );
			}

		},

		utils: {

			initializeMarkdonEditors: function() {

				// Markdown Editor requires Ace
				ace.config.set( "basePath", "/public/admin/dist/scripts/ace/" );
				$.each( $( "textarea[data-provide=markdown]" ), function( i, el ) {
					$( el ).markdownEditor( {
						preview: true,
						onPreview: function( content, callback ) {
							var lang = $( "html" ).attr( "lang" );
							$.ajax( {
								type: "POST",
								url: "/api/" + lang + "/markdown/transform/",
								data: {
									source: content,
									base_href: $( el ).data( "base_href" )
								},
								success: function( output ) {
									callback( output );
								}
							} );
						}
					} );
				} );
			},

			handleXhrImageUpload: function() {
				
				$( ".js--xhr_upload_image_form" ).each( function() {

					var $form = $( this );
					var $wrap = $form.closest(".js--image_gallery_wrap");
					var $dropZone = $form.closest(".drop-zone");
					var highglightDropZone = function() { $dropZone.addClass("drop-zone-highlight"); };
					var unhighglightDropZone = function() { $dropZone.removeClass("drop-zone-highlight"); };

					$dropZone.on( "dragenter",  highglightDropZone );
					$dropZone.on( "dragover",  highglightDropZone );
					$dropZone.on( "dragleave",  unhighglightDropZone );
					$dropZone.on( "drop",  unhighglightDropZone );

					var url = $form.attr( "action" ),
						$progress = $wrap.find( ".progress-bar" ),
						$list = $wrap.find( ".list-group-images" ),
						$input = $form.find("input");
						$input.data("url",url);

					$input.fileupload( {
						dropZone: $dropZone,
						dataType: "json",
						multipart: false,
						start: function() {
							$progress.show();
						},
						progressall: function( e, data ) {
							var progress = parseInt( data.loaded / data.total * 100, 10 );

							$progress.css(
								"width",
								progress + "%"
							);
						},
						done: function( e, data ) {

							// This is the same grip like in handleSortables
							var glyph = "<span class='fas fa-grip-vertical text-secondary handle pr-3' " +
								" title='sorting'></span>";

							$( data.result.image_gallery_item )
								.addClass( "not-processed" )
								.prepend( glyph )
								.appendTo( $list );

							$list.sortable( "refresh" );
						},
						stop: function() {
							$list.find( ".not-processed" )
								.prepend( "<span class='glyphicon glyphicon-align-justify'></span>" )
								.removeClass( "not-processed" );

							$progress.hide().css(
								"width",
								"0"
							);
						}
					} );

				} );
			},

			handleFormErrors: function( errors ) {
				$.each( errors, function( field, errorList ) {
					var $field = $( "#id_" + field ),
						$fg = $field.closest( ".form-group" ),
						errorMsgs = [];

					// Add proper class for error styling.
					$fg.addClass( "has-error" );

					// Prepeare error messages list.
					errorMsgs.push( "<ul class='help-block help-block-errors'>" );
					$.each( errorList, function( i, errorMsg ) {
						errorMsgs.push( "<li>" + errorMsg + "</li>" );
					} );
					errorMsgs.push( "</ul>" );

					// Insert error messages list into form.
					$( errorMsgs.join( "" ) ).insertAfter( $field );
				} );
			},

			clearErrorMessages: function( $form ) {
				$form.find( ".has-error" ).removeClass( "has-error" );
				$form.find( ".help-block-errors" ).remove();
			},

			// ADMIN.utils.handleFormErrors();
			// ADMIN.utils.handleFormErrors( ".list-sortable" );
			// ADMIN.utils.handleFormErrors( $element.find( "ul" ) );
			handleSortables: function( sortable ) {

				// Sortable lists.
				if ( sortable === undefined ) {
					$sortable = $( ".list-sortable" );
				} else {
					$sortable = $( sortable );
				}

				var $sortable = $( ".list-sortable" ),
					glyph = "<span class='fas fa-grip-vertical text-secondary handle pr-3' " +
						" title='sorting'></span>",
					url, $item, data, $list, id;

				if ( $sortable.length ) {
					$sortable.find( ".list-group-item" ).prepend( glyph );

					$sortable.sortable( {
						cancel: "strong",
						handle: ".handle",
						opacity: 0.9,
						update: function( jqEv, ui ) {
							$item = $( ui.item );
							$list = $item.closest( ".list-sortable" );
							url = $list.data( "sortable-url" );
							id = $list.data( "sortable-param" ) || "id";
							data = {
								rank: $item.index()
							};
							data[ id ] = $item.data( "id" );

							$.ajax( {
								type: "POST",
								url: url,
								data: data,
								success: function() {
								},
								error: function() {
								}
							} );
						}
					} );
				}
			},

			// Suggests anything according by an url
			handleSuggestions: function() {
				var inputs = $( "[data-suggesting='yes']" );
				inputs.each( function( i, el ){
					var $input = $( el ),
							url = $input.data( "suggesting_url" );
										
					autocomplete({
						// see https://github.com/kraaden/autocomplete
						input: el,
						fetch: function( text, update ) {
								text = text.toLowerCase();								
								$.getJSON( url, { q: text }, function( data ) {
									update( data );
								} );
						},
						render: function( item ) {
							var div = document.createElement( "div" );
							div.textContent = item;
							return div;
						},
						onSelect: function( item, input ) {
								input.value = item;
						},
						preventSubmit: 2,
						disableAutoSelect: true,
						debounceWaitMs: 100,
					});
				});
			},

			// Suggests tags
			handleTagsSuggestions: function() {
				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( t ) {
					return split( t ).pop();
				}
				$( "[data-tags_suggesting='yes']" ).each( function( i, el ){
					var $input = $( el ),
						lang = $( "html" ).attr( "lang" ),
						url = "/api/" + lang + "/tags_suggestions/?format=json&q=",
						cache = {},
						term, terms;

						$input.attr( "autocomplete", "off" );
						
						autocomplete({
						input: el,
						fetch: function( text, update ) {
							term = extractLast( text.toLowerCase() );

							if ( term.length > 0 ) {
								

								if ( term in cache ) {
									update( cache[ term ] );
								} else {
									$.getJSON( url, { q: term }, function( data ) {
										cache[ term ] = data;
										update( data );
									} );
								}

							}
						},
						render: function( item ) {
							var div = document.createElement( "div" );
							div.textContent = item;
							return div;
						},
						onSelect: function( item, input ) {
								terms = split( input.value );
								terms.pop(); 
								terms.push( item );
								terms.push( "" );
								input.value = terms.join( ", " );
						},
						preventSubmit: 2,
						disableAutoSelect: true,
						debounceWaitMs: 100,
						minLength: 1,
					});
				} );
			},

			// Copy iobject to clipboard
			handleCopyIobjectCode: function() {
				if( window.bootstrapVersion === 5 ){
					$( ".iobject-copy-code" ).each( function() {
						new bootstrap.Popover( this );
					} );
				} else {
					$( ".iobject-copy-code" ).popover();
				}
				$( ".iobject-copy-code" ).on( "click", function( e ) {
					e.preventDefault();
					var code = $( this ).closest( ".iobject-code-wrap" ).find( ".iobject-code" ).text();
					var el = document.createElement( "textarea" );
					el.value = code;
					document.body.appendChild( el );
					el.select();
					document.execCommand( "copy" );
					document.body.removeChild( el );
					$( this ).trigger( "focus" );
				} );
			}
		}
	};

	/*
	 * Garber-Irish DOM-based routing.
	 * See: http://goo.gl/z9dmd
	 */
	ADMIN.INITIALIZER = {
		exec: function( namespace, controller, action ) {
			var ns = ADMIN,
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

			ADMIN.INITIALIZER.exec( namespace, "common" );
			ADMIN.INITIALIZER.exec( namespace, controller );
			ADMIN.INITIALIZER.exec( namespace, controller, action );
		}
	};

	// Expose ADMIN to the global object.
	window.ADMIN = ADMIN;

	// Initialize application.
	ADMIN.INITIALIZER.init();
} )( window, window.jQuery );

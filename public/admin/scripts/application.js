/* global window */
( function( window, $, undefined ) {
	var document = window.document,
		ace = window.ace,
		UTILS = window.UTILS,

	ADMIN = {

		common: {

			// Application-wide code.
			init: function() {
				ADMIN.utils.handleSortables();
				window.UTILS.Suggestions.handleSuggestions();
				window.UTILS.Suggestions.handleTagsSuggestions();
				ADMIN.utils.initializeMarkdonEditors();
				UTILS.AsyncImageUploader.init();
				ADMIN.utils.handleCopyIobjectCode();

				// Form hints.
				UTILS.formHints();

				UTILS.leaving_unsaved_page_checker.init();

				// Back to top button display and handling
				ADMIN.utils.backToTopBtn();

				UTILS.async_file_upload.init();

				// Admin menu toggle on small devices
				ADMIN.utils.adminMenuToggler();

				// Dark mode toggle 
				ADMIN.utils.darkModeToggler();
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

			handleSortables: function() {
				// Sortable lists.

				var $sortable = $( ".list-sortable" ),
					glyph = "<span class='fas fa-grip-vertical text-secondary handle pr-3' " +
						" title='sorting'></span>";
					
				if ( $sortable.length ) {
					$sortable.find( ".list-group-item" ).prepend( glyph );

					$sortable.each( function( i, el ) {
						// eslint-disable-next-line no-undef
						new Sortable( el, {
							handle: ".handle",
							onUpdate: function( e ) {
								var $list = $( e.target );
								var $item = $( e.item );
								var url = $list.data( "sortable-url" );
								var id = $list.data( "sortable-param" ) || "id";
								var data = {
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
					} );
				}
			},

			// Copy iobject to clipboard
			handleCopyIobjectCode: function() {
				$( ".iobject-copy-code" ).popover();
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
			},

			// Back to top button display and handling
			backToTopBtn: function() {
				window.addEventListener( "scroll", function() {
					let backToTopBtn = this.document.querySelector( "#js-scroll-to-top" );
					if( window.scrollY  > 100 ) {
						backToTopBtn.classList.add( "active" );
					} else {
						backToTopBtn.classList.remove( "active" );
					}
				} );

				window.dispatchEvent( new Event( "scroll" ) );

				document.querySelector( "#js-scroll-to-top" ).addEventListener( "click", function( e ) {
					e.preventDefault();
					let els = document.querySelectorAll( "html,body" );
					console.log( "els", els );
					window.scroll( { top: 0, left: 0, behavior: "smooth" } );
				} );
				
			},

			// Admin menu toggle on small devices
			adminMenuToggler: function() {
				document.querySelector( ".nav-section__toggle" ).addEventListener( "click", function( e ) {
					e.preventDefault();
					this.closest( ".nav-section" ).classList.toggle( "expanded" );
				} );
			},

			// Dark mode toggle 
			darkModeToggler: function() {
				document.getElementById( "js--darkmode-switch" ).addEventListener( "click", function() {
					var body = document.querySelector( "body" );
					if( this.checked ){
						body.classList.add( "dark-mode" );
						document.cookie = "dark_mode=1;path=/";
					} else {
						body.classList.remove( "dark-mode" );
						document.cookie = "dark_mode=;path=/";
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

window.$ = window.jQuery = require( "jquery" );
const mde = require ( "bootstrap-markdown-editor-4/dist/js/bootstrap-markdown-editor.min.js" ); // eslint-disable-line

window.UTILS = window.UTILS || { };

window.UTILS.initializeMarkdonEditors = function() {
	var ace = window.ace;
	// Markdown Editor requires Ace
	ace.config.set( "basePath", "/public/dist/scripts/ace/" );
	$.each( $( "textarea[data-provide=markdown]" ), function( i, el ) {
		$( el ).markdownEditor( {
			preview: true,
			onPreview: function( content, callback ) {

				// match md-editor and md-preview heights
				var editorHeight = $( el ).parent().find( ".md-editor" ).height();
				if ( editorHeight ) {
					$(el).parent().find( ".md-preview" ).height( editorHeight );
				}
				var lang = $( "html" ).attr( "lang" );
				$.ajax( {
					type: "POST",
					url: "/api/" + lang + "/markdown/transform/",
					data: {
						source: content,
						base_href: $( el ).data( "base_href" )
					},
					success: function( output ) {
						output = "<div class=\"md-preview__viewport preview--desktop\"> " + output + " </div>";
						callback( output );
						// Dispatch event for other modules to initialize stuff in the preview area
						let parent = el.closest( ".form-group" ).getAttribute( "class" );
						let eventDetail = { element: el.parentElement.querySelector( ".md-preview" ), parent: parent };
						window.dispatchEvent( new CustomEvent( "markdownPreviewUpdated", { detail: eventDetail } ) );
						// Initialize preview mode toggle
						window.UTILS.PreviewModeToggle.init( el.parentElement.querySelector( ".md-preview" ) );
					}
				} );
			}
		} );
	} );
};

window.UTILS = window.UTILS || { };

window.UTILS.initializeMarkdonEditors = function() {
	// Markdown Editor requires Ace
	ace.config.set( "basePath", "/public/dist/scripts/ace/" );
	$.each( $( "textarea[data-provide=markdown]" ), function( i, el ) {
		if ( $( el ).next( ".md-container" ).length ) {
			return; // already has an editor (kept when its form was replaced, see edit_form_replacer.js)
		}
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
						if ( window.UTILS.MDPreviewStyles ) {
							// custom CSS applies to the preview only
							window.UTILS.MDPreviewStyles.apply( el.parentElement.querySelector( ".md-preview" ) );
						}
						window.UTILS.initSwiper();
						window.UTILS.PreviewModeToggle.init( el.parentElement.querySelector( ".md-preview" ) );
					}
				} );
			}
		} );
	} );
};

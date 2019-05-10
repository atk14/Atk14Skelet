window.UTILS = window.UTILS || { };

window.UTILS.leaving_unsaved_page_checker = { };

window.UTILS.leaving_unsaved_page_checker.init = function() {

	var $ = window.jQuery;

	// Saving post method
	var currentRequestMethod;
	$("form").on( "submit", function() {
		currentRequestMethod = $( this ).attr( "method" );
		if( currentRequestMethod ) {
			currentRequestMethod = currentRequestMethod.toLowerCase();
		}
	} );

	// Saving post forms state
	$( "form[method=post]" ).each( function() {
		var $form = $( this );
		$form.data( "initial_state", $form.serialize() );
	} );

	// Asking for confirmation in case of exiting page without form saving
	$( window ).on( "beforeunload", function() {
		var rv, method = currentRequestMethod;
		currentRequestMethod = null;
		console.log( "method: " + method );
		if ( method === "post" ) {
			return rv;
		}
		$( "form[method=post]" ).each( function() {
			var $form = $( this );
			var initial_state = $form.data( "initial_state" );
			var current_state = $form.serialize();
			if ( initial_state && initial_state !== current_state ) {
				rv = "Are you sure to leave the page without saving?";
			}
		} );
		return rv;
	}	);

};

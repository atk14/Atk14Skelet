window.UTILS = window.UTILS || { };

window.UTILS.leaving_unsaved_page_checker = { };

window.UTILS.leaving_unsaved_page_checker.init = function() {

	//var $ = window.jQuery;

	// Saving post method
	var currentRequestMethod;

	[...document.querySelectorAll( "form" )].forEach( function( frm ) {
		frm.addEventListener( "submit", function() {
			let currentRequestMethod = frm.getAttribute( "method" );
			if ( currentRequestMethod ) {
				currentRequestMethod = currentRequestMethod.toLowerCase();
			}
		} );
	} );

	/*$( "form" ).on( "submit", function() {
		currentRequestMethod = $( this ).attr( "method" );
		if ( currentRequestMethod ) {
			currentRequestMethod = currentRequestMethod.toLowerCase();
		}
	} );*/

	// Saving post forms state
	[...document.querySelectorAll( "form[method='post']" )].forEach( function( frm ) {
		frm.dataset.initialState = new URLSearchParams( new FormData( frm ) ).toString();
	} );

	// Saving post forms state
	/*$( "form[method=post]" ).each( function() {
		var $form = $( this );
		$form.data( "initial_state", $form.serialize() );
	} );*/

	// Asking for confirmation in case of exiting page without form saving
	window.addEventListener( "beforeunload", function( e ) {
		console.log( "beforeunload", e );
		//e.preventDefault();
		var rv, method = currentRequestMethod;
		currentRequestMethod = null;
		console.log( "method: " + method );
		if ( method === "post" ) {
			return rv;
		}
		[...document.querySelectorAll( "form[method='post']" )].forEach( function( frm ) {
			var initialState = frm.dataset.initialState;
			var currentState = new URLSearchParams( new FormData( frm ) ).toString();
			console.log( "initialState: " + initialState );
			console.log( "currentState: " + currentState );
			console.log( "initialState !== currentState: " + ( initialState !== currentState ) );
			console.log( "initialState === currentState: " + ( initialState === currentState ) );
			if ( initialState && initialState !== currentState ) {
				rv = "Are you sure to leave the page without saving?";
        e.preventDefault();
			}
		} );
		return rv;
	} );

	// Asking for confirmation in case of exiting page without form saving
	/*$( window ).on( "beforeunload", function() {
		var rv, method = currentRequestMethod;
		currentRequestMethod = null;
		console.log( "method: " + method );
		if ( method === "post" ) {
			return rv;
		}
		$( "form[method=post]" ).each( function() {
			var $form = $( this );
			var initialState = $form.data( "initial_state" );
			var currentState = $form.serialize();
			if ( initialState && initialState !== currentState ) {
				rv = "Are you sure to leave the page without saving?";
			}
		} );
		return rv;
	}	);*/

};

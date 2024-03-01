
/**
 * Replacement methods for some widely used jQuery methods
 * 
 * Usage:
 * window.UTILS.fadeOut( document.querySelector( "p.test" ) );
 * window.UTILS.fadeOut( document.querySelector( "p.test" ), "slow" );
 * window.UTILS.fadeOut( document.querySelector( "p.test" ), "fast" );
 * window.UTILS.fadeOut( document.querySelector( "p.test" ), 1500 );
 * 
 * fadeOut, fadeIn timing (same as in jQuery): <milliseconds>|"slow"|"fast"; default: 400ms, slow: 600ms, fast: 200ms (same as in jQuery) 
 * 
 */

window.UTILS = window.UTILS || { };


// Fade out - replacement to jQuery.fadeOut();

window.UTILS.fadeOut = function( el, t ) {
	console.log( "fadeOut" );
	console.trace();
	// timing
	if ( t === "slow" ) {
		t = 600;
	} else if ( t === "fast" ) {
		t = 200;
	} else if ( typeof( t ) !== "number" ) {
		t = 400;
	}

	// remember element`s display property - to be restored by elemet.fadeIn()
	if( window.getComputedStyle( el ).display !== "none" ) {
		el.dataset.display_before_fade = window.getComputedStyle( el ).display;
	}

	// fade out
	let fadeOutAnimation = el.animate( { opacity: 0}, { duration: t } );

	// set display:none after fade complete
	fadeOutAnimation.addEventListener( "finish", () => {
		el.style.display = "none";
		el.style.opacity = 0;
	} );
};

// Fade in - replacement to jQuery.fadeIn(); if element was hidden by element.fadeOut(), its original display mode will be restored, otherwise its display property will be set to "block".

window.UTILS.fadeIn = function( el, t ) {

	// get current style
	let currentStyle = window.getComputedStyle( el );
	
	// timing 
	if ( t === "slow" ) {
		t = 600;
	} else if ( t === "fast" ) {
		t = 200;
	} else if ( typeof( t ) !== "number" ) {
		t = 400;
	}

	// set appropriate display property
	if( currentStyle.display === "none" ) {
		// if element remembers it display property it had before element.fadeOut(), restore it, otherwise set it to "block"
		if( el.dataset.display_before_fade !== "none" ){
			el.style.display = el.dataset.display_before_fade;
		} else {
			el.style.display = "block";
		}
	}

	// fade in 
	if( currentStyle.opacity < 1 ){
		let fadeInAnimation = el.animate( [ { opacity: 0}, { opacity: 1} ], { duration: t } );
		fadeInAnimation.addEventListener( "finish", () => {
			el.style.opacity = 1;
			// restore original overflow if stored, otherwise set it to "auto"
			if( el.dataset.overflowy_before_fade ) {
				el.style.overflowY = el.dataset.overflowy_before_fade;
			} else {
				el.style.overflowY = "auto";
			}
		} );
	}
};

// Hide - replacement to jQuery.fadeOut();

window.UTILS.hide = function( el, t ) {
	console.log( "hide" );
	console.trace();
	// timing
	if ( t === "slow" ) {
		t = 600;
	} else if ( t === "fast" ) {
		t = 200;
	} else if ( typeof( t ) !== "number" ) {
		t = 400;
	}

	// remember element`s display property - to be restored by elemet.fadeIn()
	if( window.getComputedStyle( el ).display !== "none" ) {
		el.dataset.display_before_fade = window.getComputedStyle( el ).display;
	}
	el.dataset.overflowy_before_fade = window.getComputedStyle( el ).overflowY;

	// fade out, height to 0
	el.style.overflowY = "hidden";
	let fadeOutAnimation = el.animate( { opacity: 0, height: 0}, { duration: t } );

	// set display:none after fade complete
	fadeOutAnimation.addEventListener( "finish", () => {
		el.style.display = "none";
		el.style.opacity = 0;
	} );
};

// Show - replacement to jQuery.show(); if element was hidden by element.fadeOut() or element.hide, its original display mode will be restored, otherwise its display property will be set to "block".

window.UTILS.show = function( el, t ) {

	// get current style
	let currentStyle = window.getComputedStyle( el );
	
	// timing 
	if ( t === "slow" ) {
		t = 600;
	} else if ( t === "fast" ) {
		t = 200;
	} else if ( typeof( t ) !== "number" ) {
		t = 400;
	}

	// set appropriate display property
	if( currentStyle.display === "none" ) {
		// if element remembers it display property it had before element.fadeOut(), restore it, otherwise set it to "block"
		if( el.dataset.display_before_fade !== "none" ){
			el.style.display = el.dataset.display_before_fade;
		} else {
			el.style.display = "block";
		}
	}

	// fade in + animate height
	if( currentStyle.opacity < 1 ){
		let fadeInAnimation = el.animate( [ { opacity: 0, height: 0}, { opacity: 1} ], { duration: t } );
		fadeInAnimation.addEventListener( "finish", () => {
			el.style.opacity = 1;
			// restore original overflow if stored, otherwise set it to "auto"
			if( el.dataset.overflowy_before_fade ) {
				el.style.overflowY = el.dataset.overflowy_before_fade;
			} else {
				el.style.overflowY = "auto";
			}
		} );
	}
};

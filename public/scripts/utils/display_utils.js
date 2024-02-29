
/**
 * Replacement methods for some widely used jQuery methods
 * They extend HTMLElement class
 * 
 * Usage:
 * document.querySelector( "p.test" ).fadeOut();
 * 
 * fadeOut, fadeIn timing (same as in jQuery): <milliseconds>|"slow"|"fast"; default: 400ms, slow: 600ms, fast: 200ms (same as in jQuery) 
 * 
 */


// Fade out - replacement to jQuery.fadeOut();

HTMLElement.prototype.fadeOut = function( t ) {

	// timing
	if ( t === "slow" ) {
		t = 600;
	} else if ( t === "fast" ) {
		t = 200;
	} else if ( typeof( t ) !== "number" ) {
		t = 400;
	}

	// remember element`s display property - to be restored by elemet.fadeIn()
	if( window.getComputedStyle( this ).display !== "none" ) {
		this.dataset.display_before_fade = window.getComputedStyle( this ).display;
	}

	// fade out
	let fadeOutAnimation = this.animate( { opacity: 0}, { duration: t } );

	// set display:none after fade complete
	fadeOutAnimation.addEventListener( "finish", () => {
		this.style.display = "none";
		this.style.opacity = 0;
	} );
};

// Fade in - replacement to jQuery.fadeIn(); if element was hidden by element.fadeOut(), its original display mode will be restored, otherwise its display property will be set to "block".

HTMLElement.prototype.fadeIn = function( t ) {

	// get current style
	let currentStyle = window.getComputedStyle( this );
	
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
		if( this.dataset.display_before_fade !== "none" ){
			this.style.display = this.dataset.display_before_fade;
		} else {
			this.style.display = "block";
		}
	}

	// fade in 
	if( currentStyle.opacity < 1 ){
		let fadeInAnimation = this.animate( [ { opacity: 0}, { opacity: 1} ], { duration: t } );
		fadeInAnimation.addEventListener( "finish", () => {
			this.style.opacity = 1;
			// restore original overflow if stored, otherwise set it to "auto"
			if( this.dataset.overflowy_before_fade ) {
				this.style.overflowY = this.dataset.overflowy_before_fade;
			} else {
				this.style.overflowY = "auto";
			}
		} );
	}
};

// Hide - replacement to jQuery.fadeOut();

HTMLElement.prototype.hide = function( t ) {

	// timing
	if ( t === "slow" ) {
		t = 600;
	} else if ( t === "fast" ) {
		t = 200;
	} else if ( typeof( t ) !== "number" ) {
		t = 400;
	}

	// remember element`s display property - to be restored by elemet.fadeIn()
	if( window.getComputedStyle( this ).display !== "none" ) {
		this.dataset.display_before_fade = window.getComputedStyle( this ).display;
	}
	this.dataset.overflowy_before_fade = window.getComputedStyle( this ).overflowY;

	// fade out, height to 0
	this.style.overflowY = "hidden";
	let fadeOutAnimation = this.animate( { opacity: 0, height: 0}, { duration: t } );

	// set display:none after fade complete
	fadeOutAnimation.addEventListener( "finish", () => {
		this.style.display = "none";
		this.style.opacity = 0;
	} );
};

// Show - replacement to jQuery.show(); if element was hidden by element.fadeOut() or element.hide, its original display mode will be restored, otherwise its display property will be set to "block".

HTMLElement.prototype.show = function( t ) {

	// get current style
	let currentStyle = window.getComputedStyle( this );
	
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
		if( this.dataset.display_before_fade !== "none" ){
			this.style.display = this.dataset.display_before_fade;
		} else {
			this.style.display = "block";
		}
	}

	// fade in + animate height
	if( currentStyle.opacity < 1 ){
		let fadeInAnimation = this.animate( [ { opacity: 0, height: 0}, { opacity: 1} ], { duration: t } );
		fadeInAnimation.addEventListener( "finish", () => {
			this.style.opacity = 1;
			// restore original overflow if stored, otherwise set it to "auto"
			if( this.dataset.overflowy_before_fade ) {
				this.style.overflowY = this.dataset.overflowy_before_fade;
			} else {
				this.style.overflowY = "auto";
			}
		} );
	}
};
import 'swiper';
window.UTILS = window.UTILS || { };

window.UTILS.initSwiper = function() {

	var $ = window.jQuery;

	$( ".swiper" ).each( function( index, container ) {
		var $container = $( container );

		// Acquire some parameters from HTML data attributes.
		var slidesPerView = $container.data( "slides_per_view" );
		var loop = $container.data( "loop" );
		var autoplay = $container.data( "autoplay" );
		var sliderId = $container.data( "slider_id" );
		var breakpoint = $container.data( "breakpoint" );
		var centeredSlides = $container.data( "centered_slides" );
		var thumbsFor = $container.data( "thumbsfor" );
		var thumbs = $container.data( "thumbs" );
		var spaceBetween = $container.data( "spacebetween" );
		console.log( "thumbsFor", thumbsFor );

		if( typeof( autoplay ) === "number" ){
			autoplay = {
				delay: autoplay,
			};
		}

		// Swiper init parameters
		var initObject = {
			slidesPerView: slidesPerView,
			navigation: {
				nextEl: "#swiper_button_next_" + sliderId,
				prevEl: "#swiper_button_prev_" + sliderId,
			},
			pagination: {
				el: "#swiper_pagination_" + sliderId,
				clickable: true,
			},
			loop: loop,
			autoplay: autoplay,
			speed: 600,
			roundLengths: false,
			watchOverflow: true,
			spaceBetween: spaceBetween,
		};

		// More Swiper init params for some specific layouts
		if( !spaceBetween ) {
			initObject.spaceBetween = 0;
		} else {
			initObject.spaceBetween = spaceBetween;
		}
	
		if ( slidesPerView === "auto" ) {
			if( !spaceBetween ){
				initObject.spaceBetween = 10;
			}

			// One slide per view on small viewports, auto on screen width > breakpoint
			if ( typeof( breakpoint ) === "number" ) {
				initObject.slidesPerView = 1;
				initObject.breakpoints = {};
				initObject.breakpoints[breakpoint] = {
					slidesPerView: "auto",
				};
			}
		} else {
			if ( slidesPerView === 6 ){
				initObject.breakpoints = {
					1100: {
						slidesPerView: 6,
						slidesPerGroup: 6,
					},
					600: {
						slidesPerView: 4,
						slidesPerGroup: 4,
					},
					400: {
						slidesPerView: 3,
						slidesPerGroup: 3,
					},
					10: {
						slidesPerView: 2,
						slidesPerGroup: 2,
					}
				};
			} else if ( slidesPerView > 1 ){
				initObject.breakpoints = {
					976: {
						slidesPerView: 2,
						slidesPerGroup: 2,
					},
					740: {
						slidesPerView: 1,
						slidesPerGroup: 1,
					}
				};
			}
		}
		if ( centeredSlides ) {
			initObject.centeredSlides = centeredSlides;

			// Workaround for buggy behaviour when centeredSlides=true and slidesPerView=auto
			initObject.on = {
				imagesReady: function() {
					this.slideToLoop( 0, 0 );
					if( autoplay ) {
						this.autoplay.start();
					}
				}
			};
		}
		if ( thumbs ) {
			initObject.thumbs = { swiper: document.querySelector( thumbs ).swiper };
		}

		// eslint-disable-next-line
		var swiper = new Swiper( container, initObject );

	} );
};
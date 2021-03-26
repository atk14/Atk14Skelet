window.UTILS = window.UTILS || { };

window.UTILS.initSwiper = function() {

	var $ = window.jQuery;

	$( ".swiper-container" ).each( function( index, container ) {
		var $container = $( container );
		var slidesPerView = $container.data( "slides_per_view" );
		var loop = $container.data( "loop" );
		var autoplay = $container.data( "autoplay" );
		var sliderId = $container.data( "slider_id" );
		var breakpoint = $container.data( "breakpoint" );
		var centeredSlides = $container.data( "centered_slides" );
		var thumbsFor = $container.data( "thumbsfor" );
		var thumbs = $container.data( "thumbs" );
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
			spaceBetween: 0,
		};

		// More Swiper init params for some specific layouts
		if ( slidesPerView === "auto" ) {
			initObject.spaceBetween = 10;

			// One slide per view on small viewports, auto on screen width > breakpoint
			if ( typeof( breakpoint ) === "number" ) {
				initObject.slidesPerView = 1;
				initObject.breakpoints = {};
				initObject.breakpoints[breakpoint] = {
					slidesPerView: "auto",
				};
			}
		} else {
			if( slidesPerView > 1 ){
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
		if ( thumbsFor ) {
			initObject.watchSlidesVisibility = true;
			initObject.watchSlidesProgress = true;
			initObject.freeMode = true;
		}
		if ( thumbs ) {
			initObject.thumbs = { swiper: document.querySelector( thumbs ).swiper };
		}

		// eslint-disable-next-line
		var swiper = new Swiper( container, initObject );
		
		if ( thumbsFor ) {
			var thumbsTarget = document.querySelector( thumbsFor ).swiper;
			thumbsTarget.params.thumbs = { swiper: swiper };
			thumbsTarget.init();
			//console.log( document.querySelector( thumbsFor ).swiper.params );
			console.log( thumbsTarget.params.thumbs );
		}
		
	} );
};
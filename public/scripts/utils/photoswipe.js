window.UTILS = window.UTILS || { };

// Toto vychazi z http://photoswipe.com/documentation/getting-started.html
// upraveno pro mene striktni html strukturu
// z Example on CodePen (First gallery, Second gallery)
window.UTILS.initPhotoSwipeFromDOM = function( gallerySelector ) {


		var $ = window.jQuery;

		// Parse slide data (url, title, size ...) from DOM elements
		// (children of gallerySelector)
		var parseThumbnailElements = function( el ) {
				var thumbElements = $( el ).find( "figure" ),
						numNodes = thumbElements.length,
						items = [],
						figureEl,
						linkEl,
						size,
						item;

				for ( var i = 0; i < numNodes; i++ ) {

						figureEl = $( thumbElements[ i ] ); // <figure> element

						linkEl = figureEl.find( "a[data-size]" ); // <a> element

						size = linkEl.attr( "data-size" ).split( "x" );

						// Create slide object
						item = {
								src: linkEl.attr( "href" ),
								w: parseInt( size[ 0 ], 10 ),
								h: parseInt( size[ 1 ], 10 )
						};

						if ( figureEl.find( "figcaption" ).length > 0 ) {

								// <figcaption> content
								item.title = figureEl.find( "figcaption" ).html();
								item.description = "description";
						}
						
						if ( linkEl.data( "minithumb" ) ) {

							// Thumbnail url for zooming - little with same aspect ratio as big image
							// Used in cases where displayed thumb has different asp. ratio than big image
							item.msrc = linkEl.data( "minithumb" );
						}
						else if ( linkEl.find( "img" ).length > 0 ) {

								// <img> thumbnail element, retrieving thumbnail url
								item.msrc = linkEl.find( "img" ).attr( "src" );
						}

						// Save link to element for getThumbBoundsFn
						item.el = figureEl[ 0 ];
						items.push( item );
				}

				return items;
		};

		// Triggers when user clicks on thumbnail
		var onThumbnailClick = function( e ) {
				e = e || window.event;
	
				// OLD e.preventDefault ? e.preventDefault() : e.returnValue = false;
				e.preventDefault();

				var clickedListItem = $( this );

				// Find index of clicked item by looping through all child nodes
				// alternatively, you may define index via data- attribute
				var clickedGallery = clickedListItem.closest( gallerySelector ),
						childNodes = clickedGallery.find( "figure" ),
						numChildNodes = childNodes.length,
						index;

				for ( var i = 0; i < numChildNodes; i++ ) {
						if ( childNodes[ i ] === clickedListItem[ 0 ] ) {
								index = i;
								break;
						}
				}

				if ( index >= 0 ) {

						// Open PhotoSwipe if valid index found
						openPhotoSwipe( index, clickedGallery[ 0 ] );
				}

				return false;
		};

		// Parse picture index and gallery index from URL (#&pid=1&gid=2)
		var photoswipeParseHash = function() {
				var hash = window.location.hash.substring( 1 ),
				params = {};

				if ( hash.length < 5 ) {
						return params;
				}

				var vars = hash.split( "&" );
				for ( var i = 0; i < vars.length; i++ ) {
						if ( !vars[ i ] ) {
								continue;
						}
						var pair = vars[ i ].split( "=" );
						if ( pair.length < 2 ) {
								continue;
						}
						params[ pair[ 0 ] ] = pair[ 1 ];
				}

				if ( params.gid ) {
						params.gid = parseInt( params.gid, 10 );
				}

				return params;
		};

		var openPhotoSwipe = function( index, galleryElement, disableAnimation, fromURL ) {
				var PhotoSwipe = window.PhotoSwipe,
					PhotoSwipeUIDefault = window.PhotoSwipeUI_Default,
					pswpElement = document.querySelectorAll( ".pswp" )[ 0 ],
					gallery,
					options,
					items;

				items = parseThumbnailElements( galleryElement );

				// Define options (if needed)
				options = {

						// Define gallery index (for URL)
						galleryUID: galleryElement.getAttribute( "data-pswp-uid" ),

						getThumbBoundsFn: function( index ) {

								// See Options -> getThumbBoundsFn section
								// of documentation for more info
								var thumbnail =
										items[ index ].el.getElementsByTagName( "img" )[ 0 ],
										pageYScroll = window.pageYOffset ||
											document.documentElement.scrollTop,
										rect = thumbnail.getBoundingClientRect();

								return { x:rect.left, y:rect.top + pageYScroll, w:rect.width };
						},

						showHideOpacity:true,
						//showAnimationDuration:0

				};

				// PhotoSwipe opened from URL
				if ( fromURL ) {
						if ( options.galleryPIDs ) {

								// Parse real index when custom PIDs are used
								// http://photoswipe.com/documentation/faq.html#custom-pid-in-url
								for ( var j = 0; j < items.length; j++ ) {
										if ( items[ j ].pid === index ) {
												options.index = j;
												break;
										}
								}
						} else {

								// In URL indexes start from 1
								options.index = parseInt( index, 10 ) - 1;
						}
				} else {
						options.index = parseInt( index, 10 );
				}

				// Exit if index not found
				if ( isNaN( options.index ) ) {
						return;
				}

				if ( disableAnimation ) {
						options.showAnimationDuration = 0;
				}

				// Pass data to PhotoSwipe and initialize it
				// TODO: Ty uvozovky kolem PhotoSwipe ??
				gallery = new PhotoSwipe( pswpElement, PhotoSwipeUIDefault,
					items, options );
				gallery.init();
		};

		// Loop through all gallery elements and bind events
		// var galleryElements = document.querySelectorAll( gallerySelector );
		var galleryElements = $( gallerySelector );

		$.each( galleryElements, function( i, el ) {
			$( el ).attr( "data-pswp-uid", i + 1 );
			$( el ).find( "figure" ).on( "click", onThumbnailClick );
		} );

		// Parse URL and open gallery if it contains #&pid=3&gid=1
		var hashData = photoswipeParseHash();
		if ( hashData.pid && hashData.gid ) {
				openPhotoSwipe( hashData.pid, galleryElements[ hashData.gid - 1 ], true, true );
		}

};

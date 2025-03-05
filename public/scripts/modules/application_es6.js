
//import PhotoSwipeInitializer from "./photoswipe_initializer";
import SwiperInitializer from "./swiper_initializer";


//new PhotoSwipeInitializer();
new SwiperInitializer();

import PhotoswipeLoader from "./photoswipe_loader.js";

// Check if Photoswipe is needed on the page and if so, load it
if( document.querySelector( ".gallery__images, .iobject--picture" ||  document.querySelector( ".js_gallery_trigger" )) ) {
  PhotoswipeLoader.load();
} else {
  // console.info( "No photoswipe needed on this page" );
}
// Uncomment next line if you want to expose Photoswipe outside of this module, i.e. if you need to load it manualy
// Object.assign( globalThis, { PhotoswipeLoader } );

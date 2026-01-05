
/**
 * Main JS module for the application. It is used mainly for loading other JS modules.
 */
import SwiperLoader from "./swiperloader.js";



// Swiper
// Initialize Swiper in markdown preview areas when they are updated
window.addEventListener( "markdownPreviewUpdated", function( e ) {
  if( e.detail.element.querySelector( ".swiper" ) ) {
    SwiperLoader.load().then( () => {
      let selector = "[class='" + e.detail.parent + "'] .md-preview .swiper";
      SwiperLoader.initSwiper( selector );
    } );
  }
} );
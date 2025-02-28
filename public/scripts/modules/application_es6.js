

/**
 * Class for loading Photoswipe library and setting up gallery triggers
 * All methods are static
 * Usage: 
 * PhotoswipeLoader.load();
 * This is usually run automatically when Photoswipe is needed on the page - see code below class definition
 */
class PhotoswipeLoader {
  static isLoaded = false;
  constructor() {

  }

  /**
   * Load Photoswipe library
   * @returns {Promise<void>}
   */
  static async load() {

    // if Photoswipe already loaded, do nothing
    if( this.isLoaded ) {
      console.info( "Photoswipe already loaded" );
      return;
    }

    try {
      // Dynamic import of PhotoswipeLightbox module - loads only if needed
      const m = await import ( "/public/dist/scripts/modules/photoswipe-lightbox.esm.min.js" );
      const PhotoSwipeLightbox = m.default;
  
      const lightbox = new PhotoSwipeLightbox( {
        gallery: ".gallery__images, .iobject--picture",
        children: "figure a[data-pswp-width]",
        pswpModule: () => import( "/public/dist/scripts/modules/photoswipe.esm.min.js" )
      } );
  
      // Get image titles and captions
      lightbox.on( "uiRegister", function() {
        lightbox.pswp.ui.registerElement( {
          name: "custom-caption",
          order: 9,
          isButton: false,
          appendTo: "root",
          html: "",
          // eslint-disable-next-line no-unused-vars
          onInit: ( el, pswp ) => {
            lightbox.pswp.on( "change", () => {
              const currSlideElement = lightbox.pswp.currSlide.data.element;
              const parentFigure = currSlideElement.closest( "figure" );
              const figcaption = parentFigure.querySelector( "figcaption" );
              let captionHTML = "";
              if (currSlideElement) {
                // get caption from alt attribute
                // captionHTML = currSlideElement.querySelector('img').getAttribute('alt');
                // Get caption from figcaption tag
                if( figcaption ) {
                  captionHTML = figcaption.innerHTML;
                }
              }
              el.innerHTML = captionHTML || "";
            } );
          }
        } );
      } );
  
      lightbox.init();

      // remember that Photoswipe is loaded
      this.isLoaded = true;

      // Set handlers for gallery / images / other triggers click
      this.setGalleryHandler();

    } catch (error) {
      console.error( "Error loading Photoswipe", error);
    }
  }

  /**
   * Set handlers for gallery / images / other triggers click
   */
  static setGalleryHandler() {
    // console.log( "is Photoswipe loaded?", this.isLoaded );
    // Trigger gallery from other links 
    // Used in gallery on product with variants
    // and in swiper slider gallery to prevent issues with duplicated slides
    var galleryTriggers = document.querySelectorAll( ".js_gallery_trigger" );

    galleryTriggers.forEach( function( el ) {
      el.addEventListener( "click", function( e ) {

        var triggerElement =  e.currentTarget;

        // Get image ID 
        var imageID = triggerElement.querySelector( "a[data-preview_for]" ).dataset.preview_for;
        
        // Find corresponding Photoswipe enabled image link
        if( triggerElement.closest( ".product-gallery" ) ) {
          // in gallery for product with variants
          var galleryLink = triggerElement.closest( ".product-gallery" ).querySelector( ".gallery__item[data-id=\"" + imageID + "\"] a" );
        } else {
          // other galleries
          var galleryLink = triggerElement.closest( ".gallery__images" ).querySelector( "figure[data-gallery_item_id=\"" + imageID + "\"] a");
        }

        // Trigger click on photoswipe-enabled link
        if( galleryLink ) {
          galleryLink.click();
        }

        e.preventDefault();
      } );
    } );

    // Trigger photoswipe from link in sinle picture iObject figcaption
    var pictureTriggers = document.querySelectorAll( ".js_picture_trigger" );

    pictureTriggers.forEach( function( el ) {
      el.addEventListener( "click", function( e ) {
        var imageLink = e.currentTarget.closest( "figure" ).querySelector( "a" );
        if(imageLink){
          imageLink.click();
        }
        e.preventDefault();
      } );
    } );
  }
}

// Check if Photoswipe is needed on the page and if so, load it
if( document.querySelector( ".gallery__images, .iobject--picture" ||  document.querySelector( ".js_gallery_trigger" )) ) {
  PhotoswipeLoader.load();
} else {
  // console.info( "No photoswipe needed on this page" );
}
// Uncomment next line if you want to expose Photoswipe outside of this module, i.e. if you need to load it manualy
// Object.assign( globalThis, { PhotoswipeLoader } );
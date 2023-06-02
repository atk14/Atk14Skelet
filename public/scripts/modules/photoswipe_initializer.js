import PhotoSwipeLightbox from "photoswipe/lightbox";

let PhotoSwipeInitializer = function() {
  // Initialize PhotoSwipe
  const lightbox = new PhotoSwipeLightbox({
    gallery: ".gallery__images, .iobject--picture",
    children: "figure a[data-pswp-width]",
    pswpModule: () => import("/public/dist/scripts/modules/photoswipe.esm.min.js")
  });

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
        });
      }
    });
  });

  lightbox.init();

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
};

export default PhotoSwipeInitializer;
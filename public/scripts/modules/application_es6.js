import PhotoSwipeLightbox from "photoswipe/lightbox";
//import Swiper from "swiper";
import Swiper, { Autoplay, EffectFade, FreeMode, Navigation, Pagination, Thumbs } from 'swiper';
Swiper.use([Navigation, Pagination, Autoplay, FreeMode, EffectFade, Thumbs]);


// Photoswipe 

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

// Swiper 

var swipers = document.querySelectorAll( ".swiper" );
swipers.forEach( function( container ) {

  // Acquire some parameters from HTML data attributes.
  var slidesPerView = container.dataset.slides_per_view;
  var loop = container.dataset.loop;
  var autoplay = container.dataset.autoplay;
  var sliderId = container.dataset.slider_id;
  var breakpoint = container.dataset.breakpoint;
  var centeredSlides = container.dataset.centered_slides;
  var thumbs = container.dataset.thumbs;
  var spaceBetween = container.dataset.spacebetween;

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

  new Swiper( container, initObject );

} );
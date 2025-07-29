/**
 * Class for loading Swiper library and setting up sliders
 * All methods are static
 * Usage: 
 * SwiperLoader.load();
 */
export default class SwiperLoader {
  static isLoaded = false;
  static Swiper = null;
  static swiperModules= null;
  constructor() {
  }

  static async load() {
    // if Swiper already loaded, do nothing
    if( this.isLoaded ) {
      console.info( "Swiper already loaded" );
      return;
    }

    try {
      const swiperModule = await import( "swiper" );
      const { Navigation, Pagination, Autoplay, Thumbs } = await import( "swiper/modules" );

      this.Swiper = swiperModule.default;// || swiperModule.Swiper;
      this.Swiper.use([Navigation, Pagination, Autoplay, Thumbs]);
      this.swiperModules = { Navigation, Pagination, Autoplay, Thumbs };
  
      // remember that Swiper is loaded
      this.isLoaded = true;

      this.initSwiper();
      
    } catch (error) {
      console.error( "Error loading Swiper", error);
    }
  }

  static initSwiper() {
    if( !this.isLoaded ) {
      console.error( "Swiper not loaded, run SwiperLoader.load() first" );
      return;
    }
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
        //modules: [ Navigation, Pagination, Autoplay, Thumbs ],
        modules: this.swiperModules,
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
      //console.log(initObject);

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
      new this.Swiper( container, initObject );

    }.bind( this ) );
  }
}
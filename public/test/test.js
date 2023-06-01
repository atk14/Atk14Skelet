import * as bootstrap from 'bootstrap';
window.$ = window.jQuery = require('jquery');

( function( window, $, undefined ) {
  console.log("test");
  console.log("bootstrap", bootstrap);
  console.log("jQuery", $);
} )( window, window.jQuery );
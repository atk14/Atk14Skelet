/*
 * Notifications class for displaying notifications
 * 
 * Displays notification in bottom right corner of viewport
 * Usage: 
 * window.UTILS.Notifications.show( {message: "message text"}, { delay: 2000, type:"success" } )
 * 
 * Bootstrap 5 version
 *  
 */

import { Toast } from "bootstrap";

window.UTILS = window.UTILS || { };

window.UTILS.Notifications = class {
  static toastCounter = 0;

  static show( message, options ) {

    // Default options
    options = {
      delay: null,
      type: "info",
      ...options
    };

    let container;
    // Create container if it does not exist yet
    if( !document.querySelector( ".notifications_container" ) ) {
      container = document.createElement( "div" );
      container.classList.add( "notifications_container" );
      document.querySelector( "body" ).appendChild( container );
    } else {
      container = document.querySelector( ".notifications_container" );
    }

    let toastID = "toast_" + this.toastCounter;
    let autohide = false;
    let delay = 0;

    // Set autohide delay 
    if( options.delay ){
      autohide = true;
      delay = options.delay;
    }

    // Bootstrap 5
    // Create toast HTML
    let toastTemplate = `
    <div class="toast align-items-center toast--${options.type}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="${autohide}" data-bs-delay="${delay}" id="${toastID}">
      <div class="d-flex">
        <div class="toast-body">
        ${message.message}
        </div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
    `;

    // Append toast to container
    container.insertAdjacentHTML( "beforeend", toastTemplate.trim() );

    // Coonnect to Bootstrap javascript
    let tsel = document.getElementById( toastID )
    let ts = new Toast( tsel );
    ts.show();

    tsel.addEventListener( "hidden.bs.toast", function ( e ) {
      Toast.getInstance( e.target ).dispose();
      e.target.remove();
    } );

    this.toastCounter++;
  }
  
};
/*
 * Notifications class for displaying notifications
 * 
 * Displays notification in bottom right corner of viewport
 * Usage: 
 * window.UTILS.Notifications.show( {message: "message text"}, { delay: 2000, type:"success" } )
 * 
 * TODO: Bootstrap 5 compatibility 
 */
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

    // Create toast HTML
    let toastTemplate = `
    <div role="alert" aria-live="assertive" aria-atomic="true" class="toast toast--notitle toast--${options.type}" data-autohide="${autohide}" data-delay="${delay}" id="${toastID}">
      <div class="toast-header">
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body">
        ${message.message}
      </div>
    </div>
    `;

    // Append toast to container
    container.insertAdjacentHTML( "beforeend", toastTemplate.trim() );

    // Connect to Bootstrap 4 javascript toast handlers
    let $ = window.jQuery;
    let $ts = $( "#" + toastID )
    $ts.toast( "show" );
    $ts.on( "hidden.bs.toast", function(){
      console.log( "hidden", this );
      $( this ).toast( "dispose" );
      $( this ).remove();
    } )

    this.toastCounter++;
  }
  
};
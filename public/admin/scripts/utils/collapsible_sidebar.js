/**
 * Collapsible sidebar for admin interface.
 *
 * Usage:
 *   new UTILS.CollapsibleSidebar();
 *
 * The state of the sidebar (collapsed/expanded) is stored in sessionStorage.
 * Thanks to naming the window (window.name) uniquely, each tab/window has its own state even if LocalStorage would be used instead of SessionStorage.
 */
window.UTILS = window.UTILS || { };

window.UTILS.CollapsibleSidebar = class {

  #sidebarOpen = true;
  storageName;

  constructor() {

    // Unique storage key for the current window/tab
    window.name = window.name || "name_" + Math.round(  Math.random() * 1000000 );
    this.storageName = "admin_sidebarOpen_" + window.name;

    // Restore sidebar state from sessionStorage
    this.restoreSidebarState();

    // Set event handlers for sidebar toggles
    this.setToggleHandlers();

  }

  /**
   * Getters and setters for sidebarOpen property
   *  Setting sidebarOpen to true opens the sidebar (removes "sidebar-collapsed" class from root html element (=documentElement))
   *  @param {boolean} state
   */
  set sidebarOpen( state ) {
    this.#sidebarOpen = state;
    if ( state && document.documentElement.classList.contains( "sidebar-collapsed" ) ) {
      document.documentElement.classList.remove( "sidebar-collapsed" );
    } else if ( !state ){
      document.documentElement.classList.add( "sidebar-collapsed" );
    }
    this.saveSidebarState();
  }

  /**
   * Get the current state of the sidebar (open/closed)
   * @returns {boolean}
   */
  get sidebarOpen() {
    return this.#sidebarOpen;
  }

  /**
   * Set event handlers for sidebar toggle buttons
   */
  setToggleHandlers() {
    let togglers = document.querySelectorAll( ".js--sidebar-toggle" );
    [...togglers].forEach( ( toggler ) => {
      toggler.addEventListener( "click", function( e ) {
        e.preventDefault();
        this.sidebarOpen = !this.sidebarOpen;
      }.bind( this ) );
    } );
  }

  /**
   * Save the current state of the sidebar to sessionStorage
   */
  saveSidebarState() {
    sessionStorage.setItem( this.storageName, this.sidebarOpen ? true : false );
  }

  /**
   * Restore the state of the sidebar from sessionStorage
   */
  restoreSidebarState() {
    if( sessionStorage.getItem( this.storageName ) !== null ) {
      this.sidebarOpen = sessionStorage.getItem( this.storageName ) === "true";
      setTimeout( () => {
        document.querySelector( ".has-nav-section" ).classList.add( "with-animation" );
      }, 100 );
    }
  }
};
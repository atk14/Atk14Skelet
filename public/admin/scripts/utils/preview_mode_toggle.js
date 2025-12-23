window.UTILS = window.UTILS || { };

/**
 * PreviewModeToggle class
 *
 * Provides a toolbar to toggle between different preview modes (desktop, tablet, phone)
 * for markdown previews in the admin interface.
 * Usage:
 * window.UTILS.PreviewModeToggle.init( el );
 * 
 */

window.UTILS.PreviewModeToggle = class {

  // Markup for the preview mode toggle toolbar
  static toolbarMarkup = `<div class="preview-mode-toggler">
    <div class="btn-group" role="group" aria-label="Preview viewport toggle">
      <button class="btn btn-outline-light active" data-mode="desktop" title="Desktop Preview">
        <i class="fas fa-desktop"></i>
      </button>
      <button class="btn btn-outline-light" data-mode="tablet" title="Tablet Preview">
        <i class="fas fa-tablet-alt"></i>
      </button>
      <button class="btn btn-outline-light" data-mode="phone" title="Phone Preview">
        <i class="fas fa-mobile-alt"></i>
      </button>
    </div>
  </div>`;
  

  constructor() {
    console.log("PreviewModeToggle initialized");
  }

  // Initialize the preview mode toggle for a given element
  static init( previewContainer ) {
    previewContainer.insertAdjacentHTML( "afterbegin", this.toolbarMarkup );
    [...previewContainer.querySelectorAll( ".preview-mode-toggler .btn" )].forEach( button => {
      button.addEventListener( "click",  e => {
        e.preventDefault();
        let mode = e.currentTarget.getAttribute( "data-mode" );
        this.changeMode( previewContainer, mode );
      } );
    } );
  }

  // Change the preview mode to the specified mode
  static changeMode( previewContainer,mode ) {
    let modes = [ "desktop", "tablet", "phone" ];
    modes.forEach( m => {
      previewContainer.querySelector( ".md-preview__viewport" ).classList.remove( "preview--" + m );
      previewContainer.querySelector( ".preview-mode-toggler .btn[data-mode='" + m + "']" ).classList.remove( "active" );
    } );
    previewContainer.querySelector( ".md-preview__viewport" ).classList.add( "preview--" + mode );
    previewContainer.querySelector( ".preview-mode-toggler .btn[data-mode='" + mode + "']" ).classList.add( "active" );
  }

};
/**
 * Helper class for adding/removing buttons to the markdown editor toolbar.
 * Configuration object:
 * {}
 */


window.UTILS = window.UTILS || { };

window.UTILS.MDEditorToolbarHelper = class {
  constructor() {
    // nothing here so far
  }
  static addToolbarButton( configObject ) {
    console.log( "Adding toolbar button: ", configObject );
    let toolbars = window.UTILS.MDEditorToolbarHelper.findMDEditorToolbars();
    console.log( "Found toolbars: ", toolbars );
    toolbars.forEach( toolbar => {
      if ( toolbar.querySelector( "[data-btnname='" + configObject.name + "']" ) ) {
        return;
      }
      let button = window.UTILS.MDEditorToolbarHelper.createButton(
        configObject.name,
        configObject.text,
        configObject.title,
        configObject.className,
        configObject.hasModal,
        configObject.modalId,
        configObject.onClick
      );
      toolbar.appendChild( button );
    } );
  }
  static removeToolbarButton( buttonName ) {
    let toolbars = window.UTILS.MDEditorToolbarHelper.findMDEditorToolbars();
    toolbars.forEach( toolbar => {
      let btn = toolbar.querySelector( "[data-btnname='" + buttonName + "']" );
      if ( !btn ) {
        return;
      }
      ( btn.closest( ".button-group" ) || btn ).remove();
    } );
  }

  static createButton( name, text, title, className, hasModal, modalId, onClick ) {
    console.log( "Creating toolbar button: ", name, text, title, className, hasModal, modalId, onClick );
    let btn = document.createElement( "button" );
    btn.type = "button";
    btn.className = "md-btn btn btn-default md-btn--icon";
    if ( className ) {
      btn.className += " " + className;
    }
    btn.title = title;
    btn.innerHTML = text;
    btn.dataset.btnname = name;

    if ( hasModal ) {
      btn.setAttribute ( "data-bs-toggle", "modal" );
      btn.setAttribute ( "data-toggle", "modal" );
      btn.setAttribute ( "data-bs-target", modalId );
      btn.setAttribute ( "data-target", modalId );
    } else {
      // other btn actions than triggering modal can be added here
    }

    if ( onClick ) {
      btn.addEventListener( "click", onClick );
    }

    let div = document.createElement( "div" );
    div.className = "button-group";
    div.appendChild( btn );
    return div;
  }

  static findMDEditorToolbars() {
    let toolbars = [...document.querySelectorAll( ".md-container .md-toolbar .btn-toolbar" )]
    return toolbars;
  }
}
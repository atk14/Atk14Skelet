/**
 * Helper class for adding/removing buttons to/from the toolbar(s) of every
 * markdown editor (SimpleMDE/EasyMDE-style ".md-container") present on the page.
 *
 * Usage:
 *   window.UTILS.MDEditorToolbarHelper.addToolbarButton( {
 *     name: "fa_iconpicker",                              // unique id, stored as data-btnname
 *     text: "<i class=\"fa-solid fa-icons\"></i> Icons",  // button innerHTML
 *     title: "Icons",                                     // button title/tooltip
 *     className: "",                                      // extra CSS class(es), optional
 *     hasModal: true,                                      // wire up Bootstrap modal triggering
 *     modalId: "#fa_iconpicker_modal",                     // modal target selector, when hasModal
 *     onClick: () => { ... },                              // click handler, optional
 *   } );
 *
 *   window.UTILS.MDEditorToolbarHelper.removeToolbarButton( "fa_iconpicker" );
 */


window.UTILS = window.UTILS || { };

window.UTILS.MDEditorToolbarHelper = class {
  constructor() {
    // nothing here so far
  }

  /**
   * Adds a button to every MD editor toolbar currently in the page.
   * Skips a toolbar that already has a button with the same data-btnname
   * (so it's safe to call again, e.g. after a form is replaced).
   * @param {Object} configObject - { name, text, title, className, hasModal, modalId, onClick }
   */
  static addToolbarButton( configObject ) {
    console.log( "Adding toolbar button: ", configObject );
    let toolbars = window.UTILS.MDEditorToolbarHelper.findMDEditorToolbars();
    console.log( "Found toolbars: ", toolbars );
    toolbars.forEach( toolbar => {
      if ( toolbar.querySelector( "[data-btnname='" + configObject.name + "']" ) ) {
        return;
      }
      // a new button must be created per toolbar; a single shared DOM node
      // would be moved (not copied) by each appendChild, ending up in only one toolbar
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

  /**
   * Removes the button with the given name from every MD editor toolbar it's found in.
   * @param {String} buttonName - value matching the button's data-btnname
   */
  static removeToolbarButton( buttonName ) {
    let toolbars = window.UTILS.MDEditorToolbarHelper.findMDEditorToolbars();
    toolbars.forEach( toolbar => {
      let btn = toolbar.querySelector( "[data-btnname='" + buttonName + "']" );
      if ( !btn ) {
        return;
      }
      // remove the wrapping .button-group created by createButton(), not just the <button>
      ( btn.closest( ".button-group" ) || btn ).remove();
    } );
  }

  /**
   * Builds a single toolbar button, wrapped in a ".button-group" div
   * (matching the markup of the editor's own built-in toolbar buttons).
   * @param {String} name - unique id, stored as data-btnname (used for lookup/removal)
   * @param {String} text - button innerHTML (e.g. an <i> icon plus label)
   * @param {String} title - button title/tooltip
   * @param {String} className - extra CSS class(es) to append, optional
   * @param {Boolean} hasModal - when true, wires up Bootstrap 4/5 data attributes to open modalId
   * @param {String} modalId - modal target selector (e.g. "#my_modal"), required when hasModal
   * @param {Function} onClick - click handler, optional
   * @returns {Element} the ".button-group" div containing the button
   */
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
      // both data-bs-* (Bootstrap 5) and data-* (Bootstrap 4) attributes are set
      // so the button works regardless of which Bootstrap version is loaded
      btn.setAttribute ( "data-bs-toggle", "modal" );
      btn.setAttribute ( "data-toggle", "modal" );
      btn.setAttribute ( "data-bs-target", modalId );
      btn.setAttribute ( "data-target", modalId );
    }

    if ( onClick ) {
      btn.addEventListener( "click", onClick );
    }

    let div = document.createElement( "div" );
    div.className = "button-group";
    div.appendChild( btn );
    return div;
  }

  /**
   * Finds the toolbar element of every MD editor currently in the page.
   * @returns {Element[]}
   */
  static findMDEditorToolbars() {
    let toolbars = [...document.querySelectorAll( ".md-container .md-toolbar .btn-toolbar" )]
    return toolbars;
  }
}
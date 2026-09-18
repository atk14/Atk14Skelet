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
 *
 *   window.UTILS.MDEditorToolbarHelper.addToolbarDropdownMenu( {
 *     name: "demo_dropdown", text: "Menu", title: "Demo menu", className: "",
 *   } );
 *   window.UTILS.MDEditorToolbarHelper.addToolbarDropdownItem( "demo_dropdown", {
 *     name: "icons", text: "Icons", title: "Icons", className: "",
 *     hasModal: false, modalId: null, onClick: () => { ... },
 *   } );
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
   * Adds a dropdown menu to every MD editor toolbar currently in the page.
   * Skips a toolbar that already has a dropdown (or button) with the same data-btnname
   * (so it's safe to call again, e.g. after a form is replaced).
   * Add items to it afterwards via addToolbarDropdownItem().
   * @param {Object} configObject - { name, text, title, className }
   */
  static addToolbarDropdownMenu( configObject ) {
    console.log( "Adding toolbar dropdown menu: ", configObject );
    let toolbars = window.UTILS.MDEditorToolbarHelper.findMDEditorToolbars();
    console.log( "Found toolbars: ", toolbars );
    toolbars.forEach( toolbar => {
      if ( toolbar.querySelector( "[data-btnname='" + configObject.name + "']" ) ) {
        return;
      }
      // a new dropdown must be created per toolbar; a single shared DOM node
      // would be moved (not copied) by each appendChild, ending up in only one toolbar
      let dropdown = window.UTILS.MDEditorToolbarHelper.createDropdownMenu(
        configObject.name,
        configObject.text,
        configObject.title,
        configObject.className
      );
      toolbar.appendChild( dropdown );
    } );
  }

  /**
   * Adds an item to a dropdown menu (previously added via addToolbarDropdownMenu())
   * in every MD editor toolbar it's found in.
   * Skips a dropdown that already has an item with the same data-btnname
   * (so it's safe to call again, e.g. after a form is replaced).
   * @param {String} dropdownName - value matching the dropdown toggle's data-btnname
   * @param {Object} configObject - { name, text, title, className, hasModal, modalId, onClick }
   */
  static addToolbarDropdownItem( dropdownName, configObject ) {
    console.log( "Adding toolbar dropdown item: ", dropdownName, configObject );
    let toolbars = window.UTILS.MDEditorToolbarHelper.findMDEditorToolbars();
    toolbars.forEach( toolbar => {
      let toggle = toolbar.querySelector( "[data-btnname='" + dropdownName + "']" );
      if ( !toggle ) {
        return;
      }
      let dropdownMenu = toggle.closest( ".button-group" );
      if ( !dropdownMenu || dropdownMenu.querySelector( "[data-btnname='" + configObject.name + "']" ) ) {
        return;
      }
      window.UTILS.MDEditorToolbarHelper.createDropdownMenuItem(
        dropdownMenu,
        configObject.name,
        configObject.text,
        configObject.title,
        configObject.className,
        configObject.hasModal,
        configObject.modalId,
        configObject.onClick
      );
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

  /**
   * Builds a dropdown toggle button with an (initially empty) menu, wrapped
   * in a ".button-group.btn-group" div (Bootstrap needs "btn-group" on the
   * immediate parent of ".dropdown-toggle" + ".dropdown-menu" for positioning).
   * Populate the menu via createDropdownMenuItem().
   * @param {String} name - unique id, stored as data-btnname (used for lookup/removal)
   * @param {String} text - toggle button innerHTML (e.g. an <i> icon plus label)
   * @param {String} title - toggle button title/tooltip
   * @param {String} className - extra CSS class(es) to append to the toggle button, optional
   * @returns {Element} the ".button-group" div containing the toggle button and the menu
   */
  static createDropdownMenu( name, text, title, className ) {
    let btn = document.createElement( "button" );
    btn.type = "button";
    btn.className = "md-btn btn btn-default md-btn--icon dropdown-toggle";
    if ( className ) {
      btn.className += " " + className;
    }
    btn.title = title;
    btn.innerHTML = text;
    btn.dataset.btnname = name;
    // both data-bs-* (Bootstrap 5) and data-* (Bootstrap 4) attributes are set
    // so the toggle works regardless of which Bootstrap version is loaded
    btn.setAttribute ( "data-bs-toggle", "dropdown" );
    btn.setAttribute ( "data-toggle", "dropdown" );
    btn.setAttribute ( "aria-haspopup", "true" );
    btn.setAttribute ( "aria-expanded", "false" );

    let menu = document.createElement( "ul" );
    menu.className = "dropdown-menu";

    let div = document.createElement( "div" );
    div.className = "button-group btn-group";
    div.appendChild( btn );
    div.appendChild( menu );
    return div;
  }

  /**
   * Adds an item to a dropdown menu created by createDropdownMenu().
   * @param {Element} dropdownMenu - the ".button-group" div returned by createDropdownMenu()
   * @param {String} name - unique id (within the dropdown), stored as data-btnname
   * @param {String} text - item innerHTML
   * @param {String} title - item title/tooltip
   * @param {String} className - extra CSS class(es) to append to the item, optional
   * @param {Boolean} hasModal - when true, wires up Bootstrap 4/5 data attributes to open modalId
   * @param {String} modalId - modal target selector (e.g. "#my_modal"), required when hasModal
   * @param {Function} onClick - click handler, optional
   * @returns {Element} the <li> wrapping the item
   */
  static createDropdownMenuItem( dropdownMenu, name, text, title, className, hasModal, modalId, onClick ) {
    let item = document.createElement( "button" );
    item.type = "button";
    item.className = "dropdown-item";
    if ( className ) {
      item.className += " " + className;
    }
    item.title = title;
    item.innerHTML = text;
    item.dataset.btnname = name;

    if ( hasModal ) {
      item.setAttribute ( "data-bs-toggle", "modal" );
      item.setAttribute ( "data-toggle", "modal" );
      item.setAttribute ( "data-bs-target", modalId );
      item.setAttribute ( "data-target", modalId );
    }

    if ( onClick ) {
      item.addEventListener( "click", onClick );
    }

    let li = document.createElement( "li" );
    li.appendChild( item );

    dropdownMenu.querySelector( ".dropdown-menu" ).appendChild( li );
    return li;
  }
}
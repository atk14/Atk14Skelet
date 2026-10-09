/**
 * Registry of things insertable into MD editors (tabs, link lists...), usually built in a modal by a designer.
 * Every item is registered once and appears in:
 *
 * - the "Insert" dropdown of every MD editor toolbar: the code is inserted at the cursor
 *   ("Code" view), into the edited block or as a new block (block view, see md_block_editor.js),
 * - the "Add block" menu of the block view (items with inline: false),
 * - the block view's edit button of a shortcode block the item can edit (items with shortcode and parse).
 *
 * Usage:
 *
 *   UTILS.MDEditorInserts.register( {
 *     name: "tabs",                                 // unique id
 *     text: "<span class=\"fas fa-folder\"></span> Tabs", // menu item innerHTML
 *     title: "Tabs",
 *     modal: "#tabdesigner_modal",                  // shown by run(), closing it without saving cancels
 *     inline: false,                                // true: a piece of text (e.g. an icon), not a block
 *     shortcode: "tabs",                            // optional, blocks of this shortcode can be edited...
 *     parse: raw => model || null,                  // ...when parse() understands them
 *     open: ( { raw, save } ) => { ... save( code ); }, // fills the modal; raw is null when inserting
 *   } );
 *
 *   UTILS.MDEditorInserts.run( item, { raw: null, onSave: code => { ... }, onCancel: () => { ... } } );
 *
 * Dependencies: md_editor_toolbar_helper.js, Bootstrap 4 or 5 (modals), Ace (via bootstrap-markdown-editor)
 */
window.UTILS = window.UTILS || { };

window.UTILS.MDEditorInserts = class {

  static registeredItems = [];

  /**
   * Registers an item (replaces an item of the same name) and adds it to the "Insert" dropdown
   * of every MD editor toolbar. Safe to call again, e.g. after a form is replaced.
   * @param {Object} item
   */
  static register( item ) {
    let items = window.UTILS.MDEditorInserts.registeredItems;
    let i = items.findIndex( it => it.name === item.name );
    if ( i >= 0 ) {
      items[ i ] = item;
    } else {
      items.push( item );
    }
    window.UTILS.MDEditorToolbarHelper.addToolbarDropdownItem( "insert_dropdown", {
      name: item.name,
      text: item.text,
      title: item.title,
      className: "",
      onClick: e => {
        window.UTILS.MDEditorInserts.insertInto( e.currentTarget.closest( ".md-container" ), item.name );
      },
    } );
  }

  static items() {
    return window.UTILS.MDEditorInserts.registeredItems.slice();
  }

  static get( name ) {
    return window.UTILS.MDEditorInserts.registeredItems.find( item => item.name === name ) || null;
  }

  /**
   * An item able to edit the given shortcode block
   * @param {String} shortcode - e.g. "tabs"
   * @param {String} raw - source of the block
   * @returns {Object|null}
   */
  static editorFor( shortcode, raw ) {
    return window.UTILS.MDEditorInserts.registeredItems.find( item => {
      return item.shortcode === shortcode && item.parse && item.parse( raw );
    } ) || null;
  }

  /**
   * Opens the item's modal; onSave gets the code once the modal is closed (so that the focus is back),
   * closing the modal without saving calls onCancel
   * @param {Object} item
   * @param {Object} options - { raw, onSave, onCancel }
   */
  static run( item, options ) {
    let modal = document.querySelector( item.modal );
    let code = null;
    window.UTILS.MDEditorInserts.onModalHidden( modal, () => {
      if ( code !== null ) {
        options.onSave( code );
      } else if ( options.onCancel ) {
        options.onCancel();
      }
    } );
    item.open( {
      raw: options.raw || null,
      save: c => {
        code = c;
        window.UTILS.MDEditorInserts.hideModal( modal );
      },
    } );
    window.UTILS.MDEditorInserts.showModal( modal );
  }

  /**
   * Inserts the item into the given MD editor: the block view handles it on its own,
   * the "Code" view gets the code at the cursor
   * @param {Element} container - .md-container
   * @param {String} name - item name
   */
  static insertInto( container, name ) {
    let item = window.UTILS.MDEditorInserts.get( name );
    if ( !item ) {
      return;
    }
    let blockEditor = container.mdBlockEditor;
    if ( blockEditor && blockEditor.visible ) {
      blockEditor.insertItem( item );
      return;
    }
    let editor = window.ace.edit( container.querySelector( ".md-editor" ) );
    window.UTILS.MDEditorInserts.run( item, {
      onSave: code => {
        editor.focus();
        editor.insert( item.inline ? code : window.UTILS.MDEditorInserts.asBlock( code, editor ) );
      },
      onCancel: () => {
        editor.focus();
      },
    } );
  }

  /**
   * Surrounds block code with blank lines, unless the cursor is already at the beginning of an empty line
   * @param {String} code
   * @param {Object} editor - Ace
   * @returns {String}
   */
  static asBlock( code, editor ) {
    let pos = editor.getCursorPosition();
    let session = editor.getSession();
    let line = session.getLine( pos.row );
    let before = line.slice( 0, pos.column ).trim() ? "\n\n" : ( pos.row > 0 && session.getLine( pos.row - 1 ).trim() ? "\n" : "" );
    let after = line.slice( pos.column ).trim() || pos.row < session.getLength() - 1 ? "\n\n" : "";
    return before + code + after;
  }

  // Bootstrap 5 has bootstrap.Modal.getOrCreateInstance(), Bootstrap 4 works through jQuery
  static get bootstrap5() {
    return !!( window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance );
  }

  static showModal( modal ) {
    if ( window.UTILS.MDEditorInserts.bootstrap5 ) {
      window.bootstrap.Modal.getOrCreateInstance( modal ).show();
    } else {
      window.jQuery( modal ).modal( "show" );
    }
  }

  static hideModal( modal ) {
    if ( window.UTILS.MDEditorInserts.bootstrap5 ) {
      window.bootstrap.Modal.getOrCreateInstance( modal ).hide();
    } else {
      window.jQuery( modal ).modal( "hide" );
    }
  }

  static onModalHidden( modal, callback ) {
    if ( window.UTILS.MDEditorInserts.bootstrap5 ) {
      modal.addEventListener( "hidden.bs.modal", callback, { once: true } );
    } else {
      window.jQuery( modal ).one( "hidden.bs.modal", callback );
    }
  }
};

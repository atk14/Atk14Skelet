/**
 * The form's save buttons in the toolbar of a Markdown editor shown in the fullscreen
 * ("Save and continue editing", "Save and go back"), and Ctrl+S (Cmd+S) in the editor
 * for "Save and continue editing".
 *
 * The buttons don't save anything themselves: they click the form's own submit buttons
 * (the ones in .button-container, see e.g. app/views/admin/pages/_edit_form.tpl), so the form
 * is submitted as usual (by atk14.js via XHR when it's remote). A block being edited in the block view
 * is committed first. The XHR response keeps the editors (see edit_form_replacer.js),
 * so the editor stays in the fullscreen after "Save and continue editing".
 *
 *   UTILS.MDEditorSaveButtons.init(); // after UTILS.initializeMarkdonEditors(), safe to call again
 */
window.UTILS = window.UTILS || { };

window.UTILS.MDEditorSaveButtons = class {

  static init() {
    let self = window.UTILS.MDEditorSaveButtons;
    [ ...document.querySelectorAll( "form .md-container" ) ].forEach( container => {
      if ( container.mdSaveButtons ) {
        return;
      }
      let buttons = self.formButtons( container );
      let toolbar = container.querySelector( ".md-toolbar .btn-toolbar" );
      if ( !buttons.length || !toolbar ) {
        return;
      }
      container.mdSaveButtons = true;

      let group = document.createElement( "div" );
      group.className = "btn-group md-save-buttons";
      buttons.forEach( ( button, i ) => {
        let copy = document.createElement( "button" );
        copy.type = "button";
        copy.className = "btn btn-sm btn-primary";
        copy.textContent = button.textContent.trim();
        if ( button.name === "save_and_stay" ) {
          copy.title = copy.textContent + " (Ctrl+S)";
        }
        copy.addEventListener( "click", () => self.submit( container, i ) );
        group.appendChild( copy );
      } );
      toolbar.appendChild( group );

      container.addEventListener( "keydown", e => {
        if ( ( e.ctrlKey || e.metaKey ) && !e.altKey && !e.shiftKey && e.key.toLowerCase() === "s" ) {
          e.preventDefault();
          let index = self.formButtons( container ).findIndex( button => button.name === "save_and_stay" );
          if ( index >= 0 ) {
            self.submit( container, index );
          }
        }
      }, true );
    } );
  }

  /**
   * The form's submit buttons; looked up again on every click, the form is replaced after a save
   * @param {Element} container - .md-container
   * @returns {Array}
   */
  static formButtons( container ) {
    let form = container.closest( "form" );
    return form ? [ ...form.querySelectorAll( ".button-container button[type=submit]" ) ] : [];
  }

  static submit( container, index ) {
    let button = window.UTILS.MDEditorSaveButtons.formButtons( container )[ index ];
    if ( !button ) {
      return;
    }
    // a block edited in a block view goes to the source first
    container.closest( "form" ).querySelectorAll( ".md-container" ).forEach( c => {
      if ( c.mdBlockEditor ) {
        c.mdBlockEditor.commitEdit();
      }
    } );
    button.click();
  }
};

/**
 * The form's save buttons in the toolbar of a Markdown editor shown in the fullscreen
 * ("Save and continue editing", "Save and go back"), and Ctrl+S (Cmd+S) in the editor
 * for "Save and continue editing".
 *
 * The buttons don't save anything themselves: they click the form's own submit buttons
 * (the ones in .button-container, see e.g. app/views/admin/pages/_edit_form.tpl), so the form
 * is submitted as usual (by atk14.js via XHR when it's remote). A block being edited in the block view
 * is committed first. The XHR response keeps the editors (see edit_form_replacer.js),
 * so the editor stays in the fullscreen after "Save and continue editing". An edit.xhr.tpl replacing
 * the whole form ($form.replaceWith()) removes the editor; the fullscreen is then cleaned up
 * (see cleanUpFullscreen()), so such forms (e.g. in derived projects) keep working, just without the fullscreen.
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
    let form = container.closest( "form" );
    // a block edited in a block view goes to the source first
    form.querySelectorAll( ".md-container" ).forEach( c => {
      if ( c.mdBlockEditor ) {
        c.mdBlockEditor.commitEdit();
      }
    } );
    if ( container.classList.contains( "md-fullscreen" ) && window.jQuery ) {
      let scrollY = parseFloat( container.dataset.scroll_before_fullscreen );
      // after the response is evaluated; not "ajax:complete" of the form: jQuery's replaceWith() drops the handlers of a replaced form
      window.jQuery( document ).one( "ajaxComplete", () => window.UTILS.MDEditorSaveButtons.cleanUpFullscreen( scrollY ) );
    }
    button.click();
  }

  /**
   * A safeguard for an edit.xhr.tpl replacing the whole form ($form.replaceWith(), not UTILS.EditFormReplacer):
   * the editor in the fullscreen is gone, but the page would stay locked (md-body-fullscreen on body and html);
   * the page is unlocked and scrolled to the first error, or where it was before the fullscreen
   * @param {Number} scrollY
   */
  static cleanUpFullscreen( scrollY ) {
    if ( document.querySelector( ".md-container.md-fullscreen" ) || !document.body.classList.contains( "md-body-fullscreen" ) ) {
      return;
    }
    document.documentElement.classList.remove( "md-body-fullscreen" );
    document.body.classList.remove( "md-body-fullscreen" );
    let error = document.querySelector( "form .form-group--has-error, form .feedback--invalid, form .alert-danger" );
    window.setTimeout( () => {
      if ( error ) {
        ( error.closest( ".form-group" ) || error ).scrollIntoView( { block: "center" } );
      } else if ( !isNaN( scrollY ) ) {
        window.scrollTo( window.scrollX, scrollY );
      }
    }, 50 );
  }
};

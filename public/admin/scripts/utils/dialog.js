/**
 * Native <dialog> helpers for dialogs marked up like Bootstrap 6 dialogs (styles in _dialog.scss):
 *
 *   <dialog class="dialog dialog-lg" id="my_dialog" aria-labelledby="my_dialog_title" closedby="any">
 *     <div class="dialog-header">
 *       <h1 class="dialog-title" id="my_dialog_title">Title</h1>
 *       <button type="button" class="btn-close" commandfor="my_dialog" command="close" aria-label="Close"></button>
 *     </div>
 *     <div class="dialog-body">...</div>
 *     <div class="dialog-footer">
 *       <button type="button" class="btn btn-secondary" commandfor="my_dialog" command="close">Close</button>
 *     </div>
 *   </dialog>
 *
 *   UTILS.Dialog.show( dialog );
 *   UTILS.Dialog.onClose( dialog, () => { ... } ); // once; Esc, a click on the backdrop, command="close" and close() all end here
 *   UTILS.Dialog.close( dialog );
 *
 * No Bootstrap JavaScript is involved. Buttons with commandfor + command ("show-modal", "close", "request-close")
 * work natively (Invoker Commands); in browsers without them a click handler does the same.
 * closedby="any" (a click on the backdrop closes the dialog) gets a fallback too.
 */
window.UTILS = window.UTILS || { };

window.UTILS.Dialog = class {

  static show( dialog ) {
    if ( !dialog.open ) {
      dialog.showModal();
    }
  }

  static close( dialog ) {
    if ( dialog.open ) {
      dialog.close();
    }
  }

  static isOpen( dialog ) {
    return !!dialog.open;
  }

  /**
   * Calls the callback once the dialog is closed (in any way); the focus is already back at that time
   */
  static onClose( dialog, callback ) {
    dialog.addEventListener( "close", callback, { once: true } );
  }

  static get invokerCommandsSupported() {
    return typeof window.HTMLButtonElement !== "undefined" && "command" in window.HTMLButtonElement.prototype;
  }

  static get closedbySupported() {
    return typeof window.HTMLDialogElement !== "undefined" && "closedBy" in window.HTMLDialogElement.prototype;
  }

  /**
   * Fallbacks for browsers without Invoker Commands or closedby; called once when the script is loaded
   */
  static initFallbacks() {
    if ( !window.UTILS.Dialog.invokerCommandsSupported ) {
      document.addEventListener( "click", e => {
        let button = e.target.closest && e.target.closest( "button[commandfor][command]" );
        let dialog = button && document.getElementById( button.getAttribute( "commandfor" ) );
        if ( !dialog || dialog.tagName !== "DIALOG" || button.disabled ) {
          return;
        }
        let command = button.getAttribute( "command" );
        if ( command === "show-modal" ) {
          window.UTILS.Dialog.show( dialog );
        } else if ( command === "close" || command === "request-close" ) {
          window.UTILS.Dialog.close( dialog );
        }
      } );
    }
    if ( !window.UTILS.Dialog.closedbySupported ) {
      // a click on the backdrop targets the dialog itself (its content covers the rest of it)
      document.addEventListener( "click", e => {
        let dialog = e.target;
        if ( dialog.tagName !== "DIALOG" || !dialog.open || dialog.getAttribute( "closedby" ) !== "any" ) {
          return;
        }
        let r = dialog.getBoundingClientRect();
        let inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
        if ( !inside ) {
          window.UTILS.Dialog.close( dialog );
        }
      } );
    }
  }
};

window.UTILS.Dialog.initFallbacks();

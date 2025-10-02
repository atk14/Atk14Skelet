/**
 * MD Editor Resizer
 * Object for handling resizing of markdown editors.
 * It remembers the heights of editors in sessionStorage
 * and restores them when the form is reloaded.
 * If user resizes an MD editor, its height is restored after reload.
 * TODO: It should also restore height after MD editor goes fullscreen and back.
 * 
 * Usage:
 *   new UTILS.MDEditorResizer();
 */

window.UTILS = window.UTILS || { };

window.UTILS.MDEditorResizer = class {
  recordId = null;
  controller = ""
  storageName = ""


  constructor() {
    
    console.log( "MDEditorResizer initialized" );
    
    // Set storage record key based on controller and record ID
    const params = new URLSearchParams(window.location.search);
    this.recordId = params.get( "id" );
    this.controller = document.body.dataset.controller;
    this.storageName = "md_editors_" + this.controller + "_" + this.recordId

    // Restore heights of editors from sessionStorage
    this.restoreHeights();
    // Assign resize handlers to all editors on the page
    this.assignResizeHandlers();
    // Start MutationObserver to detect when form is loaded via AJAX
    this.startMutationObserver();

  }

  // Assign resize handlers to all editors on the page
  assignResizeHandlers() {
    let editors = document.querySelectorAll( ".md-editor" );
    [...editors].forEach( ( editor ) => {
     this.makeResizable( editor );
    } );
  }

  /**
   * assign event handler for editor resize (mouseup event)
   * @param {*} editor 
   */
  makeResizable( editor ) {
    if ( editor.dataset.height_handler ) {
      return;
    }
    // Mark editor as having assigned resize handler
    editor.dataset.height_handler = true;
    // Assign event handler
    editor.addEventListener( "mouseup", () => {
      this.onEditorResize();
    } );
  }

  /**
   * Handler for editor resize event
   */
  onEditorResize() {
    this.storeHeights();
  }

  /**
   * Start MutationObserver to detect when form is loaded via AJAX
   * and re-assign resize handlers to editors in the form
   * and restore their heights from sessionStorage
   * If ajax reloading of form would emit some event, we could use it instead of MutationObserver
   */
  startMutationObserver() {
    // MutationObserver for detecting changes in DOM - namely when form was loaded via AJAX
    const observer = new MutationObserver( ( mutations ) => {    
      mutations.forEach( ( mutation ) => {
        if ( mutation.addedNodes.length > 0 ) {
          this.onDOMMutation();
        }
      });
    });

    // Start observing .content-main for childList changes
    observer.observe( document.querySelector( ".content-main" ), {
      childList: true,
      subtree: false
    } );
  }

  onDOMMutation() {
    this.assignResizeHandlers();
    this.restoreHeights();
  }

  /**
   * Store heights of all editors to sessionStorage
   */
  storeHeights() {
    let editors = document.querySelectorAll( ".md-editor" );
    let storageObject = {
      editors: new Array()
    };
    [...editors].forEach( ( editor ) => {
      let editorId = editor.closest( ".form-group" ).querySelector( "textarea.form-control[id]" ).getAttribute( "id" );
      storageObject.editors.push( { id: editorId, height: editor.offsetHeight } );
    } );
    sessionStorage.setItem( this.storageName, JSON.stringify( storageObject ) );
  }

  /**
   * Restore heights of all editors from sessionStorage
   */
  restoreHeights() {
    if( sessionStorage.getItem( this.storageName ) ){
      let storageObject = JSON.parse( sessionStorage.getItem( this.storageName ) );
      if ( storageObject && storageObject.editors ) {
        storageObject.editors.forEach( ( item ) => {
          console.log( "Restoring height", item.id, item.height );
          //let textarea = document.getElementById( item.id );
          let formgrup = document.getElementById( item.id )?.closest( ".form-group" );
          let editor = formgrup?.querySelector( ".md-editor" );
          if ( editor ) {
            editor.style.height = item.height + "px";
          }
        } );
      }
    }
  }



};
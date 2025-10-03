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
  controller = "";
  storageName = "";
  observer = null;


  constructor() {
        
    // Set storage record key based on controller and record ID
    const params = new URLSearchParams(window.location.search);
    this.recordId = params.get( "id" );
    this.controller = document.body.dataset.controller;
    this.storageName = "md_editors_" + this.controller + "_" + this.recordId

    
    // Assign resize handlers to all editors on the page
    this.assignResizeHandlers();
    // Start MutationObserver to detect when form is loaded via AJAX
    this.startMutationObserver();
    // Restore heights of editors from sessionStorage
    this.restoreHeights();
    // Observe .content-main for childList changes - this indicates that form was reloaded via AJAX
    this.observeFormReloads();
    // Observe fullscreen changes
    this.observeFullscreenChanges();

  }

  /**
   * Assign resize handlers to all editors on the page
   */
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
    this.observer = new MutationObserver( ( mutations ) => {    
      mutations.forEach( ( mutation ) => {
        //console.log( "mutation", mutation.type);
        if (mutation.type === "childList") {
          if ( mutation.addedNodes.length > 0 ) {
            this.onDOMMutation();
          }
        } else if ( mutation.type === "attributes" ) {
          //console.log( "Attributes changed", mutation.target, mutation.attributeName, mutation.oldValue );
          if ( mutation.target.classList.contains( "md-container" ) ) {
            // An MD editor had its attributes changed - possibly it went fullscreen or exited fullscreen
            //this.onDOMMutation();
            //const oldClasses = mutation.oldValue ? mutation.oldValue.split( " " ) : [];
            //const newClasses = [...mutation.target.classList];

            if ( !mutation.target.classList.contains( "md-fullscreen" ) ) {
              //console.log( "An MD editor exited fullscreen" );
              this.restoreHeights();
            }
          }
        }
      });
    });    
  }

  /**
   * Observe .content-main for childList changes - this indicates that form was reloaded via AJAX`
   */
  observeFormReloads() {
    this.observer.observe( document.querySelector( ".content-main" ), {
      childList: true,
      subtree: false
    } );
  }

  /**
   * Observe MD editors for fullscreen changes
   */
  observeFullscreenChanges(){
    let editors = document.querySelectorAll( ".md-container" );
    [...editors].forEach( ( editor ) => {
      if ( editor.dataset.fullscreen_handler ) {
        return;
      }
      // Mark editor as having assigned fullscreen handler
      editor.dataset.fullscreen_handler = true;
      // Observe attribute changes on each editor to detect fullscreen changes);
      this.observer.observe( editor, {
        attributes: true,
        attributeFilter: ["class"],
        subtree: false,
        childList: false,
      } );
    } );
  }

  /**
   * Handler for DOM mutations detected by MutationObserver
   */
  onDOMMutation() {
    this.assignResizeHandlers();
    this.restoreHeights();
    this.observeFullscreenChanges();
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
          //console.log( "Restoring height", item.id, item.height );
          //let textarea = document.getElementById( item.id );
          let formgrup = document.getElementById( item.id )?.closest( ".form-group" );
          let editor = formgrup?.querySelector( ".md-editor" );
          if ( editor ) {
            editor.style.height = item.height + "px";
          }
        } );
      }
      // this.observeFullscreenChanges();
    }
  }

};
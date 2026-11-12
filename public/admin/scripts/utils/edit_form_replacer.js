/**
 * Replaces an edit form by its new HTML (the response of an XHR "Save and continue editing")
 * keeping the Markdown editors as they are: their textareas and editors (.md-container) stay in the page,
 * only everything around them is replaced. So an editor keeps its view (blocks, code, preview),
 * the selected block, the cursor, the undo history and the fullscreen.
 *
 *   UTILS.EditFormReplacer.replace( $form[ 0 ], html );
 *   window.dispatchEvent( new Event( "edit_form_replaced" ) ); // initializes the new fields
 *
 * The new HTML is merged into the form: the elements containing a kept editor are kept (their attributes
 * are updated), their other children are replaced by the new ones. A kept editor's textarea gets the classes
 * of the new one (e.g. is-invalid), its value stays.
 *
 * When the form has errors and an editor is shown in the fullscreen, the fullscreen is left and the page
 * is scrolled to the first error, unless all the errors are of the editor's own field.
 */
window.UTILS = window.UTILS || { };

window.UTILS.EditFormReplacer = class {

  // a textarea with a Markdown editor (bootstrap-markdown-editor puts the .md-container after it)
  static editorTextareas( root ) {
    return [ ...root.querySelectorAll( "textarea[name]" ) ].filter( textarea => {
      let next = textarea.nextElementSibling;
      return next && next.classList.contains( "md-container" );
    } );
  }

  /**
   * @param {Element} form - the form in the page
   * @param {String} html - the new form
   * @returns {Element} the form in the page (the same element when merged, a new one otherwise)
   */
  static replace( form, html ) {
    let self = window.UTILS.EditFormReplacer;
    let template = document.createElement( "template" );
    template.innerHTML = html.trim();
    let newForm = template.content.firstElementChild;
    if ( !newForm || newForm.tagName !== "FORM" || template.content.children.length !== 1 ) {
      form.replaceWith( template.content );
      return null;
    }

    // the kept nodes; in the new form, their places are marked
    let kept = [];
    self.editorTextareas( form ).forEach( textarea => {
      let newTextarea = [ ...newForm.querySelectorAll( "textarea[name]" ) ].find( t => t.name === textarea.name );
      if ( !newTextarea ) {
        return;
      }
      let container = textarea.nextElementSibling;
      textarea.className = newTextarea.className;
      textarea.classList.add( "md-textarea-hidden" );
      [ textarea, container ].forEach( ( node, i ) => {
        let marker = document.createElement( "md-keep" );
        marker.dataset.index = kept.length;
        if ( i === 0 ) {
          newTextarea.replaceWith( marker );
        } else {
          kept[ kept.length - 1 ].marker.after( marker );
        }
        kept.push( { node: node, marker: marker } );
      } );
    } );

    if ( !kept.length ) {
      form.replaceWith( newForm );
      return newForm;
    }
    let fullscreen = form.querySelector( ".md-container.md-fullscreen" );
    self.merge( form, newForm, kept );
    self.leaveFullscreenOnErrors( form, fullscreen );
    return form;
  }

  /**
   * Merges the new element into the old one (both containing the kept nodes)
   * @param {Element} old
   * @param {Element} nu
   * @param {Array} kept - [ { node, marker } ]
   */
  static merge( old, nu, kept ) {
    let self = window.UTILS.EditFormReplacer;
    [ ...old.attributes ].forEach( attr => {
      if ( !nu.hasAttribute( attr.name ) ) {
        old.removeAttribute( attr.name );
      }
    } );
    [ ...nu.attributes ].forEach( attr => {
      if ( old.getAttribute( attr.name ) !== attr.value ) {
        old.setAttribute( attr.name, attr.value );
      }
    } );

    let markersIn = node => node.nodeType === 1 ? ( node.tagName === "MD-KEEP" ? [ node ] : [ ...node.querySelectorAll( "md-keep" ) ] ) : [];
    let children = [ ...nu.childNodes ].map( child => {
      let markers = markersIn( child );
      if ( !markers.length ) {
        return child;
      }
      let first = kept[ markers[ 0 ].dataset.index ];
      if ( child === first.marker ) {
        return first.node;
      }
      // the old element holding the kept node
      let oldChild = [ ...old.children ].find( c => c.contains( first.node ) );
      if ( oldChild && oldChild.tagName === child.tagName && oldChild !== first.node ) {
        self.merge( oldChild, child, kept );
        return oldChild;
      }
      // another structure: the kept nodes are moved into the new one
      markers.forEach( marker => marker.replaceWith( kept[ marker.dataset.index ].node ) );
      return child;
    } );

    // the old children not kept are removed, the new ones put in their places
    [ ...old.childNodes ].forEach( child => {
      if ( !children.includes( child ) ) {
        child.remove();
      }
    } );
    children.forEach( ( child, i ) => {
      if ( old.childNodes[ i ] !== child ) {
        old.insertBefore( child, old.childNodes[ i ] || null );
      }
    } );
  }

  /**
   * Errors of other fields can't be seen in the fullscreen: it's left and the first error is shown
   * @param {Element} form
   * @param {Element|null} container - the editor in the fullscreen
   */
  static leaveFullscreenOnErrors( form, container ) {
    if ( !container || !container.classList.contains( "md-fullscreen" ) ) {
      return;
    }
    let group = container.closest( ".form-group" ) || container.parentElement;
    let errors = [ ...form.querySelectorAll( ".form-group--has-error, .feedback--invalid, .alert-danger, .alert-error" ) ];
    let error = errors.find( e => !group.contains( e ) );
    if ( !error ) {
      return;
    }
    let button = container.querySelector( ".md-toolbar [data-btn='fullscreen']" );
    if ( button ) {
      button.click();
    }
    window.setTimeout( () => {
      ( error.closest( ".form-group" ) || error ).scrollIntoView( { block: "center" } );
    }, 50 );
  }
};

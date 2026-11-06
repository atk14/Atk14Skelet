/**
 * Inline style editor
 * Edits the value of a style attribute as a list of declarations, one per row (property, value, remove button),
 * a new one is added by the "+" button. Used by designers of the Markdown editor (e.g. the div editor),
 * in their dialogs as well as in the inspector of the block view.
 *
 * Usage:
 *
 *   let editor = new UTILS.InlineStyleEditor( container, {
 *     value: "color: red; margin: 0 auto;",
 *     onChange: style => { ... },              // called on every change of the resulting value
 *   } );
 *   editor.getValue(); // "color: red; margin: 0 auto;" (the original string when nothing changed)
 *   editor.setValue( "padding: 1rem;" );
 *
 *   UTILS.InlineStyleEditor.parse( "color: red; background: url(\"a;b.png\")" );
 *   // [ { property: "color", value: "red" }, { property: "background", value: "url(\"a;b.png\")" } ]
 *
 * Declarations are split by ";" outside parentheses and quotes, so url(data:...;base64,...) is kept whole.
 * A row without a property or a value is left out of the result. Parsing and serializing don't need DOM (testable in Node.js).
 *
 * Dependencies:
 *
 * _inline_style_editor.scss
 * app/views/admin/shared/editor_modals/_inline_style_editor_texts.tpl (window.inlineStyleEditorTexts)
 */
( function( root ) {

  class InlineStyleEditor {

    /**
     * Splits a style attribute into declarations
     * @param {String} style
     * @returns {Array} [ { property, value } ]
     */
    static parse( style ) {
      let declarations = [];
      let push = text => {
        text = text.trim();
        if ( !text ) {
          return;
        }
        let i = text.indexOf( ":" ); // a property name contains no ":", the first one ends it
        declarations.push( i < 0 ? { property: text, value: "" } : { property: text.slice( 0, i ).trim(), value: text.slice( i + 1 ).trim() } );
      };
      let buffer = "";
      let depth = 0;
      let quote = null;
      style = style || "";
      for ( let i = 0; i < style.length; i++ ) {
        let ch = style[ i ];
        if ( quote ) {
          buffer += ch;
          if ( ch === "\\" && i + 1 < style.length ) {
            buffer += style[ ++i ];
          } else if ( ch === quote ) {
            quote = null;
          }
          continue;
        }
        if ( ch === "\"" || ch === "'" ) {
          quote = ch;
        } else if ( ch === "(" ) {
          depth++;
        } else if ( ch === ")" ) {
          depth = Math.max( 0, depth - 1 );
        } else if ( ch === ";" && depth === 0 ) {
          push( buffer );
          buffer = "";
          continue;
        }
        buffer += ch;
      }
      push( buffer );
      return declarations;
    }

    /**
     * Builds the value of a style attribute; incomplete declarations (no property or no value) are left out
     * @param {Array} declarations - [ { property, value } ]
     * @returns {String} e.g. "color: red; margin: 0 auto;"
     */
    static serialize( declarations ) {
      return declarations
        .map( d => ( { property: ( d.property || "" ).trim(), value: ( d.value || "" ).trim() } ) )
        .filter( d => d.property && d.value )
        .map( d => d.property + ": " + d.value + ";" )
        .join( " " );
    }

    /**
     * True when both styles have the same declarations (whitespace and the trailing ";" don't matter)
     */
    static same( a, b ) {
      return InlineStyleEditor.serialize( InlineStyleEditor.parse( a ) ) === InlineStyleEditor.serialize( InlineStyleEditor.parse( b ) );
    }

    // UI strings (rendered in template), with English fallbacks
    static texts() {
      return Object.assign( {
        add: "Add declaration",
        remove: "Remove",
        property: "property",
        value: "value",
        invalid: "The browser doesn't understand this declaration",
      }, root.inlineStyleEditorTexts );
    }

    // Names of CSS properties offered while typing (one <datalist> for the whole page)
    static propertiesListId() {
      let id = "inline_style_editor_properties";
      if ( !document.getElementById( id ) ) {
        let names = [];
        let computed = window.getComputedStyle( document.documentElement );
        for ( let i = 0; i < computed.length; i++ ) {
          if ( computed[ i ].indexOf( "-webkit-" ) !== 0 ) {
            names.push( computed[ i ] );
          }
        }
        let list = document.createElement( "datalist" );
        list.id = id;
        names.sort().forEach( name => {
          let option = document.createElement( "option" );
          option.value = name;
          list.appendChild( option );
        } );
        document.body.appendChild( list );
      }
      return id;
    }

    /**
     * @param {Element} container - the editor is rendered into it
     * @param {Object} options - { value, onChange }
     */
    constructor( container, options ) {
      options = options || {};
      this.container = container;
      this.onChange = options.onChange || null;
      this.texts = InlineStyleEditor.texts();
      this.original = "";

      container.classList.add( "inline-style-editor" );
      this.list = document.createElement( "div" );
      this.list.className = "inline-style-editor__rows";
      this.addButton = document.createElement( "button" );
      this.addButton.type = "button";
      this.addButton.className = "btn btn-sm btn-outline-secondary inline-style-editor__add";
      this.addButton.innerHTML = "<span class=\"fa-solid fa-plus\"></span> ";
      this.addButton.appendChild( document.createTextNode( this.texts.add ) );
      container.appendChild( this.list );
      container.appendChild( this.addButton );

      this.addButton.addEventListener( "click", () => {
        this.addRow( { property: "", value: "" } ).querySelector( ".js--property" ).focus();
      } );
      this.list.addEventListener( "input", e => {
        this.markValidity( e.target.closest( ".inline-style-editor__row" ) );
        this.changed();
      } );
      this.list.addEventListener( "click", e => {
        let remove = e.target.closest( ".js--remove" );
        if ( remove ) {
          this.removeRow( remove.closest( ".inline-style-editor__row" ) );
        }
      } );
      this.list.addEventListener( "keydown", e => this.onKeyDown( e ) );

      this.setValue( options.value || "" );
    }

    /**
     * Fills the editor; doesn't call onChange
     * @param {String} style
     */
    setValue( style ) {
      this.original = style || "";
      this.list.innerHTML = "";
      InlineStyleEditor.parse( this.original ).forEach( d => this.addRow( d ) );
      this.lastValue = this.getValue();
    }

    /**
     * The value of the style attribute; the original string when the declarations didn't change
     * @returns {String}
     */
    getValue() {
      let value = InlineStyleEditor.serialize( this.declarations() );
      return InlineStyleEditor.same( value, this.original ) ? this.original : value;
    }

    declarations() {
      return [ ...this.list.querySelectorAll( ".inline-style-editor__row" ) ].map( row => ( {
        property: row.querySelector( ".js--property" ).value,
        value: row.querySelector( ".js--value" ).value,
      } ) );
    }

    addRow( declaration, before ) {
      let row = document.createElement( "div" );
      row.className = "inline-style-editor__row";
      let property = document.createElement( "input" );
      property.type = "text";
      property.className = "form-control form-control-sm js--property";
      property.placeholder = this.texts.property;
      property.setAttribute( "aria-label", this.texts.property );
      property.setAttribute( "list", InlineStyleEditor.propertiesListId() );
      property.autocomplete = "off";
      property.spellcheck = false;
      property.value = declaration.property;
      let colon = document.createElement( "span" );
      colon.className = "inline-style-editor__colon";
      colon.textContent = ":";
      let value = document.createElement( "input" );
      value.type = "text";
      value.className = "form-control form-control-sm js--value";
      value.placeholder = this.texts.value;
      value.setAttribute( "aria-label", this.texts.value );
      value.autocomplete = "off";
      value.spellcheck = false;
      value.value = declaration.value;
      let remove = document.createElement( "button" );
      remove.type = "button";
      remove.className = "btn btn-sm inline-style-editor__remove js--remove";
      remove.title = this.texts.remove;
      remove.setAttribute( "aria-label", this.texts.remove );
      remove.innerHTML = "<span class=\"fa-solid fa-xmark\"></span>";
      row.appendChild( property );
      row.appendChild( colon );
      row.appendChild( value );
      row.appendChild( remove );
      this.list.insertBefore( row, before || null );
      this.markValidity( row );
      return row;
    }

    removeRow( row ) {
      // the focus goes to the next row (or the previous one, or the "+" button), not to the page
      let next = row.nextElementSibling || row.previousElementSibling;
      row.remove();
      ( next ? next.querySelector( ".js--value" ) : this.addButton ).focus();
      this.changed();
    }

    // A complete declaration the browser doesn't understand is marked (it's kept anyway)
    markValidity( row ) {
      if ( !row ) {
        return;
      }
      let property = row.querySelector( ".js--property" ).value.trim();
      let value = row.querySelector( ".js--value" ).value.trim();
      let invalid = !!( property && value && window.CSS && window.CSS.supports && !window.CSS.supports( property, value ) );
      row.classList.toggle( "inline-style-editor__row--invalid", invalid );
      row.title = invalid ? this.texts.invalid : "";
    }

    changed() {
      let value = this.getValue();
      if ( value === this.lastValue ) {
        return; // e.g. typing into a row without a value yet
      }
      this.lastValue = value;
      if ( this.onChange ) {
        this.onChange( value );
      }
    }

    /**
     * Enter in the property goes to its value; Enter in the value goes to the next row, or adds one after the last row;
     * ":" typed in the property goes to the value too
     */
    onKeyDown( e ) {
      let row = e.target.closest( ".inline-style-editor__row" );
      if ( !row ) {
        return;
      }
      let isProperty = e.target.classList.contains( "js--property" );
      if ( isProperty && e.key === ":" ) {
        e.preventDefault();
        row.querySelector( ".js--value" ).focus();
      } else if ( e.key === "Enter" && isProperty ) {
        e.preventDefault();
        row.querySelector( ".js--value" ).focus();
      } else if ( e.key === "Enter" && e.target.classList.contains( "js--value" ) ) {
        e.preventDefault();
        let next = row.nextElementSibling || ( row.querySelector( ".js--property" ).value.trim() ? this.addRow( { property: "", value: "" } ) : null );
        if ( next ) {
          next.querySelector( ".js--property" ).focus();
        }
      }
    }
  }

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = InlineStyleEditor;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.InlineStyleEditor = InlineStyleEditor;
  }

} )( typeof window !== "undefined" ? window : this );

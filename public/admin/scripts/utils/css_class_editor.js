/**
 * CSS class editor
 * Edits the value of a class attribute as chips: a class is added by typing it and pressing Enter, Space or the "+" button
 * (pasted "a b c" makes three chips), removed by its "×" (or Delete / Backspace on a focused chip),
 * and moved by dragging (SortableJS, when loaded) or by Alt+Left / Alt+Right on a focused chip.
 * The order of the classes is kept as it is, a class is there only once.
 * Used by designers of the Markdown editor (e.g. the div editor), in their dialogs as well as in the inspector of the block view;
 * the same interface as UTILS.InlineStyleEditor.
 *
 * Usage:
 *
 *   let editor = new UTILS.CssClassEditor( container, {
 *     value: "card p-3 mx-2",
 *     onChange: value => { ... },   // called on every change of the resulting value
 *   } );
 *   editor.getValue(); // "card p-3 mx-2" (the original string when the classes didn't change)
 *   editor.setValue( "text-center" );
 *
 * A class being typed counts too (it's in the value before it becomes a chip), so nothing typed gets lost.
 * Enter consumed by the editor is marked by preventDefault(): a dialog can save on Enter when !e.defaultPrevented.
 * Suggestions are not offered yet; they would be attached to the input (.js--input).
 * Parsing and serializing don't need DOM (testable in Node.js).
 *
 * Dependencies:
 *
 * _css_class_editor.scss
 * app/views/admin/shared/editor_modals/_css_class_editor_texts.tpl (window.cssClassEditorTexts)
 * SortableJS (optional, dragging chips)
 */
( function( root ) {

  class CssClassEditor {

    /**
     * Classes of a class attribute, in their order, each one once
     * @param {String} value
     * @returns {Array}
     */
    static parse( value ) {
      let classes = [];
      ( value || "" ).split( /\s+/ ).forEach( name => {
        if ( name && classes.indexOf( name ) < 0 ) {
          classes.push( name );
        }
      } );
      return classes;
    }

    static serialize( classes ) {
      return CssClassEditor.parse( classes.join( " " ) ).join( " " );
    }

    /**
     * True when both values have the same classes in the same order (whitespace and duplicates don't matter)
     */
    static same( a, b ) {
      return CssClassEditor.serialize( CssClassEditor.parse( a ) ) === CssClassEditor.serialize( CssClassEditor.parse( b ) );
    }

    // UI strings (rendered in template), with English fallbacks
    static texts() {
      return Object.assign( {
        add: "Add class",
        placeholder: "class",
        remove: "Remove",
        chipTitle: "Drag to change the order (or Alt+Left / Alt+Right), Delete removes",
      }, root.cssClassEditorTexts );
    }

    /**
     * @param {Element} container - the editor is rendered into it
     * @param {Object} options - { value, onChange }
     */
    constructor( container, options ) {
      options = options || {};
      this.container = container;
      this.onChange = options.onChange || null;
      this.texts = CssClassEditor.texts();
      this.original = "";

      container.classList.add( "css-class-editor" );
      this.chips = document.createElement( "div" );
      this.chips.className = "css-class-editor__chips";

      let entry = document.createElement( "div" );
      entry.className = "css-class-editor__entry";
      this.input = document.createElement( "input" );
      this.input.type = "text";
      this.input.className = "form-control form-control-sm js--input";
      this.input.placeholder = this.texts.placeholder;
      this.input.setAttribute( "aria-label", this.texts.placeholder );
      this.input.autocomplete = "off";
      this.input.spellcheck = false;
      this.addButton = document.createElement( "button" );
      this.addButton.type = "button";
      this.addButton.className = "btn btn-sm btn-outline-secondary css-class-editor__add";
      this.addButton.title = this.texts.add;
      this.addButton.setAttribute( "aria-label", this.texts.add );
      this.addButton.innerHTML = "<span class=\"fa-solid fa-plus\"></span>";
      entry.appendChild( this.input );
      entry.appendChild( this.addButton );

      container.appendChild( this.chips );
      container.appendChild( entry );

      this.input.addEventListener( "input", () => {
        if ( /\s/.test( this.input.value ) ) {
          this.commitInput(); // a space (typed or pasted) ends a class
        } else {
          this.changed();
        }
      } );
      this.input.addEventListener( "keydown", e => this.onInputKeyDown( e ) );
      this.input.addEventListener( "blur", () => this.commitInput() );
      this.addButton.addEventListener( "click", () => {
        this.commitInput();
        this.input.focus();
      } );
      this.chips.addEventListener( "click", e => {
        let remove = e.target.closest( ".js--remove" );
        if ( remove ) {
          this.removeChip( remove.closest( ".css-class-editor__chip" ) );
        }
      } );
      this.chips.addEventListener( "keydown", e => this.onChipKeyDown( e ) );

      if ( window.Sortable ) {
        window.Sortable.create( this.chips, {
          draggable: ".css-class-editor__chip",
          filter: ".js--remove",
          preventOnFilter: false,
          animation: 150,
          forceFallback: true, // the same dragging as of the blocks (the clone stays in the editor, also in a dialog)
          ghostClass: "css-class-editor__chip--ghost",
          fallbackClass: "css-class-editor__chip--dragged",
          onEnd: () => this.changed(),
        } );
      }

      this.setValue( options.value || "" );
    }

    /**
     * Fills the editor; doesn't call onChange
     * @param {String} value
     */
    setValue( value ) {
      this.original = value || "";
      this.chips.innerHTML = "";
      this.input.value = "";
      CssClassEditor.parse( this.original ).forEach( name => this.addChip( name ) );
      this.lastValue = this.getValue();
    }

    /**
     * The value of the class attribute; the original string when the classes didn't change
     * @returns {String}
     */
    getValue() {
      let value = CssClassEditor.serialize( this.classes().concat( CssClassEditor.parse( this.input.value ) ) );
      return CssClassEditor.same( value, this.original ) ? this.original : value;
    }

    classes() {
      return [ ...this.chips.querySelectorAll( ".css-class-editor__chip" ) ].map( chip => chip.dataset.name );
    }

    chipOf( name ) {
      return [ ...this.chips.querySelectorAll( ".css-class-editor__chip" ) ].find( chip => chip.dataset.name === name ) || null;
    }

    addChip( name ) {
      let chip = document.createElement( "span" );
      chip.className = "css-class-editor__chip";
      chip.tabIndex = 0;
      chip.title = this.texts.chipTitle;
      chip.dataset.name = name;
      let label = document.createElement( "span" );
      label.className = "css-class-editor__name";
      label.textContent = name;
      let remove = document.createElement( "button" );
      remove.type = "button";
      remove.tabIndex = -1; // the chip itself is focusable, Delete removes it
      remove.className = "css-class-editor__remove js--remove";
      remove.title = this.texts.remove;
      remove.setAttribute( "aria-label", this.texts.remove + " " + name );
      remove.innerHTML = "<span class=\"fa-solid fa-xmark\"></span>";
      chip.appendChild( label );
      chip.appendChild( remove );
      this.chips.appendChild( chip );
      return chip;
    }

    /**
     * Turns the typed text into chips; a class already there is just highlighted
     */
    commitInput() {
      let names = CssClassEditor.parse( this.input.value );
      this.input.value = "";
      names.forEach( name => {
        let existing = this.chipOf( name );
        if ( existing ) {
          existing.classList.remove( "css-class-editor__chip--flash" );
          void existing.offsetWidth; // restarts the animation
          existing.classList.add( "css-class-editor__chip--flash" );
        } else {
          this.addChip( name );
        }
      } );
      this.changed(); // the value is the same unless a duplicate was typed
    }

    removeChip( chip ) {
      // the focus goes to the previous chip (or the next one, or the input), not to the page
      let next = chip.previousElementSibling || chip.nextElementSibling;
      chip.remove();
      ( next || this.input ).focus();
      this.changed();
    }

    changed() {
      let value = this.getValue();
      if ( value === this.lastValue ) {
        return;
      }
      this.lastValue = value;
      if ( this.onChange ) {
        this.onChange( value );
      }
    }

    onInputKeyDown( e ) {
      if ( e.key === "Enter" && this.input.value.trim() ) {
        e.preventDefault(); // consumed: a dialog doesn't save on it
        this.commitInput();
      } else if ( e.key === "Backspace" && !this.input.value && this.input.selectionStart === 0 ) {
        let last = this.chips.lastElementChild;
        if ( last ) {
          e.preventDefault();
          last.focus();
        }
      }
    }

    onChipKeyDown( e ) {
      let chip = e.target.closest( ".css-class-editor__chip" );
      if ( !chip || e.target !== chip ) {
        return;
      }
      if ( e.key === "Delete" || e.key === "Backspace" ) {
        e.preventDefault();
        this.removeChip( chip );
      } else if ( e.altKey && ( e.key === "ArrowLeft" || e.key === "ArrowRight" ) ) {
        e.preventDefault();
        let sibling = e.key === "ArrowLeft" ? chip.previousElementSibling : chip.nextElementSibling;
        if ( sibling ) {
          this.chips.insertBefore( chip, e.key === "ArrowLeft" ? sibling : sibling.nextElementSibling );
          chip.focus();
          this.changed();
        }
      } else if ( e.key === "ArrowLeft" && chip.previousElementSibling ) {
        e.preventDefault();
        chip.previousElementSibling.focus();
      } else if ( e.key === "ArrowRight" ) {
        e.preventDefault();
        ( chip.nextElementSibling || this.input ).focus();
      }
    }
  }

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = CssClassEditor;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.CssClassEditor = CssClassEditor;
  }

} )( typeof window !== "undefined" ? window : this );

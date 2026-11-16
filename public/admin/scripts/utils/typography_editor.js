/**
 * Typography editor
 * Text alignment, font weight and italics of a block in the inspector of the block view (a [div], a [row], a column...),
 * shown under the block's other fields, separated by a line. It's not a storage of its own, but another view
 * of the block's class and style editors (UTILS.CssClassEditor, UTILS.InlineStyleEditor): a choice is written
 * as a class (text-center, font-weight-bold...) into the class editor, and a declaration of the same property
 * in the style editor (text-align, font-weight, font-style) is removed, so they never contradict each other.
 * A change made in the class or style editor is shown in the panel too.
 *
 * The classes come from window.typographyEditorConfig (app/views/admin/shared/editor_modals/_typography_editor.tpl),
 * so a project with other utility classes (e.g. Bootstrap 5: text-start, fw-bold, fst-italic) overrides the template only.
 *
 * Usage (in an inserter's inspect()):
 *
 *   let classEditor = new UTILS.CssClassEditor( ..., { onChange: ... } );
 *   let styleEditor = new UTILS.InlineStyleEditor( ..., { onChange: ... } );
 *   UTILS.TypographyEditor.attach( container, classEditor, styleEditor ); // appended to container
 *
 * The panel calls the editors' own onChange after changing their values, so the inserter applies the change as usual.
 * Without the template (#typography_editor) nothing is shown.
 *
 * Reading and changing the values don't need DOM (testable in Node.js):
 *
 *   UTILS.TypographyEditor.state( "card text-center", "font-weight: bold;" ); // { align: "center", weight: "bold", italic: false }
 *   UTILS.TypographyEditor.apply( "card text-center", "font-weight: bold;", { weight: "light" } );
 *   // { class: "card text-center font-weight-light", style: "" }
 *
 * Dependencies:
 *
 * css_class_editor.js, inline_style_editor.js (parse() and serialize())
 * _typography_editor.tpl (the panel and the classes), _md_block_editor.scss
 */
( function( root ) {

  let CssClassEditor = ( typeof module !== "undefined" && module.exports ) ? require( "./css_class_editor.js" ) : root.UTILS.CssClassEditor;
  let InlineStyleEditor = ( typeof module !== "undefined" && module.exports ) ? require( "./inline_style_editor.js" ) : root.UTILS.InlineStyleEditor;

  // CSS values of the properties recognized in the style attribute (lowercase)
  let STYLE_VALUES = {
    align: { property: "text-align", values: { left: [ "left", "start" ], center: [ "center" ], right: [ "right", "end" ], justify: [ "justify" ] } },
    weight: { property: "font-weight", values: { light: [ "300", "lighter" ], normal: [ "400", "normal" ], bold: [ "700", "bold", "bolder" ] } },
    italic: { property: "font-style", values: { true: [ "italic", "oblique" ] } },
  };

  class TypographyEditor {

    // The classes; overridden by window.typographyEditorConfig (_typography_editor.tpl)
    static config() {
      let defaults = {
        align: { left: "text-left", center: "text-center", right: "text-right", justify: "text-justify" },
        weight: { light: "font-weight-light", normal: "font-weight-normal", bold: "font-weight-bold" },
        italic: "font-italic",
      };
      return Object.assign( defaults, root.typographyEditorConfig || {} );
    }

    /**
     * What the class and style say; a class wins over a style declaration
     * @param {String} classValue
     * @param {String} styleValue
     * @param {Object} config - optional, see config()
     * @returns {Object} { align: "center"|null, weight: "bold"|null, italic: Boolean }
     */
    static state( classValue, styleValue, config ) {
      config = config || TypographyEditor.config();
      let classes = CssClassEditor.parse( classValue );
      let declarations = InlineStyleEditor.parse( styleValue );
      let fromStyle = key => {
        let spec = STYLE_VALUES[ key ];
        let found = null;
        declarations.forEach( d => {
          if ( d.property.trim().toLowerCase() === spec.property ) {
            let value = d.value.trim().toLowerCase().replace( /\s*!important$/, "" );
            found = Object.keys( spec.values ).find( k => spec.values[ k ].indexOf( value ) >= 0 ) || null;
          }
        } );
        return found;
      };
      let fromClasses = map => Object.keys( map ).find( k => classes.indexOf( map[ k ] ) >= 0 ) || null;
      return {
        align: fromClasses( config.align ) || fromStyle( "align" ),
        weight: fromClasses( config.weight ) || fromStyle( "weight" ),
        italic: classes.indexOf( config.italic ) >= 0 || fromStyle( "italic" ) === "true",
      };
    }

    /**
     * Applies changes: { align: "center"|null, weight: "bold"|null, italic: true|false }; a key not given is left as it is.
     * The class of the changed property replaces the others of it (added at the end), its declaration is removed from the style.
     * Unchanged values are returned as they were given (the same strings).
     * @param {String} classValue
     * @param {String} styleValue
     * @param {Object} changes
     * @param {Object} config - optional, see config()
     * @returns {Object} { class, style }
     */
    static apply( classValue, styleValue, changes, config ) {
      config = config || TypographyEditor.config();
      let classes = CssClassEditor.parse( classValue );
      let declarations = InlineStyleEditor.parse( styleValue );
      let classesChanged = false;
      let styleChanged = false;
      let set = ( key, all, wanted ) => {
        let kept = classes.filter( c => all.indexOf( c ) < 0 );
        if ( wanted ) {
          kept.push( wanted );
        }
        if ( kept.join( " " ) !== classes.join( " " ) ) {
          classes = kept;
          classesChanged = true;
        }
        let property = STYLE_VALUES[ key ].property;
        let filtered = declarations.filter( d => d.property.trim().toLowerCase() !== property );
        if ( filtered.length !== declarations.length ) {
          declarations = filtered;
          styleChanged = true;
        }
      };
      let wantedClass = ( map, value ) => value ? ( map[ value ] || null ) : null;
      if ( "align" in changes ) {
        let all = Object.keys( config.align ).map( k => config.align[ k ] );
        set( "align", all, wantedClass( config.align, changes.align ) );
      }
      if ( "weight" in changes ) {
        let all = Object.keys( config.weight ).map( k => config.weight[ k ] );
        set( "weight", all, wantedClass( config.weight, changes.weight ) );
      }
      if ( "italic" in changes ) {
        set( "italic", [ config.italic ], changes.italic ? config.italic : null );
      }
      return {
        class: classesChanged ? classes.join( " " ) : classValue,
        style: styleChanged ? InlineStyleEditor.serialize( declarations ) : styleValue,
      };
    }

    /**
     * Appends the panel to the container and connects it with the class and style editors
     * @param {Element} container
     * @param {CssClassEditor} classEditor
     * @param {InlineStyleEditor} styleEditor
     * @returns {TypographyEditor|null} null without the template
     */
    static attach( container, classEditor, styleEditor ) {
      let template = root.document && root.document.getElementById( "typography_editor" );
      if ( !template ) {
        return null;
      }
      return new TypographyEditor( container, template, classEditor, styleEditor );
    }

    constructor( container, template, classEditor, styleEditor ) {
      this.classEditor = classEditor;
      this.styleEditor = styleEditor;
      this.config = TypographyEditor.config();
      let id = "typography_editor_" + ( ++TypographyEditor.count );

      let fields = template.content.cloneNode( true );
      this.element = fields.firstElementChild;
      [ "align", "weight" ].forEach( name => {
        let label = fields.querySelector( ".js--" + name + "-label" );
        label.id = id + "_" + name + "_label";
        fields.querySelector( ".js--" + name ).setAttribute( "aria-labelledby", label.id );
      } );
      this.alignButtons = [ ...fields.querySelectorAll( ".js--align [data-align]" ) ].filter( btn => this.config.align[ btn.dataset.align ] );
      fields.querySelectorAll( ".js--align [data-align]" ).forEach( btn => {
        if ( this.alignButtons.indexOf( btn ) < 0 ) {
          btn.remove(); // no class for it
        }
      } );
      this.weightSelect = fields.querySelector( ".js--weight-select" );
      [ ...this.weightSelect.options ].forEach( option => {
        if ( option.value && !this.config.weight[ option.value ] ) {
          option.remove();
        }
      } );
      this.italicButton = fields.querySelector( ".js--italic" );

      this.alignButtons.forEach( btn => btn.addEventListener( "click", () => {
        // a click on the pressed button removes the alignment
        this.change( { align: this.current().align === btn.dataset.align ? null : btn.dataset.align } );
      } ) );
      this.weightSelect.addEventListener( "change", () => this.change( { weight: this.weightSelect.value || null } ) );
      this.italicButton.addEventListener( "click", () => this.change( { italic: !this.current().italic } ) );

      // changes made in the editors are shown in the panel
      [ classEditor, styleEditor ].forEach( editor => {
        let onChange = editor.onChange;
        editor.onChange = value => {
          if ( onChange ) {
            onChange( value );
          }
          this.render();
        };
      } );

      container.appendChild( fields );
      this.render();
    }

    current() {
      return TypographyEditor.state( this.classEditor.getValue(), this.styleEditor.getValue(), this.config );
    }

    render() {
      let state = this.current();
      this.alignButtons.forEach( btn => {
        let pressed = state.align === btn.dataset.align;
        btn.classList.toggle( "active", pressed );
        btn.setAttribute( "aria-pressed", pressed ? "true" : "false" );
      } );
      this.weightSelect.value = state.weight || "";
      this.italicButton.classList.toggle( "active", state.italic );
      this.italicButton.setAttribute( "aria-pressed", state.italic ? "true" : "false" );
    }

    /**
     * Writes the change into the editors and calls their onChange (the inserter applies it)
     * @param {Object} changes - see apply()
     */
    change( changes ) {
      let classValue = this.classEditor.getValue();
      let styleValue = this.styleEditor.getValue();
      let result = TypographyEditor.apply( classValue, styleValue, changes, this.config );
      if ( result.style !== styleValue ) {
        this.styleEditor.setValue( result.style );
        if ( this.styleEditor.onChange ) {
          this.styleEditor.onChange( this.styleEditor.getValue() );
        }
      }
      if ( result.class !== classValue ) {
        this.classEditor.setValue( result.class );
        if ( this.classEditor.onChange ) {
          this.classEditor.onChange( this.classEditor.getValue() );
        }
      }
      this.render();
    }
  }

  TypographyEditor.count = 0;

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = TypographyEditor;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.TypographyEditor = TypographyEditor;
  }

} )( typeof window !== "undefined" ? window : this );

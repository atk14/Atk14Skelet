/**
 * Div editor
 * Inserts a DrInk Markdown [div] shortcode, or edits class and style of an existing one.
 * Content of a [div] is not edited here: in the block view it's a list of blocks (see md_block_tokenizer.js),
 * so it's kept exactly as it was, as well as other attributes of the [div].
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [div] blocks:
 * in the dialog, or in the inspector of the block view (inspect()) where changes apply as they are typed.
 *
 * Usage:
 *
 *   if( document.getElementById( "diveditor_modal" ) ) {
 *     new UTILS.DivEditor();
 *   }
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js, css_class_editor.js, inline_style_editor.js
 * _div_editor_modal.tpl (the dialog and the template of the inspector's fields)
 * dialog.js (a native <dialog>), Bootstrap 4+ (styles)
 */
window.UTILS = window.UTILS || { };

window.UTILS.DivEditor = class {
  texts = window.diveditorTexts; // UI strings (rendered in template)

  constructor() {
    if ( window.UTILS.DivEditor.instance ) {
      window.UTILS.DivEditor.instance.attachToolbarButtons();
      return window.UTILS.DivEditor.instance;
    }
    window.UTILS.DivEditor.instance = this;

    this.modal = document.getElementById( "diveditor_modal" );
    this.classEditor = new window.UTILS.CssClassEditor( this.modal.querySelector( "#diveditor_class" ) );
    this.classEditor.input.autofocus = true;
    this.styleEditor = new window.UTILS.InlineStyleEditor( this.modal.querySelector( "#diveditor_style" ) );
    this.saveBtn = this.modal.querySelector( "#diveditor_save_btn" );
    this.save = null; // callback given by open()
    this.edited = null; // { raw, model } of the edited [div]

    this.attachToolbarButtons();

    this.saveBtn.addEventListener( "click", () => this.saveCode() );
    // Enter saves, unless the class editor used it (to turn the typed text into a class)
    this.modal.querySelector( "#diveditor_class" ).addEventListener( "keydown", e => {
      if ( e.key === "Enter" && !e.defaultPrevented && e.target.matches( "input" ) ) {
        e.preventDefault();
        this.saveCode();
      }
    } );
  }

  /**
   * Registers the editor in UTILS.MDEditorInserts (adds "Div" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "diveditor",
      text: "<span class=\"fa-regular fa-square\" aria-hidden=\"true\"></span> Div",
      title: "Div",
      modal: "#diveditor_modal",
      inline: false,
      shortcode: "div",
      parse: raw => window.UTILS.DivEditor.parse( raw ),
      open: options => this.open( options ),
      inspect: ( container, options ) => this.inspect( container, options ),
    } );
  }

  /**
   * Parses a [div] shortcode; returns null when it's not a single [div]...[/div]
   *   "[div class=\"x\"]\nText\n[/div]" -> { attrs: { class: "x" }, open: "[div class=\"x\"]", inner: "\nText\n", close: "[/div]" }
   * @param {String} raw
   * @returns {Object|null}
   */
  static parse( raw ) {
    let source = raw.trim();
    let tree = window.UTILS.MDShortcodes.parseTree( source, [ "div" ] );
    if ( tree.errors.length || tree.children.length !== 1 ) {
      return null;
    }
    let node = tree.children[ 0 ];
    if ( node.start !== 0 || node.end !== source.length ) {
      return null;
    }
    return {
      attrs: node.attrs,
      open: source.slice( node.start, node.innerStart ),
      inner: source.slice( node.innerStart, node.innerEnd ),
      close: source.slice( node.innerEnd, node.end ),
    };
  }

  /**
   * Fills the form with class and style of the given [div], or empties it
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    let model = options.raw ? window.UTILS.DivEditor.parse( options.raw ) : null;
    this.edited = model ? { raw: options.raw, model: model } : null;
    this.classEditor.setValue( model ? model.attrs.class || "" : "" );
    this.styleEditor.setValue( model ? model.attrs.style || "" : "" );
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
  }

  /**
   * Builds the [div] from the form
   */
  saveCode() {
    let code = window.UTILS.DivEditor.build( this.edited, this.classEditor.getValue(), this.styleEditor.getValue() );
    if ( this.save ) {
      this.save( code );
    }
  }

  /**
   * The fields in the inspector of the block view; every change is passed to change() at once
   * @param {Element} container
   * @param {Object} options - { raw, change }
   * @returns {Boolean} false when the [div] can't be edited here
   */
  inspect( container, options ) {
    let model = window.UTILS.DivEditor.parse( options.raw );
    let template = document.getElementById( "diveditor_inspector" );
    if ( !model || !template ) {
      return false;
    }
    let edited = { raw: options.raw, model: model };
    let fields = template.content.cloneNode( true );
    // labels need unique ids, there may be more editors in the page
    let id = "diveditor_inspector_" + ( ++window.UTILS.DivEditor.inspectorCount );
    let containers = {};
    [ "class", "style" ].forEach( name => {
      let label = fields.querySelector( "div.form-label.js--" + name );
      containers[ name ] = fields.querySelector( "div[role=group].js--" + name );
      label.id = id + "_" + name + "_label";
      containers[ name ].setAttribute( "aria-labelledby", label.id );
    } );
    let classEditor, styleEditor;
    let onChange = () => {
      options.change( window.UTILS.DivEditor.build( edited, classEditor.getValue(), styleEditor.getValue() ) );
    };
    classEditor = new window.UTILS.CssClassEditor( containers.class, { value: model.attrs.class || "", onChange: onChange } );
    styleEditor = new window.UTILS.InlineStyleEditor( containers.style, { value: model.attrs.style || "", onChange: onChange } );
    container.appendChild( fields );
    return true;
  }

  static inspectorCount = 0;

  /**
   * Builds the [div]: only the opening tag is changed, the content and other attributes are kept;
   * unchanged class and style keep the source exactly as it was
   * @param {Object|null} edited - { raw, model } of the edited [div], null for a new one
   * @param {String} cssClass
   * @param {String} style
   * @returns {String}
   */
  static build( edited, cssClass, style ) {
    // DrInk Markdown doesn't allow "]" in shortcode params
    cssClass = cssClass.replace( /\]/g, "" ).trim().replace( /\s+/g, " " );
    style = style.replace( /\]/g, "" ).trim();
    let setAttr = window.UTILS.DivEditor.prototype.setAttr;
    if ( edited ) {
      let attrs = Object.assign( {}, edited.model.attrs );
      if ( window.UTILS.CssClassEditor.same( attrs.class || "", cssClass ) && window.UTILS.InlineStyleEditor.same( attrs.style || "", style ) ) {
        return edited.raw;
      }
      setAttr( attrs, "class", cssClass );
      setAttr( attrs, "style", style );
      return window.UTILS.MDShortcodes.buildOpeningTag( "div", attrs ) + edited.model.inner + edited.model.close;
    }
    let attrs = {};
    setAttr( attrs, "class", cssClass );
    setAttr( attrs, "style", style );
    return window.UTILS.MDShortcodes.buildOpeningTag( "div", attrs ) + "\n[/div]";
  }

  setAttr( attrs, name, value ) {
    if ( value ) {
      attrs[ name ] = value;
    } else {
      delete attrs[ name ];
    }
  }
};

/**
 * Div editor
 * Inserts a DrInk Markdown [div] shortcode, or edits class and style of an existing one.
 * Content of a [div] is not edited here: in the block view it's a list of blocks (see md_block_tokenizer.js),
 * so it's kept exactly as it was, as well as other attributes of the [div].
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [div] blocks.
 *
 * Usage:
 *
 *   if( document.getElementById( "diveditor_modal" ) ) {
 *     new UTILS.DivEditor();
 *   }
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js
 * _div_editor_modal.tpl
 * Bootstrap 4+
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
    this.classInput = this.modal.querySelector( "#diveditor_class" );
    this.styleInput = this.modal.querySelector( "#diveditor_style" );
    this.saveBtn = this.modal.querySelector( "#diveditor_save_btn" );
    this.save = null; // callback given by open()
    this.edited = null; // { raw, model } of the edited [div]

    this.attachToolbarButtons();

    this.saveBtn.addEventListener( "click", () => this.saveCode() );
    [ this.classInput, this.styleInput ].forEach( input => {
      input.addEventListener( "keydown", e => {
        if ( e.key === "Enter" ) {
          e.preventDefault();
          this.saveCode();
        }
      } );
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
    this.classInput.value = model ? model.attrs.class || "" : "";
    this.styleInput.value = model ? model.attrs.style || "" : "";
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
    window.setTimeout( () => this.classInput.focus(), 500 ); // after the modal is shown
  }

  /**
   * Builds the [div]: only the opening tag is changed, the content and other attributes are kept;
   * unchanged class and style keep the source exactly as it was
   */
  saveCode() {
    // DrInk Markdown doesn't allow "]" in shortcode params
    let cssClass = this.classInput.value.replace( /\]/g, "" ).trim().replace( /\s+/g, " " );
    let style = this.styleInput.value.replace( /\]/g, "" ).trim();
    let code;
    if ( this.edited ) {
      let attrs = Object.assign( {}, this.edited.model.attrs );
      if ( ( attrs.class || "" ) === cssClass && ( attrs.style || "" ) === style ) {
        code = this.edited.raw;
      } else {
        this.setAttr( attrs, "class", cssClass );
        this.setAttr( attrs, "style", style );
        code = window.UTILS.MDShortcodes.buildOpeningTag( "div", attrs ) + this.edited.model.inner + this.edited.model.close;
      }
    } else {
      let attrs = {};
      this.setAttr( attrs, "class", cssClass );
      this.setAttr( attrs, "style", style );
      code = window.UTILS.MDShortcodes.buildOpeningTag( "div", attrs ) + "\n[/div]";
    }
    if ( this.save ) {
      this.save( code );
    }
  }

  setAttr( attrs, name, value ) {
    if ( value ) {
      attrs[ name ] = value;
    } else {
      delete attrs[ name ];
    }
  }
};

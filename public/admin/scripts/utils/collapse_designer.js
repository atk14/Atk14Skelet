/**
 * Collapse designer
 * Inserts a DrInk Markdown [collapse] shortcode (a title showing hidden content on click), or edits its settings:
 * the title, its appearance (a link or a button: classes offered by a select) and CSS class, CSS class of the content,
 * class and style of the box.
 * The content is not edited here: in the block view it's a list of blocks (see md_block_tokenizer.js),
 * so it's kept exactly as it was (see md_collapse_model.js).
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [collapse] blocks:
 * in the dialog, or in the inspector of the block view (inspect()) where changes apply as they are typed.
 * The fields are the same in both places (UTILS.CollapseFields, from the template #collapsedesigner_fields).
 *
 * Usage:
 *
 *   if( document.getElementById( "collapsedesigner_modal" ) ) {
 *     new UTILS.CollapseDesigner();
 *   }
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_collapse_model.js, md_editor_inserts.js, css_class_editor.js, inline_style_editor.js
 * _collapse_designer_modal.tpl (the dialog and the template of the fields)
 * dialog.js (a native <dialog>), Bootstrap 4+ (styles)
 */
window.UTILS = window.UTILS || { };

/**
 * The fields of a [collapse]: title, class of the title, class of the content, class and style of the box
 */
window.UTILS.CollapseFields = class {

  static count = 0;

  /**
   * @param {Element} container - the fields are rendered into it
   * @param {Object} options - { onChange, small }
   */
  constructor( container, options ) {
    options = options || {};
    let fields = document.getElementById( "collapsedesigner_fields" ).content.cloneNode( true );
    let q = selector => fields.querySelector( selector );
    this.titleInput = q( "input.js--title" );
    this.appearanceSelect = q( "select.js--appearance" );

    // labels need unique ids, the fields are in the dialog and in inspectors
    let id = "collapsedesigner_" + ( ++window.UTILS.CollapseFields.count );
    this.titleInput.id = id + "_title";
    q( "label.js--title-label" ).htmlFor = this.titleInput.id;
    this.appearanceSelect.id = id + "_appearance";
    q( "label.js--appearance-label" ).htmlFor = this.appearanceSelect.id;
    let groups = {};
    [ "title-class", "content-class", "class", "style" ].forEach( name => {
      let label = q( ".js--" + name + "-label" );
      label.id = id + "_" + name + "_label";
      groups[ name ] = q( "div[role=group].js--" + name );
      groups[ name ].setAttribute( "aria-labelledby", label.id );
    } );
    if ( options.small ) {
      this.titleInput.classList.add( "form-control-sm" );
      this.appearanceSelect.classList.add( "form-control-sm" );
    }

    let changed = () => {
      if ( options.onChange ) {
        options.onChange();
      }
    };
    this.titleClassEditor = new window.UTILS.CssClassEditor( groups[ "title-class" ], {
      onChange: () => {
        this.syncAppearance();
        changed();
      },
    } );
    // the chosen appearance replaces the classes of the previous one, the other classes are kept after it
    this.appearanceSelect.addEventListener( "change", () => {
      let value = this.appearanceSelect.value;
      if ( value === "custom" ) {
        return;
      }
      let others = this.titleClasses().filter( c => !window.UTILS.CollapseFields.isAppearanceClass( c ) );
      this.titleClassEditor.setValue( value.split( /\s+/ ).filter( c => c ).concat( others ).join( " " ) );
      changed();
    } );
    this.contentClassEditor = new window.UTILS.CssClassEditor( groups[ "content-class" ], { onChange: changed } );
    this.classEditor = new window.UTILS.CssClassEditor( groups.class, { onChange: changed } );
    this.styleEditor = new window.UTILS.InlineStyleEditor( groups.style, { onChange: changed } );
    this.titleInput.addEventListener( "input", changed );

    container.appendChild( fields );
  }

  /**
   * Fills the fields from a model (UTILS.MDCollapseModel.parse()), or with the defaults of a new [collapse] (null)
   * @param {Object|null} model
   * @param {String} defaultTitle
   */
  fill( model, defaultTitle ) {
    this.titleInput.value = model ? model.title.text.trim() : defaultTitle || "";
    this.titleClassEditor.setValue( model ? model.title.attrs.class || "" : "" );
    this.contentClassEditor.setValue( model ? model.content.attrs.class || "" : "" );
    this.classEditor.setValue( model ? model.attrs.class || "" : "" );
    this.styleEditor.setValue( model ? model.attrs.style || "" : "" );
    this.syncAppearance();
  }

  titleClasses() {
    return this.titleClassEditor.getValue().split( /\s+/ ).filter( c => c );
  }

  /**
   * Classes managed by the appearance select: btn and its variants (btn-primary, btn-outline-secondary, btn-link...),
   * not its sizes and other modifiers (btn-sm, btn-block...)
   * @param {String} cssClass
   * @returns {Boolean}
   */
  static isAppearanceClass( cssClass ) {
    return cssClass === "btn" || ( /^btn-[a-z-]+$/.test( cssClass ) && !/^btn-(sm|lg|block)$/.test( cssClass ) );
  }

  /**
   * Selects the appearance matching the title's classes, or "custom"
   */
  syncAppearance() {
    let key = classes => classes.slice().sort().join( " " );
    let current = key( this.titleClasses().filter( c => window.UTILS.CollapseFields.isAppearanceClass( c ) ) );
    let option = [ ...this.appearanceSelect.options ].find( o => o.value !== "custom" && key( o.value.split( /\s+/ ).filter( c => c ) ) === current );
    this.appearanceSelect.value = option ? option.value : "custom";
  }

  /**
   * @returns {Object} changes for UTILS.MDCollapseModel.update() or create()
   */
  values() {
    return {
      title: this.titleInput.value,
      titleAttrs: { class: this.titleClassEditor.getValue() },
      contentAttrs: { class: this.contentClassEditor.getValue() },
      attrs: { class: this.classEditor.getValue(), style: this.styleEditor.getValue() },
    };
  }
};

window.UTILS.CollapseDesigner = class {
  texts = window.collapsedesignerTexts; // UI strings (rendered in template)

  constructor() {
    if ( window.UTILS.CollapseDesigner.instance ) {
      window.UTILS.CollapseDesigner.instance.attachToolbarButtons();
      return window.UTILS.CollapseDesigner.instance;
    }
    window.UTILS.CollapseDesigner.instance = this;

    this.modal = document.getElementById( "collapsedesigner_modal" );
    this.fields = new window.UTILS.CollapseFields( this.modal.querySelector( "#collapsedesigner" ) );
    this.fields.titleInput.autofocus = true;
    this.saveBtn = this.modal.querySelector( "#collapsedesigner_save_btn" );
    this.save = null; // callback given by open()
    this.model = null; // the edited [collapse]

    this.attachToolbarButtons();

    this.saveBtn.addEventListener( "click", () => this.saveCode() );
    this.fields.titleInput.addEventListener( "keydown", e => {
      if ( e.key === "Enter" ) {
        e.preventDefault();
        this.saveCode();
      }
    } );
  }

  /**
   * Registers the designer in UTILS.MDEditorInserts (adds "Collapse" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "collapsedesigner",
      text: "<span class=\"fa-solid fa-square-caret-down\" aria-hidden=\"true\"></span> Collapse",
      title: "Collapse",
      modal: "#collapsedesigner_modal",
      inline: false,
      shortcode: "collapse",
      parse: raw => window.UTILS.MDCollapseModel.parse( raw ),
      open: options => this.open( options ),
      inspect: ( container, options ) => this.inspect( container, options ),
      // the content part (selected by the inspector's path of a block in it) shows the same settings
      inspectChild: ( container, options ) => this.inspect( container, options ),
    } );
  }

  /**
   * Fills the dialog with the given [collapse], or with the defaults of a new one
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    this.model = options.raw ? window.UTILS.MDCollapseModel.parse( options.raw ) : null;
    this.fields.fill( this.model, this.texts.defaultTitle );
    this.saveBtn.textContent = this.model ? this.texts.save : this.texts.insert;
  }

  /**
   * Builds the [collapse] from the dialog; the title is required
   */
  saveCode() {
    let values = this.fields.values();
    if ( !values.title.trim() ) {
      this.fields.titleInput.focus();
      return;
    }
    let code = this.model ? window.UTILS.MDCollapseModel.update( this.model, values ) : window.UTILS.MDCollapseModel.create( values );
    if ( this.save ) {
      this.save( code );
    }
  }

  /**
   * The fields in the inspector of the block view; every change is passed to change() at once
   * (a change leaving the title empty is not applied)
   * @param {Element} container
   * @param {Object} options - { raw, change }
   * @returns {Boolean} false when the [collapse] can't be edited here
   */
  inspect( container, options ) {
    let model = window.UTILS.MDCollapseModel.parse( options.raw );
    if ( !model || !document.getElementById( "collapsedesigner_fields" ) ) {
      return false;
    }
    let fields = new window.UTILS.CollapseFields( container, {
      small: true,
      onChange: () => {
        let values = fields.values();
        if ( values.title.trim() ) {
          options.change( window.UTILS.MDCollapseModel.update( model, values ) );
        }
      },
    } );
    fields.fill( model );
    return true;
  }
};

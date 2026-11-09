/**
 * Link list inserter
 * Builds a DrInk Markdown [linklist] shortcode for a chosen link list, or edits an existing one (see parse()).
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [linklist] blocks:
 * in the dialog, or in the inspector of the block view (inspect()) where changes apply at once.
 * The fields are the same in both places (UTILS.LinkListFields, from the template #linklistinserter_fields).
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js, css_class_editor.js, inline_style_editor.js
 * _linklist_inserter_modal.tpl (the dialog and the template of the fields)
 * dialog.js (a native <dialog>), Bootstrap 4+ (styles)
 */
window.UTILS = window.UTILS || { };

/**
 * The fields of a [linklist]: the link list, type, showing the title, class and style;
 * attributes the fields don't show are kept
 */
window.UTILS.LinkListFields = class {

  static count = 0;

  /**
   * @param {Element} container - the fields are rendered into it
   * @param {Object} options - { onChange, small }
   */
  constructor( container, options ) {
    options = options || {};
    this.onChange = options.onChange || null;
    this.attrs = {}; // attributes the fields don't show
    let fields = document.getElementById( "linklistinserter_fields" ).content.cloneNode( true );
    this.linklistSelect = fields.querySelector( ".js--linklist" );
    this.typeSelect = fields.querySelector( ".js--type" );
    this.showTitleInput = fields.querySelector( ".js--show-title" );

    // labels need unique ids, the fields are in the dialog and in inspectors
    let id = "linklistinserter_" + ( ++window.UTILS.LinkListFields.count );
    [ "linklist", "type", "show-title" ].forEach( name => {
      let input = fields.querySelector( ".js--" + name );
      input.id = id + "_" + name;
      fields.querySelector( ".js--" + name + "-label" ).htmlFor = input.id;
    } );
    [ "class", "style" ].forEach( name => {
      let label = fields.querySelector( ".js--" + name + "-label" );
      label.id = id + "_" + name + "_label";
      fields.querySelector( ".js--" + name ).setAttribute( "aria-labelledby", label.id );
    } );
    if ( options.small ) {
      [ this.linklistSelect, this.typeSelect ].forEach( select => select.classList.add( "form-control-sm" ) );
    }

    let changed = () => {
      if ( this.onChange ) {
        this.onChange();
      }
    };
    this.classEditor = new window.UTILS.CssClassEditor( fields.querySelector( ".js--class" ), { onChange: changed } );
    this.styleEditor = new window.UTILS.InlineStyleEditor( fields.querySelector( ".js--style" ), { onChange: changed } );
    [ this.linklistSelect, this.typeSelect, this.showTitleInput ].forEach( input => input.addEventListener( "change", changed ) );

    container.appendChild( fields );
  }

  /**
   * Fills the fields with the attributes of a [linklist], or resets them (null)
   * @param {Object|null} model - see LinkListInserter.parse()
   */
  fill( model ) {
    let attrs = Object.assign( {}, model ? model.attrs : {} );
    this.setSelectValue( this.linklistSelect, attrs.code || "" );
    this.setSelectValue( this.typeSelect, attrs.type || "" );
    // falsy as in PHP (see shared/helpers/drink_shortcodes/_linklist.tpl): "0" or an empty value
    this.showTitleInput.checked = attrs.show_title === undefined || !/^(0|)$/.test( attrs.show_title );
    this.classEditor.setValue( attrs.class || "" );
    this.styleEditor.setValue( attrs.style || "" );
    [ "code", "type", "show_title", "class", "style" ].forEach( key => delete attrs[ key ] );
    this.attrs = attrs;
  }

  // A value missing in the select (e.g. a link list not existing anymore) is added, so it's not lost
  setSelectValue( select, value ) {
    if ( value && ![ ...select.options ].some( option => option.value === value ) ) {
      let option = document.createElement( "option" );
      option.value = value;
      option.textContent = value;
      select.appendChild( option );
    }
    select.value = value;
  }

  /**
   * Builds the [linklist] shortcode from the fields, "" when no link list is chosen;
   * only values differing from the defaults of block.drink_shortcode__linklist.php are written
   * @returns {String}
   */
  code() {
    let code = this.linklistSelect.value;
    if ( !code ) {
      return "";
    }
    let attrs = { code: code };
    if ( this.typeSelect.value ) {
      attrs.type = this.typeSelect.value;
    }
    if ( !this.showTitleInput.checked ) {
      attrs.show_title = "0";
    }
    // DrInk Markdown doesn't allow "]" in shortcode params
    let cssClass = this.classEditor.getValue().trim().replace( /\]/g, "" );
    if ( cssClass ) {
      attrs.class = cssClass;
    }
    let cssStyle = this.styleEditor.getValue().trim().replace( /\]/g, "" );
    if ( cssStyle ) {
      attrs.style = cssStyle;
    }
    Object.assign( attrs, this.attrs );
    return window.UTILS.MDShortcodes.buildOpeningTag( "linklist", attrs ) + "[/linklist]";
  }
};

window.UTILS.LinkListInserter = class {
  texts = window.linklistinserterTexts; // UI strings (rendered in template)

  constructor() {
    if ( window.UTILS.LinkListInserter.instance ) {
      window.UTILS.LinkListInserter.instance.attachToolbarButtons();
      return window.UTILS.LinkListInserter.instance;
    }
    window.UTILS.LinkListInserter.instance = this;

    this.modal = document.getElementById( "linklistinserter_modal" );
    this.fields = new window.UTILS.LinkListFields( this.modal.querySelector( "#linklistinserter" ) );
    this.fields.linklistSelect.autofocus = true;
    this.copyBtn = this.modal.querySelector( "#linklistinserter_copy_btn" );
    this.saveBtn = this.modal.querySelector( "#linklistinserter_save_btn" );
    this.save = null; // callback given by open()
    this.original = null; // { raw, code } of the edited shortcode

    this.attachToolbarButtons();
    this.setHandlers();
  }

  /**
   * Registers the inserter in UTILS.MDEditorInserts (adds "Link List" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "linklistinserter",
      text: "<span class=\"fas fa-grip\" aria-hidden=\"true\"></span> Link List",
      title: "Link List",
      modal: "#linklistinserter_modal",
      inline: false,
      shortcode: "linklist",
      parse: raw => window.UTILS.LinkListInserter.parse( raw ),
      open: options => this.open( options ),
      inspect: ( container, options ) => this.inspect( container, options ),
    } );
  }

  setHandlers() {
    this.copyBtn.addEventListener( "click", () => {
      let code = this.fields.code();
      if ( !code ) {
        return;
      }
      this.setClipboard( code );
      this.showCopiedFeedback();
    } );
    this.saveBtn.addEventListener( "click", () => {
      let code = this.fields.code();
      if ( !code ) {
        this.fields.linklistSelect.focus();
        return;
      }
      if ( this.original && code === this.original.code ) {
        code = this.original.raw; // nothing changed, the source is kept exactly as it was
      }
      if ( this.save ) {
        this.save( code );
      }
    } );
  }

  /**
   * Fills the dialog with the given [linklist] shortcode, or resets it
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    let model = options.raw ? window.UTILS.LinkListInserter.parse( options.raw ) : null;
    this.fields.fill( model );
    this.original = model ? { raw: options.raw, code: this.fields.code() } : null;
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
  }

  /**
   * The fields in the inspector of the block view; every change is passed to change() at once
   * (a change leaving no link list chosen is not applied)
   * @param {Element} container
   * @param {Object} options - { raw, change }
   * @returns {Boolean} false when the [linklist] can't be edited here
   */
  inspect( container, options ) {
    let model = window.UTILS.LinkListInserter.parse( options.raw );
    if ( !model || !document.getElementById( "linklistinserter_fields" ) ) {
      return false;
    }
    let original = null;
    let fields = new window.UTILS.LinkListFields( container, {
      small: true,
      onChange: () => {
        let code = fields.code();
        if ( code ) {
          options.change( code === original.code ? original.raw : code );
        }
      },
    } );
    fields.fill( model );
    original = { raw: options.raw, code: fields.code() };
    return true;
  }

  /**
   * Parses a [linklist] shortcode; returns null when it's not a single [linklist][/linklist]
   *   "[linklist code=\"main\" type=\"with_images\"][/linklist]" -> { attrs: { code: "main", type: "with_images" } }
   * @param {String} raw
   * @returns {Object|null} { attrs }
   */
  static parse( raw ) {
    let source = raw.trim();
    let tree = window.UTILS.MDShortcodes.parseTree( source, [ "linklist" ] );
    if ( tree.errors.length || tree.children.length !== 1 ) {
      return null;
    }
    let node = tree.children[ 0 ];
    if ( node.start !== 0 || node.end !== source.length || source.slice( node.innerStart, node.innerEnd ).trim() ) {
      return null;
    }
    return { attrs: node.attrs };
  }

  /**
   * Briefly shows a "Copied!" confirmation on the copy button
   */
  showCopiedFeedback() {
    let originalText = this.copyBtn.textContent;
    this.copyBtn.textContent = this.texts && this.texts.copied || "Copied!";
    window.setTimeout( () => {
      this.copyBtn.textContent = originalText;
    }, 1000 );
  }

  /**
   * Puts given text to clipboard
   * @param {String} text
   */
  async setClipboard( text ) {
    const type = "text/plain";
    const clipboardItemData = {
      [ type ]: text,
    };
    // eslint-disable-next-line no-undef
    const clipboardItem = new ClipboardItem( clipboardItemData );
    await navigator.clipboard.write( [ clipboardItem ] );
  }
};

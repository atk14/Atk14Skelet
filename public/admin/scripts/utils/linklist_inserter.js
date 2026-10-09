/**
 * Link list inserter
 * Builds a DrInk Markdown [linklist] shortcode for a chosen link list, or edits an existing one (see parse()).
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [linklist] blocks.
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js
 * _linklist_inserter_modal.tpl
 * Bootstrap 4+
 */
window.UTILS = window.UTILS || { };

window.UTILS.LinkListInserter = class {
  texts = window.linklistinserterTexts; // UI strings (rendered in template)

  constructor() {
    if ( window.UTILS.LinkListInserter.instance ) {
      window.UTILS.LinkListInserter.instance.attachToolbarButtons();
      return window.UTILS.LinkListInserter.instance;
    }
    window.UTILS.LinkListInserter.instance = this;

    this.modal = document.getElementById( "linklistinserter_modal" );
    this.linklistSelect = this.modal.querySelector( "#linklistinserter_linklist" );
    this.styleSelect = this.modal.querySelector( "#linklistinserter_style" );
    this.classInput = this.modal.querySelector( "#linklistinserter_class" );
    this.copyBtn = this.modal.querySelector( "#linklistinserter_copy_btn" );
    this.saveBtn = this.modal.querySelector( "#linklistinserter_save_btn" );
    this.save = null; // callback given by open()
    this.original = null; // { raw, code } of the edited shortcode
    this.attrs = {}; // other attributes of the edited shortcode, kept

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
    } );
  }

  setHandlers() {
    this.copyBtn.addEventListener( "click", () => {
      let code = this.generateCode();
      if ( !code ) {
        return;
      }
      this.setClipboard( code );
      this.showCopiedFeedback();
    } );
    this.saveBtn.addEventListener( "click", () => {
      let code = this.generateCode();
      if ( !code ) {
        this.linklistSelect.focus();
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
   * Fills the form with the given [linklist] shortcode, or resets it
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    let model = options.raw ? window.UTILS.LinkListInserter.parse( options.raw ) : null;
    this.reset();
    if ( model ) {
      let attrs = Object.assign( {}, model.attrs );
      this.setSelectValue( this.linklistSelect, attrs.code || "" );
      this.setSelectValue( this.styleSelect, attrs.style || "" );
      this.classInput.value = attrs.class || "";
      delete attrs.code;
      delete attrs.style;
      delete attrs.class;
      this.attrs = attrs;
      this.original = { raw: options.raw, code: this.generateCode() };
    }
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
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
   * Parses a [linklist] shortcode; returns null when it's not a single [linklist][/linklist]
   *   "[linklist code=\"main\" class=\"m-4\"][/linklist]" -> { attrs: { code: "main", class: "m-4" } }
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
   * Builds the [linklist] shortcode from the current form values, "" when no link list is chosen
   * @returns {String}
   */
  generateCode() {
    let code = this.linklistSelect.value;
    if ( !code ) {
      return "";
    }
    let attrs = { code: code };
    if ( this.styleSelect.value ) {
      attrs.style = this.styleSelect.value;
    }
    // DrInk Markdown doesn't allow "]" in shortcode params
    let cssClass = this.classInput.value.trim().replace( /\]/g, "" );
    if ( cssClass ) {
      attrs.class = cssClass;
    }
    Object.assign( attrs, this.attrs );
    return window.UTILS.MDShortcodes.buildOpeningTag( "linklist", attrs ) + "[/linklist]";
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

  reset() {
    this.linklistSelect.value = "";
    this.styleSelect.value = "";
    this.classInput.value = "";
    this.attrs = {};
    this.original = null;
  }
};

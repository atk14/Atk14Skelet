/**
 * Tab designer
 * Builds a DrInk Markdown [tabs]/[tab] shortcode from a list of named tabs and their content,
 * or edits an existing one (see parse()).
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [tabs] blocks.
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js
 * _tabdesigner.scss
 * _tabdesigner_modal.tpl
 * Bootstrap 4+
 */
window.UTILS = window.UTILS || { };

window.UTILS.TabDesigner = class {
  texts = window.tabdesignerTexts; // UI strings (rendered in template)

  constructor() {
    if ( window.UTILS.TabDesigner.instance ) {
      window.UTILS.TabDesigner.instance.attachToolbarButtons();
      return window.UTILS.TabDesigner.instance;
    }
    window.UTILS.TabDesigner.instance = this;

    this.modal = document.getElementById( "tabdesigner_modal" );
    this.tabFieldsTemplate = document.getElementById( "tabdesigner_tab_fields" );
    this.tabsContainer = this.modal.querySelector( ".tabdesigner__tabs" );
    this.addTabBtn = this.modal.querySelector( ".js--add-tab" );
    this.copyBtn = this.modal.querySelector( "#tabdesigner_copy_btn" );
    this.saveBtn = this.modal.querySelector( "#tabdesigner_save_btn" );
    this.save = null; // callback given by open()
    this.original = null; // { raw, code } of the edited shortcode
    this.tabsAttrs = {}; // attributes of [tabs]

    this.attachToolbarButtons();

    this.addTabBtn.addEventListener( "click", () => {
      this.addTab();
    } );
    this.tabsContainer.addEventListener( "click", this.onTabsContainerClick.bind( this ) );
    this.copyBtn.addEventListener( "click", () => {
      this.setClipboard( this.generateCode() );
      this.showCopiedFeedback();
    } );
    this.saveBtn.addEventListener( "click", () => {
      let code = this.generateCode();
      if ( this.original && code === this.original.code ) {
        code = this.original.raw; // nothing changed, the source is kept exactly as it was
      }
      if ( this.save ) {
        this.save( code );
      }
    } );
  }

  /**
   * Registers the designer in UTILS.MDEditorInserts (adds "Tabs" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "tabdesigner",
      text: "<img src=\"/public/admin/dist/images/icon-tabs.svg\" width=\"15\" height=\"15\" alt=\"\" class=\"dropdown-item__icon\"> Tabs",
      title: "Tabs",
      modal: "#tabdesigner_modal",
      inline: false,
      shortcode: "tabs",
      parse: raw => window.UTILS.TabDesigner.parse( raw ),
      open: options => this.open( options ),
    } );
  }

  /**
   * Fills the modal with the given [tabs] shortcode, or with two empty tabs
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    this.tabsContainer.innerHTML = "";
    let model = options.raw ? window.UTILS.TabDesigner.parse( options.raw ) : null;
    if ( model ) {
      this.tabsAttrs = model.attrs;
      model.tabs.forEach( tab => this.addTab( tab ) );
      this.original = { raw: options.raw, code: this.generateCode() };
    } else {
      this.tabsAttrs = {};
      this.addTab();
      this.addTab();
      this.original = null;
    }
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
  }

  /**
   * Parses a [tabs] shortcode; returns null when it contains anything the designer couldn't keep
   * (text between tabs, other shortcodes directly in [tabs], unpaired tags...)
   *   "[tabs]\n[tab name=\"A\"]\nText\n[/tab]\n[/tabs]" -> { attrs: {}, tabs: [ { attrs: { name: "A" }, content: "Text" } ] }
   * @param {String} raw
   * @returns {Object|null} { attrs, tabs: [ { attrs, content } ] }
   */
  static parse( raw ) {
    let MDShortcodes = window.UTILS.MDShortcodes;
    let source = raw.trim();
    let tree = MDShortcodes.parseTree( source, [ "tabs", "tab" ] );
    if ( tree.errors.length || tree.children.length !== 1 ) {
      return null;
    }
    let tabsNode = tree.children[ 0 ];
    if ( tabsNode.name !== "tabs" || tabsNode.start !== 0 || tabsNode.end !== source.length ) {
      return null;
    }
    let tabs = [];
    let pos = tabsNode.innerStart;
    for ( let node of tabsNode.children ) {
      if ( node.name !== "tab" || source.slice( pos, node.start ).trim() ) {
        return null;
      }
      tabs.push( {
        attrs: node.attrs,
        content: window.UTILS.TabDesigner.cleanContent( source.slice( node.innerStart, node.innerEnd ) ),
      } );
      pos = node.end;
    }
    if ( source.slice( pos, tabsNode.innerEnd ).trim() ) {
      return null;
    }
    return { attrs: tabsNode.attrs, tabs: tabs };
  }

  // Leading blank lines and trailing whitespace are not a part of the content (indentation of the first line is)
  static cleanContent( content ) {
    return content.replace( /^(?:[ \t]*\r?\n)+/, "" ).replace( /\s+$/, "" );
  }

  /**
   * Appends a tab fields group
   * @param {Object} tab - { attrs, content }, optional
   */
  addTab( tab ) {
    tab = tab || { attrs: {}, content: "" };
    let el = this.tabFieldsTemplate.content.firstElementChild.cloneNode( true );
    el.tabAttrs = tab.attrs; // other attributes than the name are kept
    el.querySelector( ".js--tab-name" ).value = tab.attrs.name || "";
    el.querySelector( ".js--tab-content" ).value = tab.content;
    this.tabsContainer.appendChild( el );
  }

  /**
   * Handles clicks on the remove button within a tab fields group (event delegation)
   * At least one tab is always kept
   */
  onTabsContainerClick( event ) {
    let button = event.target.closest( ".js--remove-tab" );
    if ( !button ) {
      return;
    }
    if ( this.tabsContainer.querySelectorAll( ".tabdesigner__tab" ).length <= 1 ) {
      return;
    }
    button.closest( ".tabdesigner__tab" ).remove();
  }

  /**
   * Builds the [tabs]/[tab] Markdown shortcode from the current form values
   * @returns {String}
   */
  generateCode() {
    let MDShortcodes = window.UTILS.MDShortcodes;
    let tabsCode = [ ...this.tabsContainer.querySelectorAll( ".tabdesigner__tab" ) ]
      .map( ( tab, index ) => {
        // DrInk Markdown doesn't allow "]" in shortcode params
        let name = tab.querySelector( ".js--tab-name" ).value.trim().replace( /\]/g, ")" ) || `Tab ${ index + 1 }`;
        let content = window.UTILS.TabDesigner.cleanContent( tab.querySelector( ".js--tab-content" ).value );
        let attrs = Object.assign( {}, tab.tabAttrs || {}, { name: name } );
        return `${ MDShortcodes.buildOpeningTag( "tab", attrs ) }\n${ content }\n[/tab]`;
      } )
      .join( "\n" );
    return `${ MDShortcodes.buildOpeningTag( "tabs", this.tabsAttrs ) }\n${ tabsCode }\n[/tabs]`;
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

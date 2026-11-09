/**
 * Tab designer
 * Builds a DrInk Markdown [tabs]/[tab] shortcode from a list of named tabs and their content,
 * or edits an existing one (see parse()).
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [tabs] blocks.
 * In the inspector of the block view (inspect()) the tabs are renamed, added, removed and reordered
 * and the class of [tabs] is set, without touching their content (it's edited as blocks there), see md_tabs_model.js.
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js, md_tabs_model.js, css_class_editor.js, SortableJS (optional)
 * _tabdesigner.scss
 * _tabdesigner_modal.tpl
 * dialog.js (a native <dialog>), Bootstrap 4+ (styles)
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
      inspect: ( container, options ) => this.inspect( container, options ),
    } );
  }

  /**
   * The tabs in the inspector of the block view: a row per tab (dragging handle, name, remove button), "Add tab"
   * and the class of [tabs]. Renaming and the class apply as they are typed; adding, removing and moving a tab at once.
   * @param {Element} container
   * @param {Object} options - { raw, change }
   * @returns {Boolean} false when the [tabs] can't be edited here
   */
  inspect( container, options ) {
    let MDTabsModel = window.UTILS.MDTabsModel;
    let model = MDTabsModel.parse( options.raw );
    let template = document.getElementById( "tabdesigner_inspector" );
    if ( !model || !template ) {
      return false;
    }
    let texts = this.texts || {};
    let fields = template.content.cloneNode( true );
    let list = fields.querySelector( ".js--tabs" );
    let addBtn = fields.querySelector( ".js--add-tab" );
    let id = "tabdesigner_inspector_" + ( ++window.UTILS.TabDesigner.inspectorCount );
    [ "tabs", "class" ].forEach( name => {
      let label = fields.querySelector( ".js--" + name + "-label" );
      label.id = id + "_" + name + "_label";
      fields.querySelector( ".js--" + name ).setAttribute( "aria-labelledby", label.id );
    } );

    let apply = immediate => options.change( MDTabsModel.serialize( model ), immediate ? { immediate: true } : undefined );
    let rows = () => [ ...list.querySelectorAll( ".tabdesigner-inspector__tab" ) ];
    let updateRemoveButtons = () => {
      let single = rows().length <= 1;
      rows().forEach( row => {
        row.querySelector( ".js--remove-tab" ).disabled = single; // at least one tab is kept
      } );
    };
    let addRow = name => {
      let row = document.createElement( "div" );
      row.className = "tabdesigner-inspector__tab";
      let handle = document.createElement( "span" );
      handle.className = "tabdesigner-inspector__handle js--handle";
      handle.title = texts.moveTab || "Drag to change the order";
      handle.innerHTML = "<span class=\"fa-solid fa-grip-vertical\"></span>";
      let input = document.createElement( "input" );
      input.type = "text";
      input.className = "form-control form-control-sm js--tab-name";
      input.placeholder = texts.tabName || "Tab name";
      input.setAttribute( "aria-label", texts.tabName || "Tab name" );
      input.value = name;
      let remove = document.createElement( "button" );
      remove.type = "button";
      remove.className = "btn btn-sm tabdesigner-inspector__remove js--remove-tab";
      remove.title = texts.removeTab || "Remove tab";
      remove.setAttribute( "aria-label", texts.removeTab || "Remove tab" );
      remove.innerHTML = "<span class=\"fa-solid fa-xmark\"></span>";
      row.appendChild( handle );
      row.appendChild( input );
      row.appendChild( remove );
      list.appendChild( row );
      return row;
    };
    model.tabs.forEach( tab => addRow( tab.attrs.name || "" ) );
    updateRemoveButtons();

    // renaming; an empty name is not written (the tab keeps its name until a new one is typed)
    list.addEventListener( "input", e => {
      let row = e.target.closest( ".tabdesigner-inspector__tab" );
      let name = e.target.value.trim();
      if ( row && name ) {
        model = MDTabsModel.rename( model, rows().indexOf( row ), name );
        apply( false );
      }
    } );
    list.addEventListener( "click", e => {
      let remove = e.target.closest( ".js--remove-tab" );
      if ( !remove || rows().length <= 1 ) {
        return;
      }
      let row = remove.closest( ".tabdesigner-inspector__tab" );
      let index = rows().indexOf( row );
      let next = row.nextElementSibling || row.previousElementSibling;
      model = MDTabsModel.remove( model, index );
      row.remove();
      updateRemoveButtons();
      next.querySelector( ".js--tab-name" ).focus();
      apply( true );
    } );
    addBtn.addEventListener( "click", () => {
      let name = ( texts.newTab || "Tab" ) + " " + ( model.tabs.length + 1 );
      model = MDTabsModel.add( model, name );
      let input = addRow( name ).querySelector( ".js--tab-name" );
      updateRemoveButtons();
      input.focus();
      input.select();
      apply( true );
    } );
    if ( window.Sortable ) {
      window.Sortable.create( list, {
        handle: ".js--handle",
        draggable: ".tabdesigner-inspector__tab",
        animation: 150,
        forceFallback: true,
        ghostClass: "tabdesigner-inspector__tab--ghost",
        onEnd: evt => {
          if ( evt.oldIndex !== evt.newIndex ) {
            model = MDTabsModel.move( model, evt.oldIndex, evt.newIndex );
            apply( true );
          }
        },
      } );
    }

    new window.UTILS.CssClassEditor( fields.querySelector( ".js--class" ), {
      value: model.attrs.class || "",
      onChange: value => {
        model = MDTabsModel.setAttrs( model, { class: value } );
        apply( false );
      },
    } );

    container.appendChild( fields );
    return true;
  }

  static inspectorCount = 0;

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

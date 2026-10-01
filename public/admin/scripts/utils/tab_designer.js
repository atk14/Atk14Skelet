/**
 * Tab designer
 * Builds a DrInk Markdown [tabs]/[tab] shortcode from a list of named tabs and their content
 * Adds "Tabs" item to the MD editor's "Insert" dropdown
 *
 * Dependencies:
 *
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

    this.attachToolbarButtons();

    this.addTabBtn.addEventListener( "click", () => {
      this.addTab();
    } );
    this.tabsContainer.addEventListener( "click", this.onTabsContainerClick.bind( this ) );
    this.copyBtn.addEventListener( "click", () => {
      this.setClipboard( this.generateCode() );
      this.showCopiedFeedback();
    } );

    // Bootstrap 4's modal events are jQuery-only (not native DOM events), while
    // Bootstrap 5 dispatches them as native DOM events
    let resetForm = () => {
      this.reset();
    };
    if ( window.bootstrapVersion === 5 ) {
      this.modal.addEventListener( "show.bs.modal", resetForm );
    } else {
      window.jQuery( this.modal ).on( "show.bs.modal", resetForm );
    }
  }

  /**
   * Adds "Tabs" item to the "Insert" dropdown of every MD editor toolbar
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorToolbarHelper.addToolbarDropdownItem( "insert_dropdown", {
      name: "tabdesigner",
      text: "<img src=\"/public/admin/dist/images/icon-tabs.svg\" width=\"15\" height=\"15\" alt=\"\" class=\"dropdown-item__icon\"> Tabs",
      title: "Tabs",
      className: "",
      hasModal: true,
      modalId: "#tabdesigner_modal",
    } );
  }

  /**
   * Resets the form back to two empty tabs
   */
  reset() {
    this.tabsContainer.innerHTML = "";
    this.addTab();
    this.addTab();
  }

  /**
   * Appends a new empty tab fields group
   */
  addTab() {
    let tab = this.tabFieldsTemplate.content.firstElementChild.cloneNode( true );
    this.tabsContainer.appendChild( tab );
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
    let tabsCode = [ ...this.tabsContainer.querySelectorAll( ".tabdesigner__tab" ) ]
      .map( ( tab, index ) => {
        let name = tab.querySelector( ".js--tab-name" ).value.trim().replace( /"/g, "'" ) || `Tab ${ index + 1 }`;
        let content = tab.querySelector( ".js--tab-content" ).value.trim();
        return `[tab name="${ name }"]\n${ content }\n[/tab]`;
      } )
      .join( "\n" );
    return `[tabs]\n${ tabsCode }\n[/tabs]`;
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

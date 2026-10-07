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

    this.attachToolbarButtons();
    this.setHandlers();


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
   * Adds "Link List" item to the "Insert" dropdown of every MD editor toolbar
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorToolbarHelper.addToolbarDropdownItem( "insert_dropdown", {
      name: "linklistinserter",
      text: "<span class=\"fas fa-grip\" aria-hidden=\"true\"></span> Link List",
      title: "Link List",
      className: "",
      hasModal: true,
      modalId: "#linklistinserter_modal",
    } );
  }

  setHandlers() {
    this.copyBtn.addEventListener( "click", () => {
      let code = this.createLinkListCode(
        this.linklistSelect.value,
        this.styleSelect.value,
        this.classInput.value.trim()
      );
      if ( !code ) {
        return;
      }
      this.setClipboard( code );
      this.showCopiedFeedback();
    } );
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

  createLinkListCode( linklistCode, style, cssClass ) {
    let code = "";
    if ( linklistCode ) {
      code = `[linklist code="${linklistCode}"`;
      if ( style ) {
        code += ` style="${style}"`;
      }
      if ( cssClass ) {
        code += ` class="${cssClass}"`;
      }
      code += "][/linklist]";
    }
    return code;
  }

  reset() {
    this.linklistSelect.value = "";
    this.styleSelect.value = "";
    this.classInput.value = "";
  }
};
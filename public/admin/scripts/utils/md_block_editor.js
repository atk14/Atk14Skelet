/**
 * Markdown block editor
 * Adds a "Blocks" view mode to every MD editor, as the first and default one (the editor's "Edit" is renamed to "Code").
 * The Markdown source is split into blocks (see md_block_tokenizer.js) and every block
 * is rendered separately by the API (markdown/transform_batch), so it looks like on the web.
 *
 * Blocks can be edited (double-click, Enter or the pencil button), deleted, added (the "+" buttons)
 * and moved (dragging the handle by SortableJS, or Alt+Up / Alt+Down).
 * The source in the Ace editor stays the only source of truth: every change is written into it
 * (and so into the textarea), then the source is tokenized and the blocks are rendered again.
 * Undo/redo uses Ace's undo manager, so the history is shared with the "Code" view.
 *
 * A shortcode block can be edited by a dedicated editor (e.g. a designer in a modal):
 *
 *   UTILS.MDBlockEditor.registerBlockEditor( "tabs", {
 *     canEdit: raw => true,                       // optional; false -> the source is edited inline
 *     edit: ( { raw, block, onSave, onCancel } ) => { ... onSave( newRaw ); },
 *   } );
 *
 * Usage:
 *
 *   UTILS.MDBlockEditor.init(); // after UTILS.initializeMarkdonEditors()
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_block_tokenizer.js, md_editor_toolbar_helper.js, preview_mode_toggle.js, SortableJS
 * _md_block_editor.scss, _md_preview.scss
 * app/views/admin/shared/layout/_md_block_editor_texts.tpl
 * Ace (via bootstrap-markdown-editor)
 */
window.UTILS = window.UTILS || { };

window.UTILS.MDBlockEditor = class {

  // Rendered HTML by request payload (source + base_href); shared by all editors in the page
  static renderCache = new Map();

  // Max number of sources in one request (see TransformBatchForm::MAX_SOURCES)
  static batchSize = 500;

  // Dedicated editors of shortcode blocks by shortcode name
  static blockEditors = {};

  // Blocks offered by the "+" buttons; "|" marks the cursor position in the template
  // Formatting buttons of the editor's toolbar (data-btn) working on the edited block
  static formatButtons = [ "h1", "h2", "h3", "bold", "italic", "ul", "ol", "link", "image" ];

  static newBlockTemplates = [
    { type: "paragraph", template: "|" },
    { type: "heading", template: "## |" },
    { type: "list", template: "- |" },
    { type: "quote", template: "> |" },
    { type: "table", template: "| | |\n|---|---|\n| | |" },
    { type: "code", template: "```\n|\n```" },
    { type: "hr", template: "---" },
  ];

  /**
   * Adds "Blocks" button as the first view mode of every MD editor in the page,
   * renames the editor's "Edit" to "Code" and switches new editors to the block view.
   * Safe to call again, e.g. after a form is replaced: editors already set up are left as they are.
   */
  static init() {
    let texts = window.UTILS.MDBlockEditor.texts();
    window.UTILS.MDEditorToolbarHelper.addViewModeButton( {
      name: "blocks",
      text: "<span class=\"fa-solid fa-table-cells-large\"></span> " + texts.btnBlocks,
      title: texts.btnBlocksTitle,
      className: "btn-blocks",
      prepend: true,
      onClick: e => {
        window.UTILS.MDBlockEditor.forContainer( e.currentTarget.closest( ".md-container" ) ).show();
      },
    } );

    [ ...document.querySelectorAll( ".md-container" ) ].forEach( container => {
      if ( !container.querySelector( "[data-btnname='blocks']" ) || container.mdBlockEditor ) {
        return;
      }
      let editButton = container.querySelector( ".md-toolbar .btn-edit" );
      editButton.innerHTML = "<span class=\"fa-solid fa-code\"></span> ";
      editButton.appendChild( document.createTextNode( texts.btnCode ) );
      editButton.title = texts.btnCodeTitle;

      window.UTILS.MDBlockEditor.forContainer( container ).show();
    } );
  }

  /**
   * Registers a dedicated editor for blocks of the given shortcode
   * @param {String} name - shortcode name, e.g. "tabs"
   * @param {Object} editor - { canEdit( raw ), edit( { raw, block, onSave, onCancel } ) }
   */
  static registerBlockEditor( name, editor ) {
    window.UTILS.MDBlockEditor.blockEditors[ name ] = editor;
  }

  /**
   * Returns the block editor of the given MD editor container, creates it when needed
   * @param {Element} container - .md-container
   * @returns {MDBlockEditor}
   */
  static forContainer( container ) {
    if ( !container.mdBlockEditor ) {
      container.mdBlockEditor = new window.UTILS.MDBlockEditor( container );
    }
    return container.mdBlockEditor;
  }

  // UI strings (rendered in template), with English fallbacks
  static texts() {
    let texts = Object.assign( {
      btnBlocks: "Blocks",
      btnBlocksTitle: "Block view",
      btnCode: "Code",
      btnCodeTitle: "Markdown source",
      edit: "Edit",
      editTitle: "Edit this block (or double-click it)",
      moveTitle: "Drag to move the block (or Alt+Up / Alt+Down)",
      editHint: "Ctrl+Enter or clicking outside saves, Esc cancels",
      showSource: "Source",
      showSourceTitle: "Show the source of this block in the editor",
      editObject: "Edit object",
      editObjectTitle: "Edit the object in a new tab",
      remove: "Delete",
      removeTitle: "Delete this block",
      add: "Add block",
      addTitle: "Add a block below",
      undo: "Undo",
      redo: "Redo",
      loading: "Loading",
      empty: "The text is empty",
      error: "The preview could not be loaded",
      retry: "Try again",
      unclosed: "The shortcode is not closed",
      types: {},
    }, window.mdBlockEditorTexts );
    texts.types = Object.assign( {
      heading: "Heading",
      paragraph: "Paragraph",
      list: "List",
      quote: "Quote",
      table: "Table",
      code: "Code",
      hr: "Divider",
      iobject: "Object",
      definitions: "Link definitions",
    }, texts.types );
    return texts;
  }

  /**
   * @param {Element} container - .md-container created by bootstrap-markdown-editor after the textarea
   */
  constructor( container ) {
    this.container = container;
    this.textarea = container.previousElementSibling;
    this.texts = window.UTILS.MDBlockEditor.texts();
    this.mdEditor = container.querySelector( ".md-editor" );
    this.mdPreview = container.querySelector( ".md-preview" );
    this.button = container.querySelector( "[data-btnname='blocks']" );
    this.doc = null;
    this.editing = null; // { index, isNew, element, input, original }
    this.menu = null;
    this.refreshIobjectsOnFocus = false;

    let blockShortcodes = null;
    try {
      blockShortcodes = JSON.parse( this.textarea.dataset.block_shortcodes );
    } catch ( e ) {
      // the default list is used
    }
    this.tokenizerOptions = blockShortcodes ? { blockShortcodes: blockShortcodes } : {};

    // the container is placed after .md-preview so that querySelector( ".md-preview" ) still finds the editor's own preview
    this.element = document.createElement( "div" );
    this.element.className = "md-blocks";
    this.element.style.display = "none";
    this.element.innerHTML = "<div class=\"md-preview__viewport preview--desktop\"></div>";
    this.mdPreview.after( this.element );
    this.viewport = this.element.querySelector( ".md-preview__viewport" );
    window.UTILS.PreviewModeToggle.init( this.element );
    this.addHistoryButtons();

    this.appendButton = document.createElement( "button" );
    this.appendButton.type = "button";
    this.appendButton.className = "md-blocks__append js--add-block";
    this.appendButton.innerHTML = "<span class=\"fa-solid fa-plus\"></span> ";
    this.appendButton.appendChild( document.createTextNode( this.texts.add ) );
    this.viewport.appendChild( this.appendButton );

    // leaving the block view by the editor's own buttons
    [ ...container.querySelectorAll( ".md-toolbar .btn-edit, .md-toolbar .btn-preview" ) ].forEach( btn => {
      btn.addEventListener( "mousedown", () => {
        this.commitEdit(); // before the editor's own handler reads the source
      } );
      btn.addEventListener( "click", () => {
        this.hide();
        if ( btn.classList.contains( "btn-edit" ) ) {
          // Ace doesn't redraw changes made while it was hidden; this handler runs after the editor's own one shows it
          this.redrawAce();
        }
      } );
    } );

    // dragging blocks by their handle; the fallback mode keeps the dragged clone inside .md-blocks,
    // so it is styled as the content (a native drag image of a big block is hardly visible)
    this.sortable = window.Sortable.create( this.viewport, {
      handle: ".md-block__handle",
      draggable: ".md-block:not(.md-block--new)",
      filter: ".md-block--editing",
      preventOnFilter: false,
      forceFallback: true,
      fallbackOnBody: false,
      animation: 150,
      scrollSensitivity: 100, // the upper ~55px are covered by the sticky preview mode toolbar
      ghostClass: "md-block--ghost",
      chosenClass: "md-block--chosen",
      fallbackClass: "md-block--dragged",
      onChoose: () => {
        // an edited block is saved before dragging; it only re-renders the edited block, the dragged one stays
        this.commitEdit();
        this.closeMenu();
      },
      onEnd: evt => {
        this.onSortEnd( evt.item );
      },
    } );

    // while a block is edited, the formatting buttons work on its textarea instead of the hidden Ace;
    // capturing listeners run before the editor's own handlers on the buttons
    let toolbar = container.querySelector( ".md-toolbar" );
    toolbar.addEventListener( "mousedown", e => {
      if ( this.editing && this.formatButton( e.target ) ) {
        e.preventDefault(); // keeps the focus (and so the edit) in the textarea
      }
    }, true );
    toolbar.addEventListener( "click", e => {
      let btn = this.formatButton( e.target );
      if ( this.editing && btn ) {
        e.preventDefault();
        e.stopPropagation();
        this.applyFormat( btn.dataset.btn );
      }
    }, true );

    this.viewport.addEventListener( "click", this.onClick.bind( this ) );
    this.viewport.addEventListener( "dblclick", this.onDblClick.bind( this ) );
    this.element.addEventListener( "keydown", this.onKeyDown.bind( this ) );
    document.addEventListener( "mousedown", e => {
      if ( this.menu && !this.menu.contains( e.target ) ) {
        this.closeMenu();
      }
    } );

    // an object edited in another tab must be rendered again when coming back
    window.addEventListener( "focus", () => {
      if ( this.refreshIobjectsOnFocus && this.visible ) {
        this.refreshIobjectsOnFocus = false;
        this.forgetRendered( block => block.type === "iobject" );
        this.refresh();
      }
    } );
  }

  get visible() {
    return this.element.style.display !== "none";
  }

  get aceEditor() {
    return window.ace.edit( this.mdEditor );
  }

  redrawAce() {
    let editor = this.aceEditor;
    editor.resize( true );
    editor.renderer.updateFull( true );
  }

  /**
   * Switches the MD editor to the block view
   */
  show() {
    if ( !this.visible ) {
      // the height is kept on the editor (see md_editor_resizer.js);
      // a hidden editor has no offsetHeight, its style height is used then
      let height = this.mdEditor.offsetHeight || parseFloat( this.mdEditor.style.height ) || 400;
      this.element.style.height = height + "px";
      this.mdEditor.style.display = "none";
      this.mdPreview.style.display = "none";
      this.element.style.display = "";
      this.container.querySelector( ".btn-edit" ).classList.remove( "active" );
      this.container.querySelector( ".btn-preview" ).classList.remove( "active" );
      this.button.classList.add( "active" );
      this.setToolbarDisabled( true );
    }
    this.refresh();
  }

  /**
   * Leaves the block view; showing another panel is up to bootstrap-markdown-editor
   */
  hide() {
    if ( !this.visible ) {
      return;
    }
    this.commitEdit();
    this.closeMenu();
    this.element.style.display = "none";
    this.button.classList.remove( "active" );
    this.setToolbarDisabled( false );
  }

  /**
   * Toolbar buttons editing the source (formatting, Insert...) are disabled in the block view,
   * only switching the view and fullscreen are left; formatting buttons are enabled while a block is edited
   * @param {Boolean} disabled
   */
  setToolbarDisabled( disabled ) {
    let keep = [ "edit", "preview", "fullscreen" ];
    [ ...this.container.querySelectorAll( ".md-toolbar button, .md-toolbar .md-btn-file" ) ].forEach( el => {
      if ( keep.indexOf( el.dataset.btn ) >= 0 || el === this.button || el.closest( ".dropdown-menu" ) ) {
        return;
      }
      el.classList.toggle( "disabled", disabled );
      if ( el.tagName === "BUTTON" ) {
        el.disabled = disabled;
      }
    } );
  }

  /**
   * Enables the formatting buttons while a block is edited
   * @param {Boolean} enabled
   */
  setFormattingEnabled( enabled ) {
    [ ...this.container.querySelectorAll( ".md-toolbar .md-btn[data-btn]" ) ].forEach( el => {
      if ( this.formatButton( el ) ) {
        el.classList.toggle( "disabled", !enabled );
        el.disabled = !enabled;
      }
    } );
  }

  formatButton( target ) {
    let btn = target.closest( ".md-btn[data-btn]" );
    return btn && window.UTILS.MDBlockEditor.formatButtons.indexOf( btn.dataset.btn ) >= 0 ? btn : null;
  }

  // ---------------------------------------------------------------- rendering

  /**
   * Tokenizes the current source and updates the blocks.
   * Blocks already rendered are shown at once, the others are rendered by the API meanwhile.
   */
  refresh() {
    this.closeMenu();
    let source = this.aceEditor.getSession().getValue();
    this.doc = window.UTILS.MDBlockTokenizer.tokenize( source, this.tokenizerOptions );
    this.updateHistoryButtons();

    let cache = window.UTILS.MDBlockEditor.renderCache;
    let elements = this.doc.blocks.map( ( block, index ) => this.elementFor( block, index ) );
    this.placeElements( elements );

    let message = this.viewport.querySelector( ".md-blocks__message" );
    if ( !this.doc.blocks.length && !this.editing ) {
      if ( !message ) {
        message = document.createElement( "p" );
        message.className = "md-blocks__message";
        message.textContent = this.texts.empty;
        this.viewport.insertBefore( message, this.appendButton );
      }
    } else if ( message ) {
      message.remove();
    }

    let pending = elements.filter( el => !cache.has( el.mdKey ) );
    if ( !pending.length ) {
      return;
    }
    this.fetchRendered( pending.map( el => el.mdSource ) ).then( () => {
      this.fillElements( pending );
    } ).catch( () => {
      pending.forEach( el => {
        if ( !cache.has( el.mdKey ) ) {
          this.setElementError( el );
        }
      } );
    } );
  }

  /**
   * Returns an element showing the block: an existing one when the block didn't change
   * (so the state of interactive components and loaded media is kept), or a new one
   */
  elementFor( block, index ) {
    let source = this.renderSource( block );
    let key = this.cacheKey( source ) + ( block.unclosed ? "\n#unclosed" : "" );
    let el = [ ...this.viewport.children ].find( child => child.mdKey === key && !child.mdUsed );
    if ( !el ) {
      el = this.blockElement( block );
      el.mdKey = key;
      el.mdSource = source;
      let html = window.UTILS.MDBlockEditor.renderCache.get( this.cacheKey( source ) );
      if ( html !== undefined ) {
        this.setElementContent( el, block, html );
      } else {
        this.setElementPending( el, block );
      }
    }
    el.mdUsed = true;
    el.mdBlock = block;
    el.dataset.index = index;
    return el;
  }

  /**
   * Puts the elements into the viewport in the given order, moving as few nodes as possible
   * (moving a node reloads its iframes, e.g. embedded videos)
   */
  placeElements( elements ) {
    let editingNew = this.editing && this.editing.isNew ? this.editing.element : null;
    let editingExisting = this.editing && !this.editing.isNew ? this.editing.element : null;
    let wanted = elements.slice();
    if ( editingNew ) {
      wanted.splice( this.editing.index, 0, editingNew );
    }
    [ ...this.viewport.querySelectorAll( ":scope > .md-block" ) ].forEach( el => {
      if ( wanted.indexOf( el ) < 0 && el !== editingExisting ) {
        el.remove();
      }
    } );
    let ref = this.viewport.querySelector( ":scope > .md-block" );
    wanted.forEach( el => {
      if ( el === ref ) {
        ref = ref.nextElementSibling;
        return;
      }
      this.viewport.insertBefore( el, ref && ref.classList.contains( "md-block" ) ? ref : this.appendButton );
    } );
    elements.forEach( el => {
      el.mdUsed = false;
    } );
  }

  fillElements( elements ) {
    let cache = window.UTILS.MDBlockEditor.renderCache;
    let swiper = false;
    elements.forEach( el => {
      if ( el.parentElement && cache.has( el.mdKey ) ) {
        this.setElementContent( el, el.mdBlock, cache.get( el.mdKey ) );
        swiper = swiper || !!el.querySelector( ".swiper" );
      }
    } );
    if ( swiper && window.UTILS.initSwiper ) {
      window.UTILS.initSwiper();
    }
  }

  /**
   * Builds the element of a block, without its rendered content
   * @param {Object} block
   * @returns {Element}
   */
  blockElement( block ) {
    let el = document.createElement( "div" );
    el.className = "md-block md-block--" + block.type;
    el.tabIndex = 0;
    if ( block.unclosed ) {
      el.classList.add( "md-block--unclosed" );
    }

    let toolbar = document.createElement( "div" );
    toolbar.className = "md-block__toolbar";
    let handle = document.createElement( "span" );
    handle.className = "md-block__handle";
    handle.title = this.texts.moveTitle;
    handle.innerHTML = "<span class=\"fa-solid fa-grip-vertical\"></span>";
    toolbar.appendChild( handle );
    let label = document.createElement( "span" );
    label.className = "md-block__label";
    label.textContent = this.blockLabel( block );
    if ( block.unclosed ) {
      label.innerHTML += " <span class=\"fa-solid fa-triangle-exclamation\"></span>";
      label.title = this.texts.unclosed;
    }
    toolbar.appendChild( label );
    toolbar.appendChild( this.toolbarButton( "js--edit-block", "fa-pen", this.texts.edit, this.texts.editTitle ) );
    if ( block.type === "iobject" ) {
      let link = this.toolbarButton( "js--edit-object", "fa-arrow-up-right-from-square", this.texts.editObject, this.texts.editObjectTitle, "a" );
      link.href = "/admin/" + document.documentElement.getAttribute( "lang" ) + "/iobjects/edit/?id=" + block.id;
      link.target = "_blank";
      toolbar.appendChild( link );
    }
    toolbar.appendChild( this.toolbarButton( "js--show-source", "fa-code", this.texts.showSource, this.texts.showSourceTitle ) );
    toolbar.appendChild( this.toolbarButton( "js--remove-block", "fa-trash-can", "", this.texts.removeTitle ) );

    let content = document.createElement( "div" );
    content.className = "md-block__content";

    let add = document.createElement( "button" );
    add.type = "button";
    add.className = "md-block__add js--add-block";
    add.title = this.texts.addTitle;
    add.innerHTML = "<span class=\"fa-solid fa-plus\"></span>";

    el.appendChild( toolbar );
    el.appendChild( content );
    el.appendChild( add );
    return el;
  }

  toolbarButton( className, icon, text, title, tagName ) {
    let btn = document.createElement( tagName || "button" );
    if ( !tagName ) {
      btn.type = "button";
    }
    btn.className = "md-block__btn " + className;
    btn.title = title;
    btn.innerHTML = "<span class=\"fa-solid " + icon + "\"></span>";
    if ( text ) {
      btn.appendChild( document.createTextNode( " " + text ) );
    }
    return btn;
  }

  setElementContent( el, block, html ) {
    let content = el.querySelector( ".md-block__content" );
    el.classList.remove( "md-block--pending", "md-block--error", "md-block--empty" );
    content.innerHTML = html || "";
    if ( !content.textContent.trim() && !content.querySelector( "img, iframe, video, svg, hr, table" ) ) {
      // nothing visible, e.g. link reference definitions or an HTML comment
      el.classList.add( "md-block--empty" );
      this.setContentSource( content, block );
    }
  }

  setElementPending( el, block ) {
    el.classList.add( "md-block--pending" );
    this.setContentSource( el.querySelector( ".md-block__content" ), block );
  }

  setElementError( el ) {
    el.classList.remove( "md-block--pending" );
    el.classList.add( "md-block--error" );
    let content = el.querySelector( ".md-block__content" );
    let p = document.createElement( "p" );
    p.className = "md-blocks__message";
    p.textContent = this.texts.error + " ";
    let retry = document.createElement( "button" );
    retry.type = "button";
    retry.className = "btn btn-sm btn-outline-secondary js--retry";
    retry.textContent = this.texts.retry;
    p.appendChild( retry );
    content.appendChild( p );
  }

  setContentSource( content, block ) {
    let code = document.createElement( "code" );
    code.className = "md-block__source";
    code.textContent = block.raw;
    content.innerHTML = "";
    content.appendChild( code );
  }

  /**
   * Human readable name of the block, e.g. "Heading 2", "[tabs]", "Object #123"
   * @param {Object} block
   * @returns {String}
   */
  blockLabel( block ) {
    let types = this.texts.types;
    switch ( block.type ) {
      case "shortcode":
        return "[" + block.name + "]";
      case "heading":
        return types.heading + " " + block.level;
      case "iobject":
        return types.iobject + " #" + block.id;
      case "html":
        return "<" + block.tag + ">";
      default:
        return types[ block.type ] || block.type;
    }
  }

  /**
   * Source sent for rendering: a block lifted out of its document doesn't see
   * link reference definitions placed elsewhere, so they are appended to it
   * @param {Object} block
   * @returns {String}
   */
  renderSource( block ) {
    if ( block.type === "definitions" || !this.doc.definitions.length ) {
      return block.raw;
    }
    return block.raw + "\n\n" + this.doc.definitions.join( "\n" );
  }

  cacheKey( source ) {
    return ( this.textarea.dataset.base_href || "" ) + "\n" + source;
  }

  // Removes rendered HTML of the matching blocks from the cache
  forgetRendered( filter ) {
    let cache = window.UTILS.MDBlockEditor.renderCache;
    this.doc.blocks.filter( filter ).forEach( block => {
      cache.delete( this.cacheKey( this.renderSource( block ) ) );
    } );
    [ ...this.viewport.querySelectorAll( ":scope > .md-block" ) ].forEach( el => {
      if ( el.mdBlock && filter( el.mdBlock ) ) {
        el.mdKey = null; // never reused
      }
    } );
  }

  /**
   * Renders the given sources by the API and stores the results in the cache
   * @param {Array} sources
   * @returns {Promise}
   */
  fetchRendered( sources ) {
    let cache = window.UTILS.MDBlockEditor.renderCache;
    let unique = sources.filter( ( s, i ) => sources.indexOf( s ) === i );
    let batches = [];
    for ( let i = 0; i < unique.length; i += window.UTILS.MDBlockEditor.batchSize ) {
      batches.push( unique.slice( i, i + window.UTILS.MDBlockEditor.batchSize ) );
    }
    let lang = document.documentElement.getAttribute( "lang" );
    return Promise.all( batches.map( batch => {
      let body = new URLSearchParams();
      body.append( "sources", JSON.stringify( batch ) );
      body.append( "base_href", this.textarea.dataset.base_href || "" );
      body.append( "editor_preview", "1" );
      body.append( "format", "json" );
      return fetch( "/api/" + lang + "/markdown/transform_batch/", {
        method: "POST",
        body: body,
        credentials: "same-origin",
      } ).then( response => {
        if ( !response.ok ) {
          throw new Error( "HTTP " + response.status );
        }
        return response.json();
      } ).then( contents => {
        batch.forEach( ( source, i ) => {
          cache.set( this.cacheKey( source ), contents[ i ] );
        } );
      } );
    } ) );
  }

  // ---------------------------------------------------------------- changing the source

  /**
   * Writes the document into the editor and refreshes the blocks
   */
  applyDoc() {
    this.setSource( window.UTILS.MDBlockTokenizer.join( this.doc ) );
    this.refresh();
  }

  /**
   * Replaces the source in Ace by the given text.
   * Only the changed part is replaced, so Ace's undo history gets a single, minimal change.
   * @param {String} text
   */
  setSource( text ) {
    let editor = this.aceEditor;
    let session = editor.getSession();
    let doc = session.getDocument();
    let old = session.getValue();
    if ( old === text ) {
      return;
    }
    let prefix = 0;
    let max = Math.min( old.length, text.length );
    while ( prefix < max && old[ prefix ] === text[ prefix ] ) {
      prefix++;
    }
    let suffix = 0;
    while ( suffix < max - prefix && old[ old.length - 1 - suffix ] === text[ text.length - 1 - suffix ] ) {
      suffix++;
    }
    let Range = window.ace.require( "ace/range" ).Range;
    let start = doc.indexToPosition( prefix );
    let end = doc.indexToPosition( old.length - suffix );
    session.markUndoGroup();
    doc.replace( new Range( start.row, start.column, end.row, end.column ), text.slice( prefix, text.length - suffix ) );
    session.markUndoGroup();
  }

  // Source typed by the user: surrounding blank lines would end up as block separators
  static cleanRaw( raw ) {
    return raw.replace( /^(?:[ \t]*\r?\n)+/, "" ).replace( /\s+$/, "" );
  }

  undo() {
    this.commitEdit();
    let session = this.aceEditor.getSession();
    if ( session.getUndoManager().canUndo() ) {
      session.getUndoManager().undo( session );
      this.refresh();
    }
  }

  redo() {
    this.commitEdit();
    let session = this.aceEditor.getSession();
    if ( session.getUndoManager().canRedo() ) {
      session.getUndoManager().redo( session );
      this.refresh();
    }
  }

  addHistoryButtons() {
    let bar = this.element.querySelector( ".preview-mode-toggler" );
    let group = document.createElement( "div" );
    group.className = "btn-group md-blocks__history";
    group.innerHTML =
      "<button type=\"button\" class=\"btn btn-outline-light js--undo\"><span class=\"fa-solid fa-rotate-left\"></span></button>" +
      "<button type=\"button\" class=\"btn btn-outline-light js--redo\"><span class=\"fa-solid fa-rotate-right\"></span></button>";
    this.undoButton = group.querySelector( ".js--undo" );
    this.redoButton = group.querySelector( ".js--redo" );
    this.undoButton.title = this.texts.undo + " (Ctrl+Z)";
    this.redoButton.title = this.texts.redo + " (Ctrl+Shift+Z)";
    this.undoButton.addEventListener( "click", () => this.undo() );
    // mousedown would commit an edited block before the click
    this.undoButton.addEventListener( "mousedown", e => e.preventDefault() );
    this.redoButton.addEventListener( "click", () => this.redo() );
    this.redoButton.addEventListener( "mousedown", e => e.preventDefault() );
    bar.insertBefore( group, bar.firstChild );
  }

  updateHistoryButtons() {
    let undoManager = this.aceEditor.getSession().getUndoManager();
    this.undoButton.disabled = !undoManager.canUndo();
    this.redoButton.disabled = !undoManager.canRedo();
  }

  // ---------------------------------------------------------------- editing blocks

  /**
   * Edits the block by its dedicated editor (when registered) or inline
   * @param {Number} index
   */
  editBlock( index ) {
    this.commitEdit();
    let block = this.doc.blocks[ index ];
    if ( !block ) {
      return;
    }
    let blockEditor = block.type === "shortcode" && window.UTILS.MDBlockEditor.blockEditors[ block.name ];
    if ( blockEditor && ( !blockEditor.canEdit || blockEditor.canEdit( block.raw ) ) ) {
      let raw = block.raw;
      blockEditor.edit( {
        raw: raw,
        block: block,
        onSave: newRaw => {
          // the document may have been changed meanwhile, so the block is looked up again
          let i = this.doc.blocks.findIndex( b => b.raw === raw );
          if ( i >= 0 ) {
            this.replaceBlock( i, newRaw );
          }
        },
        onCancel: () => {},
      } );
      return;
    }
    let el = this.viewport.querySelector( ":scope > .md-block[data-index='" + index + "']" );
    this.startEdit( { index: index, isNew: false, element: el, original: block.raw, value: block.raw, cursor: block.raw.length } );
  }

  /**
   * Starts editing a new block placed at the given position
   * @param {Number} index
   * @param {String} template - "|" marks the cursor position
   */
  addBlock( index, template ) {
    this.commitEdit();
    let cursor = template.indexOf( "|" );
    let value = template.replace( "|", "" );
    if ( cursor < 0 ) {
      // nothing to fill in (e.g. a divider)
      this.insertBlock( index, value );
      return;
    }
    let el = document.createElement( "div" );
    el.className = "md-block md-block--new";
    this.startEdit( { index: index, isNew: true, element: el, original: "", value: value, cursor: cursor } );
  }

  startEdit( editing ) {
    let el = editing.element;
    el.classList.add( "md-block--editing" );

    let input = document.createElement( "textarea" );
    input.className = "form-control md-block__input";
    input.value = editing.value;
    input.spellcheck = true;
    let hint = document.createElement( "div" );
    hint.className = "md-block__hint";
    hint.textContent = this.texts.editHint;
    let wrap = document.createElement( "div" );
    wrap.className = "md-block__editor";
    wrap.appendChild( input );
    wrap.appendChild( hint );
    el.appendChild( wrap );

    editing.input = input;
    editing.wrap = wrap;
    this.editing = editing;
    this.setFormattingEnabled( true );
    if ( editing.isNew ) {
      this.refresh(); // places the new element
    }

    let autosize = () => {
      input.style.height = "auto";
      input.style.height = ( input.scrollHeight + 2 ) + "px";
    };
    input.addEventListener( "input", autosize );
    input.addEventListener( "keydown", e => {
      if ( e.key === "Escape" ) {
        e.preventDefault();
        e.stopPropagation();
        this.cancelEdit();
      } else if ( e.key === "Enter" && ( e.ctrlKey || e.metaKey ) ) {
        e.preventDefault();
        this.commitEdit();
      } else if ( ( e.ctrlKey || e.metaKey ) && !e.shiftKey && !e.altKey && { b: 1, i: 1, k: 1 }[ e.key.toLowerCase() ] ) {
        // the same shortcuts as in the "Code" view
        e.preventDefault();
        this.applyFormat( { b: "bold", i: "italic", k: "link" }[ e.key.toLowerCase() ] );
      }
    } );
    input.addEventListener( "blur", () => {
      // a click on another block's button commits first; the timeout lets focus settle
      window.setTimeout( () => {
        if ( this.editing === editing && document.activeElement !== input ) {
          this.commitEdit();
        }
      }, 0 );
    } );
    autosize();
    input.focus();
    input.setSelectionRange( editing.cursor, editing.cursor );
  }

  /**
   * Finishes the edited block: writes it into the source (an emptied block is removed)
   */
  commitEdit() {
    let editing = this.editing;
    if ( !editing ) {
      return;
    }
    this.editing = null;
    let raw = window.UTILS.MDBlockEditor.cleanRaw( editing.input.value );
    this.finishEditElement( editing );
    let element = editing.element;

    if ( editing.isNew ) {
      element.remove();
      if ( raw ) {
        this.insertBlock( editing.index, raw );
      } else {
        this.refresh();
      }
    } else if ( raw === editing.original ) {
      this.focusBlock( editing.index );
    } else if ( !raw ) {
      this.removeBlock( editing.index );
    } else {
      this.replaceBlock( editing.index, raw );
    }
  }

  cancelEdit() {
    let editing = this.editing;
    if ( !editing ) {
      return;
    }
    this.editing = null;
    this.finishEditElement( editing );
    if ( editing.isNew ) {
      editing.element.remove();
      this.refresh();
      this.focusBlock( editing.index - 1 );
    } else {
      this.focusBlock( editing.index );
    }
  }

  finishEditElement( editing ) {
    editing.wrap.remove();
    editing.element.classList.remove( "md-block--editing" );
    this.setFormattingEnabled( false );
  }

  /**
   * Applies a formatting button to the edited block, the same way as bootstrap-markdown-editor does in Ace:
   * wraps the selection (or a placeholder, left selected) or prefixes the selected lines
   * @param {String} name - data-btn of the button: "bold", "italic", "link", "image", "h1", "h2", "h3", "ul", "ol"
   */
  applyFormat( name ) {
    let input = this.editing.input;
    let start = input.selectionStart;
    let end = input.selectionEnd;
    let selected = input.value.slice( start, end );

    let wrap = ( before, after, placeholder, cursorInAfter ) => {
      let inner = selected || placeholder;
      let text = before + inner + after;
      if ( selected ) {
        // the cursor goes after the whole text, or into its end part (e.g. the url of a link)
        let cursor = start + before.length + inner.length + ( cursorInAfter === undefined ? after.length : cursorInAfter );
        this.replaceInputRange( start, end, text, cursor, cursor );
      } else {
        this.replaceInputRange( start, end, text, start + before.length, start + before.length + inner.length );
      }
    };

    let prefixLines = prefix => {
      let lineStart = input.value.lastIndexOf( "\n", start - 1 ) + 1;
      let lineEnd = input.value.indexOf( "\n", Math.max( end - ( end > start && input.value[ end - 1 ] === "\n" ? 1 : 0 ), start ) );
      if ( lineEnd < 0 ) {
        lineEnd = input.value.length;
      }
      let text = input.value.slice( lineStart, lineEnd ).split( "\n" ).map( line => prefix + " " + line ).join( "\n" );
      this.replaceInputRange( lineStart, lineEnd, text, lineStart + text.length, lineStart + text.length );
    };

    switch ( name ) {
      case "bold": wrap( "**", "**", "text" ); break;
      case "italic": wrap( "*", "*", "text" ); break;
      case "link": wrap( "[", "](http://)", "text", 9 ); break;
      case "image": wrap( "![", "](http://)", "text", 9 ); break;
      case "h1": prefixLines( "#" ); break;
      case "h2": prefixLines( "##" ); break;
      case "h3": prefixLines( "###" ); break;
      case "ul": prefixLines( "*" ); break;
      case "ol": prefixLines( "1." ); break;
    }
  }

  /**
   * Replaces a part of the edited block's text; done by "insertText" so that the browser's own undo (Ctrl+Z) works
   */
  replaceInputRange( start, end, text, selectionStart, selectionEnd ) {
    let input = this.editing.input;
    input.focus();
    input.setSelectionRange( start, end );
    let done = false;
    try {
      done = document.execCommand( "insertText", false, text );
    } catch ( e ) {
      done = false;
    }
    if ( !done ) {
      input.setRangeText( text, start, end, "end" );
      input.dispatchEvent( new Event( "input" ) );
    }
    input.setSelectionRange( selectionStart, selectionEnd );
  }

  insertBlock( index, raw ) {
    this.doc.blocks.splice( index, 0, { type: "paragraph", raw: raw } );
    window.UTILS.MDBlockTokenizer.normalize( this.doc, [ index - 1, index ] );
    this.applyDoc();
    this.focusBlock( index );
  }

  replaceBlock( index, raw ) {
    this.doc.blocks[ index ].raw = raw;
    window.UTILS.MDBlockTokenizer.normalize( this.doc, [ index - 1, index ] );
    this.applyDoc();
    this.focusBlock( index );
  }

  removeBlock( index ) {
    this.doc.blocks.splice( index, 1 );
    window.UTILS.MDBlockTokenizer.normalize( this.doc, [ index - 1 ] );
    this.applyDoc();
    this.focusBlock( Math.min( index, this.doc.blocks.length - 1 ) );
  }

  /**
   * Writes the order of block elements left by dragging into the source
   * @param {Element} moved - the dragged block
   */
  onSortEnd( moved ) {
    let order = [ ...this.viewport.querySelectorAll( ":scope > .md-block:not(.md-block--new):not(.md-block--dragged)" ) ].map( el => parseInt( el.dataset.index, 10 ) );
    if ( order.every( ( index, i ) => index === i ) ) {
      return;
    }
    window.UTILS.MDBlockTokenizer.reorder( this.doc, order );
    this.applyDoc();
    moved.focus( { preventScroll: true } );
  }

  /**
   * Moves the block one position up or down (Alt+Up / Alt+Down)
   * @param {Number} index
   * @param {Number} delta - -1 or 1
   */
  moveBlock( index, delta ) {
    let target = index + delta;
    if ( target < 0 || target >= this.doc.blocks.length ) {
      return;
    }
    let order = this.doc.blocks.map( ( block, i ) => i );
    order[ index ] = target;
    order[ target ] = index;
    window.UTILS.MDBlockTokenizer.reorder( this.doc, order );
    this.applyDoc();
    this.focusBlock( target );
    let el = this.viewport.querySelector( ":scope > .md-block[data-index='" + target + "']" );
    if ( el ) {
      el.scrollIntoView( { block: "nearest" } );
    }
  }

  focusBlock( index ) {
    let el = this.viewport.querySelector( ":scope > .md-block[data-index='" + index + "']" );
    if ( el ) {
      el.focus( { preventScroll: true } );
    }
  }

  // ---------------------------------------------------------------- the "add block" menu

  openMenu( button, index ) {
    this.closeMenu();
    let menu = document.createElement( "div" );
    menu.className = "md-blocks__menu";
    window.UTILS.MDBlockEditor.newBlockTemplates.forEach( item => {
      let btn = document.createElement( "button" );
      btn.type = "button";
      btn.className = "md-blocks__menu-item";
      btn.textContent = this.texts.types[ item.type ] || item.type;
      btn.addEventListener( "click", () => {
        this.closeMenu();
        this.addBlock( index, item.template );
      } );
      menu.appendChild( btn );
    } );
    this.element.appendChild( menu );
    // placed under the button, inside the scrolled container;
    // a hidden button (shown on hover only, e.g. when triggered from keyboard) is replaced by its block
    let anchor = button.getClientRects().length ? button : ( button.closest( ".md-block" ) || this.appendButton );
    let rect = anchor.getBoundingClientRect();
    let parentRect = this.element.getBoundingClientRect();
    menu.style.top = ( rect.bottom - parentRect.top + this.element.scrollTop + 4 ) + "px";
    menu.style.left = Math.max( 4, rect.left - parentRect.left + rect.width / 2 - menu.offsetWidth / 2 ) + "px";
    this.menu = menu;
    menu.scrollIntoView( { block: "nearest" } );
    menu.querySelector( "button" ).focus( { preventScroll: true } );
  }

  closeMenu() {
    if ( this.menu ) {
      this.menu.remove();
      this.menu = null;
    }
  }

  // ---------------------------------------------------------------- events

  /**
   * Clicks inside rendered blocks must not leave the page (links in content, images opening in a gallery...),
   * interactive components (tabs, collapses, sliders) keep working
   */
  onClick( e ) {
    let target = e.target;
    if ( target.closest( ".md-block__editor" ) ) {
      return;
    }
    if ( this.editing && target.closest( ".js--add-block, .js--edit-block, .js--show-source, .js--remove-block, .js--retry" ) ) {
      // the edited block is saved first, it may change positions of the other blocks
      this.commitEdit();
      if ( !target.isConnected ) {
        e.preventDefault();
        return;
      }
    }
    let blockEl = target.closest( ".md-block" );
    let index = blockEl ? parseInt( blockEl.dataset.index, 10 ) : null;

    if ( target.closest( ".js--add-block" ) ) {
      this.openMenu( target.closest( ".js--add-block" ), blockEl ? index + 1 : this.doc.blocks.length );
    } else if ( !blockEl ) {
      return;
    } else if ( target.closest( ".js--edit-block" ) ) {
      this.editBlock( index );
    } else if ( target.closest( ".js--show-source" ) ) {
      this.showSource( index );
    } else if ( target.closest( ".js--remove-block" ) ) {
      this.removeBlock( index );
    } else if ( target.closest( ".js--edit-object" ) ) {
      this.refreshIobjectsOnFocus = true;
      return; // the link opens in a new tab
    } else if ( target.closest( ".js--retry" ) ) {
      this.refresh();
    } else if ( !target.closest( "a[href]" ) ) {
      return;
    }
    e.preventDefault();
  }

  onDblClick( e ) {
    let blockEl = e.target.closest( ".md-block" );
    if ( !blockEl || blockEl.classList.contains( "md-block--editing" ) || e.target.closest( ".md-block__toolbar, .md-block__add" ) ) {
      return;
    }
    e.preventDefault();
    window.getSelection().removeAllRanges(); // a double-click selects a word
    this.editBlock( parseInt( blockEl.dataset.index, 10 ) );
  }

  onKeyDown( e ) {
    if ( e.key === "Escape" && this.menu ) {
      this.closeMenu();
      return;
    }
    if ( e.target.closest( "textarea, input, select, [contenteditable]" ) ) {
      return;
    }
    let ctrl = e.ctrlKey || e.metaKey;
    let key = e.key.toLowerCase();
    if ( ctrl && key === "z" ) {
      e.preventDefault();
      if ( e.shiftKey ) {
        this.redo();
      } else {
        this.undo();
      }
    } else if ( ctrl && key === "y" ) {
      e.preventDefault();
      this.redo();
    } else if ( e.altKey && ( e.key === "ArrowUp" || e.key === "ArrowDown" ) && e.target.classList.contains( "md-block" ) ) {
      e.preventDefault();
      this.moveBlock( parseInt( e.target.dataset.index, 10 ), e.key === "ArrowUp" ? -1 : 1 );
    } else if ( e.key === "Enter" && e.target.classList.contains( "md-block" ) ) {
      e.preventDefault();
      this.editBlock( parseInt( e.target.dataset.index, 10 ) );
    }
  }

  /**
   * Switches to "Edit" and selects the source of the given block
   * @param {Number} index
   */
  showSource( index ) {
    let block = this.doc.blocks[ index ];
    // clicking the editor's own button switches the view as the user would do it
    this.container.querySelector( ".btn-edit" ).click();
    if ( !block ) {
      return;
    }
    let editor = this.aceEditor;
    let Range = window.ace.require( "ace/range" ).Range;
    let lastRow = block.line + block.lineCount - 1;
    editor.selection.setRange( new Range( block.line, 0, lastRow, editor.getSession().getLine( lastRow ).length ) );
    editor.scrollToLine( block.line, true, true );
    editor.focus();
  }
};

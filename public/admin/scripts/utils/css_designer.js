/**
 * CSS designer
 * Inserts a Custom CSS block (a <style> element in the Markdown source) or edits it.
 * The CSS is edited in tabs: rules for all widths and one tab for every breakpoint of the web's grid,
 * whose CSS is written as @media (min-width: ...) (see md_css_model.js); or as a whole ("Whole code").
 * The breakpoints come from the setting MARKDOWN_CSS_BREAKPOINTS (rendered in _css_designer_modal.tpl),
 * they must be the same as the web's ones.
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of <style> blocks:
 * in the dialog, or in the inspector of the block view (inspect()) where changes apply as they are typed.
 * In previews the CSS is scoped to the preview (see md_preview_styles.js).
 *
 * Usage:
 *
 *   if( document.getElementById( "cssdesigner_modal" ) ) {
 *     new UTILS.CssDesigner();
 *   }
 *
 * Dependencies:
 *
 * md_css_model.js, md_editor_inserts.js, Ace (with its CSS mode, loaded from ace.config's basePath)
 * _css_designer_modal.tpl (the dialog, the template of the fields, the breakpoints)
 * dialog.js (a native <dialog>), Bootstrap 4+ (styles)
 */
window.UTILS = window.UTILS || { };

/**
 * The fields of a Custom CSS block: tabs of the sections and an Ace editor
 */
window.UTILS.CssFields = class {

  static count = 0;

  // instances, so that the Ace editors of the removed ones are destroyed (an inspector is rendered again and again)
  static instances = [];

  /**
   * @param {Element} container - the fields are rendered into it
   * @param {Object} options - { onChange, minLines, maxLines }
   */
  constructor( container, options ) {
    options = options || {};
    let self = window.UTILS.CssFields;
    self.instances = self.instances.filter( fields => {
      if ( fields.element.isConnected ) {
        return true;
      }
      fields.editor.destroy();
      return false;
    } );
    self.instances.push( this );

    this.texts = window.cssdesignerTexts;
    this.breakpoints = window.cssdesignerConfig.breakpoints;
    this.options = options;
    let fields = document.getElementById( "cssdesigner_fields" ).content.cloneNode( true );
    let q = selector => fields.querySelector( selector );
    this.element = q( ".css-designer" );
    this.tabsElement = q( ".js--tabs" );
    this.wholeCheckbox = q( "input.js--whole" );
    this.note = q( ".js--note" );
    let id = "cssdesigner_" + ( ++self.count );
    this.wholeCheckbox.id = id + "_whole";
    q( "label.js--whole-label" ).htmlFor = this.wholeCheckbox.id;

    // a tab for every section: "" (all widths) and the breakpoints
    this.sections = [ { name: "", label: this.texts.allWidths, title: this.texts.allWidthsTitle } ].concat( this.breakpoints.map( bp => ( {
      name: bp.name,
      label: bp.name,
      min: bp.min,
      title: this.texts.breakpointTitle.replace( "%min%", bp.min ),
    } ) ) );
    this.tabs = this.sections.map( section => {
      let tab = document.createElement( "button" );
      tab.type = "button";
      tab.className = "css-designer__tab";
      tab.setAttribute( "role", "tab" );
      tab.title = section.title;
      tab.textContent = section.label;
      if ( section.min !== undefined ) {
        let small = document.createElement( "small" );
        small.textContent = "≥" + section.min;
        tab.appendChild( small );
      }
      tab.addEventListener( "click", () => this.showSection( section.name ) );
      this.tabsElement.appendChild( tab );
      return tab;
    } );

    this.filling = false;
    this.editor = window.ace.edit( q( ".js--editor" ) );
    this.editor.setTheme( "ace/theme/tomorrow" );
    this.editor.setOptions( {
      minLines: options.minLines || 8,
      maxLines: options.maxLines || 24,
      showPrintMargin: false,
      fontSize: "13px",
      tabSize: 2,
      useSoftTabs: true,
    } );
    this.editor.renderer.setScrollMargin( 4, 4 );
    let session = () => {
      let s = window.ace.createEditSession( "", "ace/mode/css" );
      s.setTabSize( 2 );
      s.setUseSoftTabs( true );
      s.setUseWrapMode( true );
      s.on( "change", () => this.changed() );
      return s;
    };
    this.sessions = {};
    this.sections.forEach( section => {
      this.sessions[ section.name ] = session();
    } );
    this.wholeSession = session();
    this.current = "";
    this.headers = {};
    this.whole = false;

    this.wholeCheckbox.addEventListener( "change", () => this.setWhole( this.wholeCheckbox.checked ) );
    container.appendChild( fields );
  }

  /**
   * Fills the fields from a model (UTILS.MDCssModel.parse()), or empty ones for a new block (null)
   * @param {Object|null} model
   */
  fill( model ) {
    this.filling = true;
    let sections = model ? model.sections : {};
    this.headers = model ? model.headers : {};
    this.sections.forEach( section => {
      this.sessions[ section.name ].setValue( ( sections && sections[ section.name ] ) || "" );
    } );
    this.wholeSession.setValue( model ? model.css : "" );
    this.filling = false;
    this.splittable = !model || !!model.sections;
    this.setWhole( !this.splittable );
    // the first section with some CSS
    let first = this.sections.find( section => this.sessions[ section.name ].getValue().trim() );
    this.showSection( first ? first.name : "" );
    this.updateTabs();
  }

  showSection( name ) {
    this.current = name;
    if ( !this.whole ) {
      this.editor.setSession( this.sessions[ name ] );
    }
    this.tabs.forEach( ( tab, i ) => {
      let active = this.sections[ i ].name === name;
      tab.classList.toggle( "active", active );
      tab.setAttribute( "aria-selected", active ? "true" : "false" );
    } );
  }

  /**
   * Switches between the tabs and the whole code; the whole code is split into the tabs only when it can be
   * @param {Boolean} whole
   */
  setWhole( whole ) {
    let model = window.UTILS.MDCssModel;
    this.note.textContent = "";
    this.note.hidden = true;
    if ( whole && !this.whole ) {
      this.filling = true;
      if ( this.splittable ) {
        this.wholeSession.setValue( this.joinedSections() );
      }
      this.filling = false;
    } else if ( !whole && this.whole ) {
      let split = model.splitSections( this.wholeSession.getValue(), this.breakpoints );
      if ( !split ) {
        whole = true;
        this.note.textContent = this.texts.notSplittable;
        this.note.hidden = false;
      } else {
        this.filling = true;
        this.headers = split.headers;
        this.sections.forEach( section => this.sessions[ section.name ].setValue( split.sections[ section.name ] || "" ) );
        this.filling = false;
        this.splittable = true;
        this.updateTabs();
      }
    } else if ( whole && !this.splittable ) {
      this.note.textContent = this.texts.notSplittable;
      this.note.hidden = false;
    }
    this.whole = whole;
    this.wholeCheckbox.checked = whole;
    this.tabsElement.hidden = whole;
    this.editor.setSession( whole ? this.wholeSession : this.sessions[ this.current ] );
  }

  joinedSections() {
    let sections = {};
    this.sections.forEach( section => {
      sections[ section.name ] = this.sessions[ section.name ].getValue();
    } );
    return window.UTILS.MDCssModel.joinSections( sections, this.breakpoints, this.headers );
  }

  // the tabs having some CSS are marked
  updateTabs() {
    this.tabs.forEach( ( tab, i ) => {
      tab.classList.toggle( "css-designer__tab--filled", !!this.sessions[ this.sections[ i ].name ].getValue().trim() );
    } );
  }

  changed() {
    if ( this.filling ) {
      return;
    }
    this.updateTabs();
    if ( this.options.onChange ) {
      this.options.onChange();
    }
  }

  /**
   * @returns {String} the CSS
   */
  value() {
    return this.whole ? this.wholeSession.getValue() : this.joinedSections();
  }

  focus() {
    this.editor.resize( true );
    this.editor.focus();
  }
};

window.UTILS.CssDesigner = class {
  texts = window.cssdesignerTexts; // UI strings (rendered in template)

  constructor() {
    if ( window.UTILS.CssDesigner.instance ) {
      window.UTILS.CssDesigner.instance.attachToolbarButtons();
      return window.UTILS.CssDesigner.instance;
    }
    window.UTILS.CssDesigner.instance = this;

    this.modal = document.getElementById( "cssdesigner_modal" );
    this.fields = new window.UTILS.CssFields( this.modal.querySelector( "#cssdesigner" ), { minLines: 16, maxLines: 30 } );
    this.saveBtn = this.modal.querySelector( "#cssdesigner_save_btn" );
    this.save = null; // callback given by open()
    this.model = null; // the edited block

    this.attachToolbarButtons();
    this.saveBtn.addEventListener( "click", () => this.saveCode() );
  }

  static breakpoints() {
    return window.cssdesignerConfig ? window.cssdesignerConfig.breakpoints : [];
  }

  /**
   * Registers the designer in UTILS.MDEditorInserts (adds "Custom CSS" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "cssdesigner",
      text: "<span class=\"fa-solid fa-paintbrush\" aria-hidden=\"true\"></span> " + this.texts.title,
      title: this.texts.title,
      modal: "#cssdesigner_modal",
      inline: false,
      blockType: "html",
      parse: raw => window.UTILS.MDCssModel.parse( raw, window.UTILS.CssDesigner.breakpoints() ),
      open: options => this.open( options ),
      inspect: ( container, options ) => this.inspect( container, options ),
    } );
  }

  /**
   * Fills the dialog with the given block, or empty for a new one
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    this.model = options.raw ? window.UTILS.MDCssModel.parse( options.raw, window.UTILS.CssDesigner.breakpoints() ) : null;
    this.fields.fill( this.model );
    this.saveBtn.textContent = this.model ? this.texts.save : this.texts.insert;
    // the editor gets its size once the dialog is shown
    setTimeout( () => this.fields.focus(), 50 );
  }

  saveCode() {
    let css = this.fields.value();
    if ( !this.model && !css.trim() ) {
      this.fields.focus();
      return;
    }
    let code = this.model ? window.UTILS.MDCssModel.build( this.model, css ) : window.UTILS.MDCssModel.create( css );
    if ( this.save ) {
      this.save( code );
    }
  }

  /**
   * The fields in the inspector of the block view; every change is passed to change() at once
   * @param {Element} container
   * @param {Object} options - { raw, change }
   * @returns {Boolean} false when the block can't be edited here
   */
  inspect( container, options ) {
    let model = window.UTILS.MDCssModel.parse( options.raw, window.UTILS.CssDesigner.breakpoints() );
    if ( !model || !document.getElementById( "cssdesigner_fields" ) ) {
      return false;
    }
    let fields = new window.UTILS.CssFields( container, {
      onChange: () => options.change( window.UTILS.MDCssModel.build( model, fields.value() ) ),
    } );
    fields.fill( model );
    return true;
  }
};

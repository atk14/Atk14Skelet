/**
 * Layout designer
 * Editor for creating Boostrap 4+ responsive layout columns structures
 * Outputs DrInk shortcodes for Markdown or pure HTML code
 * Adds button to MD editor
 * 
 * Usage:
 * 
 * if( document.getElementById( "layout-designer" ) ) {
 *  new UTILS.LayoutDesigner();
 * }
 * 
 * Dependencies:
 * 
 * _layout_designer.scss
 * _layout_designer.tpl
 * Bootstrap 4+
 * 
 */
window.UTILS = window.UTILS || { };

window.UTILS.LayoutDesigner = class {
  designer;                               // container
  countSelector;                          // HTML select element for number of columns
  rowXL;                                  // editors for all breakpoints
  rowLG;
  rowMD;
  rowSM;
  rowXS;
  designerModal;                          // modal selector
  columnCount = 0;                        // current number of columns
  rows = [];                              // array for editors
  copyMDButton;                           // button to copy Markdown code
  copyHTMLButton;                         //  button to copy HTML code
  storage = sessionStorage;               // storage to use - sessionStorage or LocalStorage
  texts = window.layoutDesignerTexts;     // UI strings (rendered in template)

  /**
   * Constructor
   */
  constructor() {
    // assign UI controls
    this.designer = document.getElementById( "layout-designer" );
    this.designerModal = document.getElementById( "layout_designer_modal" );
    this.countSelector = this.designer.querySelector( "#layout_designer_column_count" );
    this.copyMDButton = this.designerModal.querySelector( "#copy_md_btn" );
    this.copyHTMLButton = this.designerModal.querySelector( "#copy_html_btn" );
    this.resetBtn = this.designerModal.querySelector( "#reset_btn" );

    // create Layout button in toolbars of all MD editors
    document.querySelectorAll( ".md-container" ).forEach( el => {
      this.createToolbarButton( el );
    } );
    
    // Create editors for each breakpoint
    this.rowXL = new window.UTILS.LayoutDesignerRow( "rowXL", this.texts.titleXL, false );
    this.rowLG = new window.UTILS.LayoutDesignerRow( "rowLG", this.texts.titleLG, false );
    this.rowMD = new window.UTILS.LayoutDesignerRow( "rowMD", this.texts.titleMD, false );
    this.rowSM = new window.UTILS.LayoutDesignerRow( "rowSM", this.texts.titleSM, false );
    this.rowXS = new window.UTILS.LayoutDesignerRow( "rowXS", this.texts.titleXS, true );

    // Put them to array for easy access
    this.rows = [ this.rowXL, this.rowLG, this.rowMD, this.rowSM, this.rowXS ];

    // Handler for colummn count select
    this.countSelector.addEventListener( "change", this.changeColumnCount.bind( this ) );

    // Handler for close btn
    // TODO: better would be to have handler on modal close
    this.designerModal.querySelector( "[data-bs-dismiss='modal']" ).addEventListener( "click", () => {
      this.save();
    } );

    // Handler for copy Markdown button
    this.copyMDButton.addEventListener( "click", () => {
      this.codeToClipboard( "markdown" );
    } );

    // Handler for copy HTML button
    this.copyHTMLButton.addEventListener( "click", () => {
      this.codeToClipboard( "html" );
    } );

    // Handler for copy reset button
    this.resetBtn.addEventListener( "click", () => {
      this.reset();
    } )
  }

  // 
  /**
   * Create Layout button in MD editor toolbar, set click handler
   * @param {*} el - MD editor container (typically ".md-container")
   */
  createToolbarButton( el ) {
    let toolbar = el.querySelector( ".md-toolbar .btn-toolbar" );
    let btn = document.createElement( "button" );
    btn.type = "button";
    btn.className = "md-btn btn btn-default md-btn--icon";
    btn.title = "Insert Layout";
    btn.innerHTML = "<i class=\"fa-solid fa-table-columns\"></i> Layout";
    btn.setAttribute ("data-bs-toggle", "modal");
    btn.setAttribute ("data-toggle", "modal");
    btn.setAttribute ("data-bs-target", "#layout_designer_modal");
    btn.setAttribute ("data-target", "#layout_designer_modal");
    let div = document.createElement( "div" );
    div.className = "button-group";
    div.appendChild( btn );
    toolbar.appendChild( div );
    btn.addEventListener( "click", this.initModal.bind( this ) );
  }

  /**
   * Sets column count for editors
   */
  changeColumnCount() {
    let newCount = parseInt( this.countSelector.value, 10 );
    let span =  Math.floor( 12 / newCount );
    let colsToAdd = newCount - this.columnCount;
    if ( colsToAdd > 0) {
      for ( let i = 1; i <= colsToAdd; i++ ) {
        this.rows.forEach( (row)=> { row.addCell(span); } );
      }
    } else if ( colsToAdd < 0 ) {
      for ( let i = 1; i <= -colsToAdd; i++ ) {
        this.rows.forEach( (row)=> { row.removeLastCell(); } );
      }
    }
    this.rows.forEach( (row)=> { row.evenWidths(); } );
    this.columnCount = newCount;
  }

  /**
   * Initializer
   */
  initModal() {
    // If there is saved layout import it otherwise just reset
    if( this.storage.getItem( "layout_designer_layout" ) ) {
      this.importSizes( JSON.parse( this.storage.getItem( "layout_designer_layout" ) ) );
    } else {
      this.reset();
    }
  }

  /**
   * Creates object with layout description
   * @returns {Object} 
   */
  exportSizes() {
    let exportObject = {};
    exportObject.xl = { sizes: this.rowXL.sizes, key: "xl" };
    exportObject.lg = { sizes: this.rowLG.sizes, key: "lg" };
    exportObject.md = { sizes: this.rowMD.sizes, key: "md" };
    exportObject.sm = { sizes: this.rowSM.sizes, key: "sm" };
    exportObject.xs = { sizes: this.rowXS.sizes, key: "xs" };
    exportObject.cellsNumber = this.columnCount;
    return exportObject;
  }

  /**
   * Generates HTML or Markdown code
   * @param {*} style - "html"|"markdown"
   * @returns {String} - generated code
   */
  generateCode( style ) {
    style = style || "markdown";
    let data = this.exportSizes();
    let cellCode = "";
    let prefix = "  ";
    let suffix = "\n";
    let output;
    let hidden = false;
    // make sure order is correct
    let breakpoints = [ data.xs, data.sm, data.md, data.lg, data.xl ];
    for( let i = 0; i < this.columnCount; i++ ) {
      let cellClass = "";
      breakpoints.forEach( breakpoint => {
        let span = breakpoint.sizes[ i ];
        if( span === 0 ) {
          hidden = true;
          if( breakpoint.key === "xs" ) {
            cellClass += "d-none ";
          }
          cellClass += `d-${breakpoint.key}-none `;
        } else {
          if( breakpoint.key === "xs" ) {
            cellClass += `col-${span} `;
          }
          cellClass += `col-${breakpoint.key}-${span} `;
          if( hidden ) {
            cellClass += `d-${breakpoint.key}-block `;
            hidden = false;
          }
        }
      } );
      cellCode += prefix;
      if( style === "markdown" ) {
        cellCode += `[col class="${cellClass}" defaultclasses=0]${i+1}[/col]`;
      } else if ( style === "html" ) {
        cellCode += `<div class="${cellClass}">${i+1}</div>`;
      }
      cellCode += suffix;
    }
    
    if( style === "markdown" ) {
      output = `[row]${suffix}${cellCode}[/row]`;
    } else if ( style === "html" ) {
      output = `<div class="row">${suffix}${cellCode}</div>`;
    }
    return output;
  }

  /**
   * Imports and displays data generated by exportSizes
   * Used when loading saved data from storage
   * @param {Object} 
   */
  importSizes( data ) {
    this.reset();
    this.countSelector.selectedIndex = data.cellsNumber - 1;
    this.changeColumnCount();
    this.rowXL.sizes = data.xl.sizes;
    this.rowLG.sizes = data.lg.sizes;
    this.rowMD.sizes = data.md.sizes;
    this.rowSM.sizes = data.sm.sizes;
    this.rowXS.sizes = data.xs.sizes;
  }

  /**
   * Resets editor to default state
   */
  reset(){
    this.rows.forEach( (row)=> { row.clear(); } );
    this.columnCount = 0;
    this.countSelector.selectedIndex = 1; // Default to 2 columns
    this.changeColumnCount();
  }

  /**
   * Export code in html or markdown, copies to clipboard, saves layout to storage
   * @param {String} style - "html"|"markdown"
   */
  codeToClipboard( style ) {
    this.setClipboard( this.generateCode( style ) );
    this.save();
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
  
  /**
   * Save current layout to storage
   */
  save(){
    this.storage.setItem( "layout_designer_layout", JSON.stringify( this.exportSizes() ) );
  }

};





/**
 * Helper class for single breakpoint editor called row editor
 */
window.UTILS.LayoutDesignerRow = class {
  rowEditor;              // html of this class
  cellsContainer;         // html container for displaying cells
  cells = [];             // array to store cells
  hiddenCellsContainer;   // html container to display hidden cells
  stacked;                // if true all cells will span full width by default
  btnCopyUp;              // copy button
  btnCopyDown;            // paste button
  texts = window.layoutDesignerTexts; // UI strings (rendered in template)
  
  /**
   * 
   * @param {*} id - id attribute for editor
   * @param {*} title - html title for editor
   * @param {*} stacked - if true, all cells will span full width by default
   */
  constructor( id, title, stacked ) {
    // create row
    this.rowEditor = document.createElement( "div" );
    document.querySelector( "#layout_designer_modal .editor-container").appendChild( this.rowEditor );

    // editor content from html template tag
    let editorFragment = document.querySelector( "#layout_designer_row" ).content.cloneNode( true );
    this.rowEditor.appendChild( editorFragment );
    this.rowEditor.setAttribute( "id", id );

    // reference html nodes
    this.cellsContainer = this.rowEditor.querySelector( ".row-edit-area" );
    this.hiddenCellsContainer = this.rowEditor.querySelector( ".hidden-cells-container" );
    this.btnCopy = this.rowEditor.querySelector( ".js--btn-copy" );
    this.btnPaste = this.rowEditor.querySelector( ".js--btn-paste" );

    // set title
    this.rowEditor.querySelector( ".js--row-title" ).innerHTML = title;
   
    this.cells = [];
    this.stacked = stacked;
    this.copyPasteHandlers();
  }

  /**
   * Add new cell
   * @param {int} colspan 1-12
   * @returns {LayoutDesignerCell}
   */
  addCell( colspan ) {
    if( this.stacked ) {
      colspan = 12;
    }
    let cell = new window.UTILS.LayoutDesignerCell( this, colspan, this.cells.length + 1 );
    this.cells.push( cell );
    this.cellsContainer.appendChild( cell.element );
    return cell;
  }

  /**
   * Remove last cell
   */
  removeLastCell() {
    let lastCell = this.cells.pop();
    if ( lastCell ) {
      lastCell.destroy();
    }
  }

  /**
   * Remove all cells
   */
  clear() {
    this.cells.forEach( cell => cell.destroy() );
    this.cells = [];
    this.cellsContainer.innerHTML = "";
  }

  /**
   * Set even widths to cells to fit in one row
   */
  evenWidths() {
    let evenspan = Math.floor( 12 / this.cells.length );
    if( this.stacked ) {
      evenspan = 12;
    }
    this.cells.forEach( cell => {
      cell.span = evenspan;
    } );
  }

  /**
   * Getter to get all cells spans
   * @returns {Array}
   */
  get sizes(){
    let widths = []
    this.cells.forEach(
      ( cell ) => {
        widths.push( cell.span );
      }
    )
    return widths;
  }

  /**
   * Setter to set cell widths
   * @param {Array} newsizes 
   */
  set sizes ( newsizes ) {
    if( newsizes.length !== this.cells.length ) {
      alert( this.texts.wrongPaste );
      return;
    }
    for( let i=0; i < newsizes.length; i++ ) {
      this.cells[ i ].span = newsizes[ i ];
    }
  }

  /**
   * Handlers for copy/paste breakpoint layout
   */
  copyPasteHandlers() {
    this.btnCopy.addEventListener( "click", ()=>{
      window.copiedRow = this.sizes;
    } );
    this.btnPaste.addEventListener( "click", ()=>{
      this.sizes = window.copiedRow;
    } );
  }
};



/**
 * Helper class representing single cell (column)
 */
window.UTILS.LayoutDesignerCell = class {
  element;                    // this html 
  #span;                      // cell span 1-12 
  cellNumber;                 // order number of this cell
  hiddenCellAvatar = null;    // graphic representation of this cell when hidden
  previousSpan;

  /**
   * 
   * @param {LayoutDesignerRow} parent 
   * @param {int} colspan -  cell span 
   * @param {int} cellNumber - cell order number 
   */
  constructor( parent, colspan, cellNumber ) {
    this.parent = parent;
    this.cellNumber = cellNumber;
    this.element = document.createElement( "div" );
    // get UI controls from html template tag
    let controls = document.querySelector( "#layout_designer_cell_controls" ).content.cloneNode( true );
    this.element.appendChild( controls );

    // display cell number
    this.element.querySelector( ".cellnumber" ).innerHTML = cellNumber;

    // set UI handlers
    this.element.querySelector( ".js-span-plus" ).addEventListener( "click", () => {
      if ( this.#span < 12 ) {
        this.span += 1;
      }
    } );
    this.element.querySelector( ".js-span-minus" ).addEventListener( "click", () => {
      if ( this.#span > 1 ) {
        this.span -= 1;
      }
    } );
    this.element.querySelector( ".js-hide-cell" ).addEventListener( "click", () => {
        this.span =0;
    } );

    // set initial span
    this.span = colspan;
  }

  /**
   * Setter for cell span
   * @param {int} span - 1-12
   */
  set span ( n ) {
    this.previousSpan = this.#span;
    this.#span = n;
    this.element.setAttribute( "data-span", this.#span );
    this.element.className = "cellspan-" + this.#span;
    this.element.querySelector( ".js-span-display" ).innerHTML = this.#span;
    if( n === 0 ) {
      if( !this.hiddenCellAvatar ){
        this.createHiddenCellAvatar();
      }
    } else {
      if( this.hiddenCellAvatar ){
        this.destroyHiddenCellAvatar();
        this.hiddenCellAvatar = null;
      }
    }
  }

  /**
   * getter for cell span
   * @returns {int} - cell span
   */
  get span() {
    return this.#span;
  }

  /**
   * Create hidden cell graphics representation
   */
  createHiddenCellAvatar() {
    let hiddenCellAvatar = document.createElement( "div" );
    // get markup from html template
    let content = document.querySelector( "#hidden_cell_avatar" ).content.cloneNode( true );
    hiddenCellAvatar.className = "hidden_cell_avatar";
    hiddenCellAvatar.appendChild( content );
    hiddenCellAvatar.querySelector( ".cellnumber" ).innerHTML = this.cellNumber;
    hiddenCellAvatar.style.order = this.cellNumber;
    this.hiddenCellAvatar = hiddenCellAvatar;
    
    this.parent.hiddenCellsContainer.appendChild( hiddenCellAvatar );

    hiddenCellAvatar.querySelector( ".js-show-cell" ).addEventListener( "click", () => {
      this.span = this.previousSpan;
      this.destroyHiddenCellAvatar();
    } );

  }

  /**
   * Destroys hidden cell avatar (when cell is unhidden or is deleted)
   */
  destroyHiddenCellAvatar() {
    if( this.hiddenCellAvatar && this.parent.hiddenCellsContainer.contains( this.hiddenCellAvatar ) ) {
      this.parent.hiddenCellsContainer.removeChild( this.hiddenCellAvatar );
    }
    this.hiddenCellAvatar = null;
  }

  /**
   * Destroy this cell
   */
  destroy() {
    if( this.hiddenCellAvatar ){
      this.destroyHiddenCellAvatar( this.hiddenCellAvatar );
    }
    this.parent.cellsContainer.removeChild( this.element );
  }
};
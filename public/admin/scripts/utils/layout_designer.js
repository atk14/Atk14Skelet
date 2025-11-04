const size = require("gulp-size");

window.UTILS = window.UTILS || { };

window.UTILS.LayoutDesigner = class {

  //toolbar;
  //toolbarButton;
  designer;
  countSelector;
  rowXL;
  rowLG;
  rowMD;
  rowSM;
  rowXS;
  designerModal;
  columnCount = 0;
  rows = [];

  constructor() {
    this.designer = document.getElementById( "layout-designer" );
    this.designerModal = document.getElementById( "layout_designer_modal" );
    this.countSelector = this.designer.querySelector( "#layout_designer_column_count" );

    
    document.querySelectorAll( ".md-container" ).forEach( el => {
      this.createToolbarButton( el );
    } );
    
    //this.rowXL = new window.UTILS.LayoutDesignerRow( this.designer.querySelector( "#row_xl" ), false );
    this.rowXL = new window.UTILS.LayoutDesignerRow( "rowXL", false );
    this.rowLG = new window.UTILS.LayoutDesignerRow( "rowLG", false );
    this.rowMD = new window.UTILS.LayoutDesignerRow( "rowMD", false );
    this.rowSM = new window.UTILS.LayoutDesignerRow( "rowSM", false );
    this.rowXS = new window.UTILS.LayoutDesignerRow( "rowXS", true );

    this.rows = [ this.rowXL, this.rowLG, this.rowMD, this.rowSM, this.rowXS ];

    this.countSelector.addEventListener( "change", this.changeColumnCount.bind( this ) );

    this.designerModal.addEventListener( "show.bs.modal", () => {
      this.initModal();
    } );
  }

  createToolbarButton( el ) {
    // Initialization code for LayoutDesigner
    let toolbar = el.querySelector( ".md-toolbar .btn-toolbar" );
    console.log( "LayoutDesigner initialized with parent editor:" );
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

  changeColumnCount() {
    let newCount = parseInt( this.countSelector.value, 10 );
    let span =  Math.floor( 12 / newCount );
    let colsToAdd = newCount - this.columnCount;
    console.log( "Changing column count to:", newCount, "span", span, "colsToAdd", colsToAdd );
    if ( colsToAdd > 0) {
      for ( let i = 1; i <= colsToAdd; i++ ) {
        this.rows.forEach( (row)=> { row.addCell(span); } );
        console.log( "Added column", i );
      }
    } else if ( colsToAdd < 0 ) {
      for ( let i = 1; i <= -colsToAdd; i++ ) {
        this.rows.forEach( (row)=> { row.removeLastCell(); } );
        console.log( "Remove column", i );
      }
    }
    this.rows.forEach( (row)=> { row.evenWidths(); } );
    this.columnCount = newCount;
  }

  initModal() {
    // Code on show modal dialog for layout selection
    this.countSelector.selectedIndex = 1; // Default to 2 columns
    this.rows.forEach( (row)=> { row.clear(); } );
    this.columnCount = 0;
    console.log( "Showing layout selection modal." );
    /*let count = parseInt( this.countSelector.value, 10 );
    let span =  Math.floor( 12 / count );
    console.log( "Selected column count:", count, "span", span );
    for ( let i = 1; i <= count; i++ ) {
      this.rowXL.addCell( span );
    }*/
   this.changeColumnCount();
  }

  
};


window.UTILS.LayoutDesignerRow = class {
  rowEditor;
  cellsContainer;
  cells;
  stacked;
  btnCopyUp;
  btnCopyDown;
  
  constructor( id, stacked ) {
    this.rowEditor = document.createElement( "div" );
    document.querySelector( "#layout_designer_modal .editor-container").appendChild( this.rowEditor );
    let editorFragment = document.querySelector( "#layout_designer_row" ).content.cloneNode( true );
    this.rowEditor.appendChild( editorFragment );
    this.rowEditor.setAttribute( "id", id );
    this.cellsContainer = this.rowEditor.querySelector( ".row-edit-area" );
    this.btnCopy = this.rowEditor.querySelector( ".js--btn-copy" );
    this.btnPaste = this.rowEditor.querySelector( ".js--btn-paste" );
   
    this.cells = [];
    this.stacked = stacked;
    this.copyPasteHandlers();
  }

  addCell( colspan ) {
    if( this.stacked ) {
      colspan = 12;
    }
    let cell = new window.UTILS.LayoutDesignerCell( this, colspan );
    this.cells.push( cell );
    this.cellsContainer.appendChild( cell.element );
    return cell;
  }

  remove( cellElement ) {
    this.cellsContainer.removeChild( cellElement );
    this.cells = this.cells.filter( c => c.element !== cellElement );
  }

  removeLastCell() {
    let lastCell = this.cells.pop();
    if ( lastCell ) {
      this.cellsContainer.removeChild( lastCell.element );
    }
  }

  clear() {
    this.cells.forEach( cell => cell.destroy() );
    this.cells = [];
    this.cellsContainer.innerHTML = "";
  }

  evenWidths() {
    let evenspan = Math.floor( 12 / this.cells.length );
    if( this.stacked ) {
      evenspan = 12;
    }
    this.cells.forEach( cell => {
      cell.span = evenspan;
    } );
  }

  get sizes(){
    let widths = []
    this.cells.forEach(
      ( cell ) => {
        widths.push( cell.span );
      }
    )
    console.log( "sizes: ", widths );
    return widths;
  }

  set sizes ( newsizes ) {
    if( newsizes.length !== this.cells.length ) {
      alert( "Cannot paste columns: Number of pasted columns do not match number of current columns." );
      return;
    }
    for( let i=0; i < newsizes.length; i++ ) {
      this.cells[ i ].span = newsizes[ i ];
    }
  }

  copyPasteHandlers() {
    console.log(this.sizes)
    this.btnCopy.addEventListener( "click", ()=>{
      window.copiedRow = this.sizes;
    } );
    this.btnPaste.addEventListener( "click", ()=>{
      this.sizes = window.copiedRow;
    } );
  }

};


window.UTILS.LayoutDesignerCell = class {
  element;
  #span;
  constructor( parent, colspan ) {
    this.parent = parent;
    this.element = document.createElement( "div" );
    let controls = document.querySelector( "#layout_designer_cell_controls" ).content.cloneNode( true );
    this.element.appendChild( controls );

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
    this.span = colspan;
  }
  set span ( n ) {
    this.#span = n;
    this.element.setAttribute( "data-span", this.#span );
    //this.element.className = "col-" + this.#span + "  col-xs-" + this.#span;
    this.element.className = "cellspan-" + this.#span;
    this.element.querySelector( ".js-span-display" ).innerHTML = this.#span;
  }

  get span() {
    return this.#span;
  }
  destroy() {
    this.parent.remove( this.element );
  }
};
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
  copyMDButton;
  copyHTMLButton;

  constructor() {
    this.designer = document.getElementById( "layout-designer" );
    this.designerModal = document.getElementById( "layout_designer_modal" );
    this.countSelector = this.designer.querySelector( "#layout_designer_column_count" );
    this.copyMDButton = this.designerModal.querySelector( "#copy_md_btn" );
    this.copyHTMLButton = this.designerModal.querySelector( "#copy_html_btn" );
    this.resetBtn = this.designerModal.querySelector( "#reset_btn" );

    
    document.querySelectorAll( ".md-container" ).forEach( el => {
      this.createToolbarButton( el );
    } );
    
    //this.rowXL = new window.UTILS.LayoutDesignerRow( this.designer.querySelector( "#row_xl" ), false );
    this.rowXL = new window.UTILS.LayoutDesignerRow( "rowXL", "XL", false );
    this.rowLG = new window.UTILS.LayoutDesignerRow( "rowLG", "LG", false );
    this.rowMD = new window.UTILS.LayoutDesignerRow( "rowMD", "MD", false );
    this.rowSM = new window.UTILS.LayoutDesignerRow( "rowSM", "SM", false );
    this.rowXS = new window.UTILS.LayoutDesignerRow( "rowXS", "XS", true );

    this.rows = [ this.rowXL, this.rowLG, this.rowMD, this.rowSM, this.rowXS ];

    this.countSelector.addEventListener( "change", this.changeColumnCount.bind( this ) );

    this.designerModal.querySelector( "[data-bs-dismiss='modal']" ).addEventListener( "click", () => {
      //sessionStorage.setItem( "layout_designer_layout", JSON.stringify( this.exportSizes() ) );
      console.log( "hej", sessionStorage.getItem( "layout_designer_layout" ) )
    } );

    this.copyMDButton.addEventListener( "click", () => {
      let exportedData = this.exportSizes();
      console.log( JSON.stringify( exportedData, null, 2 ) );
      this.setClipboard( this.generateCode( "markdown" ) );
      //sessionStorage.setItem( "layout_designer_layout", JSON.stringify( exportedData ) );
    } );
    this.copyHTMLButton.addEventListener( "click", () => {
      let exportedData = this.exportSizes();
      console.log( JSON.stringify( exportedData, null, 2 ) );
      this.setClipboard( this.generateCode( "html" ) );
      //sessionStorage.setItem( "layout_designer_layout", JSON.stringify( exportedData ) );
    } );
    this.resetBtn.addEventListener( "click", () => {
      console.log( "click on reset" );
      this.reset();
    } )
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
    

    if( sessionStorage.getItem( "layout_designer_layout" ) ) {
      this.importSizes( JSON.parse( sessionStorage.getItem( "layout_designer_layout" ) ) );
    } else {
      this.reset();
    }


    console.log( "Showing layout selection modal." );
    
  }

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
      //console.log( "class", cellClass );
      cellCode += prefix;
      if( style === "markdown" ) {
        cellCode += `[col class="${cellClass}"]${i+1}[/col]`;
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
    console.log(output);
    return output;
  }

  importSizes( data ) {
    //console.log( "Importing data for", data.cellsNumber, "cols" );
    //console.log( data );
    this.reset();
    this.countSelector.selectedIndex = data.cellsNumber - 1;
    this.changeColumnCount();
    this.rowXL.sizes = data.xl.sizes;
    this.rowLG.sizes = data.lg.sizes;
    this.rowMD.sizes = data.md.sizes;
    this.rowSM.sizes = data.sm.sizes;
    this.rowXS.sizes = data.xs.sizes;
  }

  reset(){
    this.rows.forEach( (row)=> { row.clear(); } );
    this.columnCount = 0;
    this.countSelector.selectedIndex = 1; // Default to 2 columns
    this.changeColumnCount();
  }

  async setClipboard( text ) {
    const type = "text/plain";
    const clipboardItemData = {
      [ type ]: text,
    };
    const clipboardItem = new ClipboardItem( clipboardItemData );
    await navigator.clipboard.write( [ clipboardItem ] );
  }

  
};


window.UTILS.LayoutDesignerRow = class {
  rowEditor;
  cellsContainer;
  cells = [];
  hiddenCellsContainer;
  stacked;
  btnCopyUp;
  btnCopyDown;
  
  constructor( id, title, stacked ) {
    this.rowEditor = document.createElement( "div" );
    document.querySelector( "#layout_designer_modal .editor-container").appendChild( this.rowEditor );
    let editorFragment = document.querySelector( "#layout_designer_row" ).content.cloneNode( true );
    this.rowEditor.appendChild( editorFragment );
    this.rowEditor.setAttribute( "id", id );
    this.cellsContainer = this.rowEditor.querySelector( ".row-edit-area" );
    this.hiddenCellsContainer = this.rowEditor.querySelector( ".hidden-cells-container" );
    this.btnCopy = this.rowEditor.querySelector( ".js--btn-copy" );
    this.btnPaste = this.rowEditor.querySelector( ".js--btn-paste" );

    this.rowEditor.querySelector( ".js--row-title" ).innerHTML = title;
   
    this.cells = []; console.log("cl", this.cells.length);
    this.stacked = stacked;
    this.copyPasteHandlers();
  }

  addCell( colspan ) {
    if( this.stacked ) {
      colspan = 12;
    }
    let cell = new window.UTILS.LayoutDesignerCell( this, colspan, this.cells.length + 1 );
    this.cells.push( cell );
    this.cellsContainer.appendChild( cell.element );
    return cell;
  }

  removeLastCell() {
    let lastCell = this.cells.pop();
    if ( lastCell ) {
      lastCell.destroy();
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
  cellNumber;
  hiddenCellAvatar = null;
  previousSpan;

  constructor( parent, colspan, cellNumber ) {
    this.parent = parent;
    this.cellNumber = cellNumber;
    this.element = document.createElement( "div" );
    let controls = document.querySelector( "#layout_designer_cell_controls" ).content.cloneNode( true );
    this.element.appendChild( controls );

    this.element.querySelector( ".cellnumber" ).innerHTML = cellNumber;

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
    this.span = colspan;
  }

  set span ( n ) {
    this.previousSpan = this.#span;
    this.#span = n;
    this.element.setAttribute( "data-span", this.#span );
    //this.element.className = "col-" + this.#span + "  col-xs-" + this.#span;
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

  get span() {
    return this.#span;
  }

  createHiddenCellAvatar() {
    console.log( "cell hidden, No. ", this.cellNumber );
    let hiddenCellAvatar = document.createElement( "div" );
    let content = document.querySelector( "#hidden_cell_avatar" ).content.cloneNode( true );
    hiddenCellAvatar.className = "hidden_cell_avatar";
    hiddenCellAvatar.appendChild( content );
    hiddenCellAvatar.querySelector( ".cellnumber" ).innerHTML = this.cellNumber;
    this.hiddenCellAvatar = hiddenCellAvatar;
    
    this.parent.hiddenCellsContainer.appendChild( hiddenCellAvatar );

    hiddenCellAvatar.querySelector( ".js-show-cell" ).addEventListener( "click", ( e ) => {
      this.span = this.previousSpan;
      this.destroyHiddenCellAvatar();
    } );

  }

  destroyHiddenCellAvatar() {
    if( this.hiddenCellAvatar && this.parent.hiddenCellsContainer.contains( this.hiddenCellAvatar ) ) {
      this.parent.hiddenCellsContainer.removeChild( this.hiddenCellAvatar );
    }
    this.hiddenCellAvatar = null;
  }


  destroy() {
    if( this.hiddenCellAvatar ){
      this.destroyHiddenCellAvatar( this.hiddenCellAvatar );
    }
    this.parent.cellsContainer.removeChild( this.element );
  }
};
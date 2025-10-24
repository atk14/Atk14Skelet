window.UTILS = window.UTILS || { };

window.UTILS.LayoutDesigner = class {

  parentEditor;
  toolbar;
  toolbarButton;
  designer;
  countSelector;
  rowXL;
  designerModal;

  constructor( parentEditor ) {
    this.parentEditor = parentEditor;
    this.toolbar = parentEditor.querySelector( ".md-toolbar .btn-toolbar" );

    this.designer = document.getElementById( "layout-designer" );
    this.designerModal = document.getElementById( "layout_designer_modal" );
    this.countSelector = this.designer.querySelector( "#layout_designer_column_count" );
    this.rowXL = this.designer.querySelector( "#row_xl" );

    this.createToolbarButton();

    this.designerModal.addEventListener( "show.bs.modal", () => {
      this.initModal();
    } );
  }

  createToolbarButton() {
    // Initialization code for LayoutDesigner
    console.log( "LayoutDesigner initialized with parent editor:", this.parentEditor );
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
    this.toolbar.appendChild( div );
    this.toolbarButton = btn;
    this.toolbarButton.addEventListener( "click", this.initModal.bind( this ) );
  }

  initModal() {
    // Code to show modal dialog for layout selection
    this.rowXL.innerHTML = "";
    console.log( "Showing layout selection modal." );
    let count = parseInt( this.countSelector.value, 10 );
    let xlClass = "col-xl-" + Math.floor( 12 / count );
    console.log( "Selected column count:", count );
    for ( let i = 1; i <= count; i++ ) {
      this.rowXL.appendChild( this.createColumn( xlClass ) );
    }
  }

  createColumn( xlClass) {
    let col = document.createElement( "div" );
    col.className = "col " + xlClass;
    return col;
  }
};
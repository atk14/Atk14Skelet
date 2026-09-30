window.UTILS = window.UTILS || { };

window.UTILS.TabDesigner = class {

  
  constructor() {
    console.log("tabdesiger init")
    if ( window.UTILS.TabDesigner.instance ) {
      window.UTILS.TabDesigner.instance.attachToolbarButtons();
      return window.UTILS.TabDesigner.instance;
    }
    window.UTILS.TabDesigner.instance = this;
    this.attachToolbarButtons();
  }

  attachToolbarButtons() {
    /*window.UTILS.MDEditorToolbarHelper.addToolbarButton( {
      name: "fa_iconpicker",
      text: "<i class=\"fa-solid fa-icons\"></i> " + faIconpickerTexts.icons,
      title: "Icons",
      className: "",
      hasModal: true,
      modalId: "#fa_iconpicker_modal",
    } );*/
    window.UTILS.MDEditorToolbarHelper.addToolbarDropdownItem( "insert_dropdown", {
      name: "tabdesigner",
      text: "<img src=\"/public/admin/dist/images/icon-tabs.svg\" width=\"15\" height=\"15\" alt=\"\"  class=\"dropdown_item_icon\"> " + "Tabs",
      title: "Tabs",
      className: "",
      hasModal: true,
      modalId: "#tabdesigner_modal",
    } );
  }
};
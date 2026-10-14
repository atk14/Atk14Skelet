/**
 * Iobject inserter
 * Offers objects (pictures, galleries, videos, files...) linked to the edited record and inserts
 * the chosen one into Markdown text by its insert mark (e.g. "[#123 Picture: Rose]").
 * New objects are created in a new tab by the usual administration pages; the list is reloaded
 * when the user comes back.
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of iobject blocks (to replace the object).
 *
 * Usage:
 *
 *   if( document.getElementById( "iobjectinserter_modal" ) ) {
 *     new UTILS.IobjectInserter();
 *   }
 *
 * Dependencies:
 *
 * md_editor_inserts.js
 * _iobject_inserter.scss
 * _iobject_inserter_modal.tpl, api/iobjects/index
 * Bootstrap 4+
 */
window.UTILS = window.UTILS || { };

window.UTILS.IobjectInserter = class {
  texts = window.iobjectinserterTexts; // UI strings (rendered in template)

  // Icons of objects without a preview image, by object type
  static typeIcons = { Picture: "fa-image", Gallery: "fa-images", Video: "fa-film", File: "fa-file" };

  constructor() {
    if ( window.UTILS.IobjectInserter.instance ) {
      window.UTILS.IobjectInserter.instance.attachToolbarButtons();
      return window.UTILS.IobjectInserter.instance;
    }
    window.UTILS.IobjectInserter.instance = this;

    this.modal = document.getElementById( "iobjectinserter_modal" );
    this.list = this.modal.querySelector( ".js--list" );
    this.filter = this.modal.querySelector( ".js--filter" );
    this.message = this.modal.querySelector( ".js--message" );
    this.saveBtn = this.modal.querySelector( "#iobjectinserter_save_btn" );
    this.items = [];
    this.selectedId = null;
    this.save = null; // callback given by open()
    this.original = null; // { raw, id } of the edited iobject block

    this.attachToolbarButtons();

    if ( this.list ) {
      this.filter.addEventListener( "input", () => this.renderList() );
      this.list.addEventListener( "click", e => {
        let item = e.target.closest( ".iobjectinserter__item" );
        if ( item ) {
          this.select( parseInt( item.dataset.id, 10 ) );
        }
      } );
      this.list.addEventListener( "dblclick", e => {
        let item = e.target.closest( ".iobjectinserter__item" );
        if ( item ) {
          this.select( parseInt( item.dataset.id, 10 ) );
          this.saveSelected();
        }
      } );
      // an object added in another tab appears when coming back
      window.addEventListener( "focus", () => {
        if ( this.modal.classList.contains( "show" ) ) {
          this.load();
        }
      } );
    }
    this.saveBtn.addEventListener( "click", () => this.saveSelected() );
  }

  /**
   * Registers the inserter in UTILS.MDEditorInserts (adds "Objects" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "iobjectinserter",
      text: "<span class=\"fa-solid fa-photo-film\" aria-hidden=\"true\"></span> Objects",
      title: "Objects",
      modal: "#iobjectinserter_modal",
      inline: false,
      blockType: "iobject",
      parse: raw => window.UTILS.IobjectInserter.parse( raw ),
      open: options => this.open( options ),
    } );
  }

  /**
   * Parses an insert mark
   *   "[#123 Picture: Rose]" -> { id: 123 }
   * @param {String} raw
   * @returns {Object|null}
   */
  static parse( raw ) {
    let m = raw.trim().match( /^\[#(\d+)[^\]]*\]$/ );
    return m ? { id: parseInt( m[ 1 ], 10 ) } : null;
  }

  /**
   * Shows the objects, the edited one (if any) selected
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    let model = options.raw ? window.UTILS.IobjectInserter.parse( options.raw ) : null;
    this.original = model ? { raw: options.raw, id: model.id } : null;
    this.selectedId = model ? model.id : null;
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
    this.updateSaveButton();
    if ( this.list ) {
      this.filter.value = "";
      this.items = [];
      this.renderList();
      this.load();
    }
  }

  /**
   * Loads objects of the record by the API
   */
  load() {
    let lang = document.documentElement.getAttribute( "lang" );
    let params = new URLSearchParams( {
      format: "json",
      table_name: this.modal.dataset.table_name,
      record_id: this.modal.dataset.record_id,
    } );
    if ( !this.items.length ) {
      this.showMessage( this.texts.loading );
    }
    fetch( "/api/" + lang + "/iobjects/?" + params.toString(), { credentials: "same-origin" } ).then( response => {
      if ( !response.ok ) {
        throw new Error( "HTTP " + response.status );
      }
      return response.json();
    } ).then( items => {
      this.items = items;
      this.renderList();
    } ).catch( () => {
      this.showMessage( this.texts.error );
    } );
  }

  renderList() {
    let q = this.filter.value.trim().toLowerCase();
    let items = this.items.filter( item => !q || ( item.title + " " + item.type_label + " " + item.id ).toLowerCase().indexOf( q ) >= 0 );
    this.list.innerHTML = "";
    items.forEach( item => this.list.appendChild( this.itemElement( item ) ) );
    if ( !this.items.length ) {
      this.showMessage( this.message.textContent === this.texts.error ? this.texts.error : this.texts.empty );
    } else if ( !items.length ) {
      this.showMessage( this.texts.notFound );
    } else {
      this.showMessage( "" );
    }
    this.updateSaveButton();
  }

  itemElement( item ) {
    let el = document.createElement( "button" );
    el.type = "button";
    el.className = "iobjectinserter__item";
    el.dataset.id = item.id;
    el.classList.toggle( "active", item.id === this.selectedId );
    el.title = item.insert_mark;

    let preview = document.createElement( "div" );
    preview.className = "iobjectinserter__preview";
    if ( item.preview_image_url ) {
      let img = document.createElement( "img" );
      img.src = item.preview_image_url;
      img.alt = "";
      img.loading = "lazy";
      preview.appendChild( img );
    } else {
      preview.innerHTML = "<span class=\"fa-solid " + ( window.UTILS.IobjectInserter.typeIcons[ item.type ] || "fa-cube" ) + "\"></span>";
    }

    let type = document.createElement( "div" );
    type.className = "iobjectinserter__type";
    type.textContent = item.type_label + " #" + item.id;
    let title = document.createElement( "div" );
    title.className = "iobjectinserter__title";
    title.textContent = item.title || this.texts.untitled;
    title.classList.toggle( "iobjectinserter__title--untitled", !item.title );

    el.appendChild( preview );
    el.appendChild( type );
    el.appendChild( title );
    return el;
  }

  select( id ) {
    this.selectedId = id;
    [ ...this.list.querySelectorAll( ".iobjectinserter__item" ) ].forEach( el => {
      el.classList.toggle( "active", parseInt( el.dataset.id, 10 ) === id );
    } );
    this.updateSaveButton();
  }

  updateSaveButton() {
    this.saveBtn.disabled = !this.items.some( item => item.id === this.selectedId );
  }

  /**
   * Inserts the insert mark of the selected object; the edited one is kept as it was when not changed
   */
  saveSelected() {
    let item = this.items.find( it => it.id === this.selectedId );
    if ( !item || !this.save ) {
      return;
    }
    this.save( this.original && this.original.id === item.id ? this.original.raw : item.insert_mark );
  }

  showMessage( text ) {
    this.message.textContent = text;
    this.message.classList.toggle( "d-none", !text );
  }
};

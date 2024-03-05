/* Tag chooser
 * Nice graphical UI for tag selection input
 * appends itself to #id_tag input
 * 
 * Uses Sortablejs to handle drag and drop tag ordering
 * 
 * Autocomplete suggestions integration via window.UTILS.Suggestions
 * 
 * Usage: window.UTILS.TagChooser.init();
 * 
 * Dependencies
 * sortablejs - https://github.com/SortableJS/Sortable (optional)
 * window.UTILS.Suggestions - from ATK14SKelet (optional)
 * public/admin/styles/tag_chooser.scss - styles
 * 
*/
window.UTILS = window.UTILS || { };

window.UTILS.TagChooser = class {
  static url;
  static numInstances = 0;
  input;
  parent;
  wrapper;
  singleTagInput;
  addTagBtn;
  tagContainer;
  template = `
  <div class="tag_chooser js--tag_chooser">
    <div class="input-group">
      <input type="text" class="form-control js--single_tag_input" placeholder="Tag" aria-label="Tag">
      <button class="btn btn-outline-secondary js--single_tag_btn" type="button"><i class=\"fa-solid fa-circle-plus\"></i></button>
    </div>
    <div class="tag_container js--tag_container"></div>
  </div>
  `;

  static init() {
    let inputs = document.querySelectorAll( "#id_tags" );
    let lang = document.querySelector( "html" ).getAttribute( "lang" );
    this.url = "/api/" + lang + "/tags_suggestions/?format=json&q=";
    [...inputs].forEach( function( input ) {
      new window.UTILS.TagChooser( input );
    } );
  }

  constructor( input ) {
    this.input = input;
    this.parent = this.input.parentElement;
    // Hide default input
    this.input.type = "hidden";
    
    // Insert TagChooser HTML markup
    this.parent.insertAdjacentHTML( "beforeend", this.template );

    this.wrapper = this.parent.querySelector( ".js--tag_chooser" );
    this.singleTagInput = this.parent.querySelector( ".js--single_tag_input" );
    this.addTagBtn = this.parent.querySelector( ".js--single_tag_btn" );
    this.tagContainer = this.parent.querySelector( ".js--tag_container" );

    // Initial tags render
    let tags = this.split( this.input.value );
    tags.forEach( this.renderTag.bind( this ) );

    // Set handlers to single tag input and its button
    this.singleTagInput.addEventListener( "keydown", this.onInputEnter.bind( this ) );
    this.addTagBtn.addEventListener( "click", this.onBtnClick.bind( this ) );

    // Init suggestions handled by window.UTILS.Suggestions
    if( window.UTILS.Suggestions ) {
      window.UTILS.Suggestions.tagSuggestions( this.singleTagInput );
    }

    // Disable default Bootstrap popover
    if( window.jQuery && bootstrapVersion < 5 ){
      window.jQuery( this.singleTagInput ).popover( "disable" );
    }

    // if Sortable library exists use it to make tags sortable by drag+drop
    // https://github.com/SortableJS/Sortable
    if( window.Sortable ) {
      new window.Sortable( this.tagContainer, {
        onUpdate: this.onChange.bind( this )
      } );
    }
  }

  // create new tag item from single tag input value on Enter
  onInputEnter( e ) {
    if ( e.key === "Enter" || e.keyCode === 13 ) {
      e.preventDefault();
      // delay processing so tag is added AFTER autocomplete fills input with selected value
      setTimeout( ()=> {
        if( this.singleTagInput.value.length > 0 ){
          this.renderTag( this.singleTagInput.value );
          this.singleTagInput.value = "";
        }
      }, 20 );
    }
  }

  // create new tag item from single tag input value on btn click
  onBtnClick( e ) {
    e.preventDefault();
    if( this.singleTagInput.value.length > 0 ) {
      this.renderTag( this.singleTagInput.value );
      this.singleTagInput.value = "";
    }
  }

  // remove tag item
  removeTag( e ) {
    e.target.closest( ".badge" ).remove();
    this.onChange();
  }

  // render tag badges
  renderTag( input ) {
    // split input by commas in case there are more than one tag entered at once
    let tagNames = this.split( input );

    // render tag items
    tagNames.forEach( ( tagName )=>{

      // trim whitespace
      tagName = tagName.trim();

      // check to prevent whitespace only tag name
      if( tagName.length < 1 ) {
        return;
      }

      // check to prevent duplicate tags
      if ( this.tagContainer.querySelector( ".tag_item[data-tag_name='" + tagName + "']") ) {
        return
      };

      // template for tag, tagName interpolated automatically
      let tagItemTemplate = `
      <div class="badge badge rounded-pill text-bg-secondary tag_item js--tag_item" data-tag_name="${tagName}">
        <div class="remove_btn js--remove_btn"><i class="fa-solid fa-circle-xmark"></i></div>
        ${tagName}
      </div>
    `;
      
      // Insert tag badge
      this.tagContainer.insertAdjacentHTML( "beforeend", tagItemTemplate.trim() );

      // Handler for tag delete button
      this.tagContainer.querySelector( ".tag_item[data-tag_name='" + tagName+ "'] .js--remove_btn" ).addEventListener( "click", this.removeTag.bind( this ) );
    })
    this.onChange();
  }

  // When tag badges change write their comma separated list to original input
  // to keep visual tags and hidden inputs in sync
  //called on every tag removal/addition
  onChange(){
    let tagItems = this.tagContainer.querySelectorAll( ".tag_item[data-tag_name]" );
    let tagList = "";
    [...tagItems].forEach( ( item ) => {
      tagList += item.dataset.tag_name + ", ";
    } );
    this.input.value = tagList;
  }
  
  // Helper functions for parsing tags lists
  split( val ) {
    return val.split( /,\s*/ );
  }

};
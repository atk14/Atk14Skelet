/* Tag chooser
 * Nice graphical UI for tag selection input
 * appends itself to #id_tag input
 * 
 * TODO: optional autocomplete integration via window.UTILS.Suggestions
 *       optional Drag + Drop rearrange
 * 
 * Usage: window.UTILS.TagChooser.init();
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
      <input type="text" class="form-control js--single_tag_input" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary js--single_tag_btn" type="button" id="button-addon2"><i class=\"fa-solid fa-circle-plus\"></i></button>
      </div>
    </div>
    <div class="tag_container js--tag_container"></div>
  </div>
  `;
  


  static init() {
    console.log( "Hi, I am your brand new TagChooser!" );
    let inputs = document.querySelectorAll( "#id_tags" );
    let lang = document.querySelector( "html" ).getAttribute( "lang" );
    this.url = "/api/" + lang + "/tags_suggestions/?format=json&q=";
    //[...inputs].forEach( this.createTagChooser.bind( this ) );
    [...inputs].forEach( function( input ) {
      new window.UTILS.TagChooser( input );
    } );
  }

  constructor( input ) {
    this.input = input;
    console.log( "createTagChooser", input );
    this.parent = this.input.parentElement;
    this.parent.style.outline = "1px solid red";
    this.input.style.opacity = 0.7;
    
    this.parent.insertAdjacentHTML( "beforeend", this.template );

    this.wrapper = this.parent.querySelector( ".js--tag_chooser" ); console.log( "test", this.wrapper );
    this.singleTagInput = this.parent.querySelector( ".js--single_tag_input" );
    this.addTagBtn = this.parent.querySelector( ".js--single_tag_btn" );
    this.tagContainer = this.parent.querySelector( ".js--tag_container" );

    let tags = this.split( this.input.value );
    tags.forEach( this.renderTag.bind( this ) );

    this.singleTagInput.addEventListener( "keydown", this.onInputEnter.bind( this ) );
    this.addTagBtn.addEventListener( "click", this.onBtnClick.bind( this ) );
  }

  // create new tag item from single tag input value on Enter
  onInputEnter( e ) {
    if ( e.key === "Enter" || e.keyCode === 13 ) {
      e.preventDefault();
      console.log( e.target.value );
      if( e.target.value.length > 0 ){
        this.renderTag( e.target.value );
        e.target.value = "";
      }
    }
  }

  onBtnClick( e ) {
    e.preventDefault();
    if( this.singleTagInput.value.length > 0 ) {
      this.renderTag( this.singleTagInput.value );
    }
  }

  // remove tag item
  removeTag( e ) {
    console.log( "removeTag", e.target.closest( ".badge" ) );
    e.target.closest( ".badge" ).remove();
    this.onChange();
  }

  renderTag( input ) {
    // split input by commas in case there are more than one tag entered at once
    let tagNames = this.split( input );

    // render tag items
    tagNames.forEach( ( tagName )=>{
      tagName = tagName.trim();

      // check to prevent whitespace only 
      if( tagName.length < 1 ) {
        return;
      }

      // check to prevent duplicate tags
      if ( this.wrapper.querySelector( ".tag-item[data-tag_name='" + tagName + "']") ) {
        return
      };

      let tagItemTemplate = `
      <div class="badge badge-pill badge-info tag_item js--tag_item" data-tag_name="${tagName}">
        <div class="remove_btn js--remove_btn"><i class="fa-solid fa-circle-xmark"></i></div>
        ${tagName}
      </div>
    `;
      
      this.tagContainer.insertAdjacentHTML( "beforeend", tagItemTemplate );
      this.tagContainer.querySelector( ".tag_item[data-tag_name='" + tagName+ "'] .js--remove_btn" ).addEventListener( "click", this.removeTag.bind( this ) );
    })
    this.onChange();
  }

  // When tags change write their comma separated list to original input
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

  /*extractLast( t ) {
    return this.split( t ).pop();
  }*/
};
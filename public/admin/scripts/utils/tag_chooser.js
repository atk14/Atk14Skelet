window.UTILS = window.UTILS || { };

window.UTILS.TagChooser = class {
  static url;
  static numInstances = 0;
  input;
  parent;
  wrapper;
  singleTagInput;


  static init() {
    console.log( "Hi, I am your brand new TagChooser!" );
    let inputs = document.querySelectorAll( "[data-tags_suggesting='yes']" );
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
    console.log( this );
    console.log( this.split( "a,b b, c, dede") );
    this.input.style.opacity = 0.7;
    this.wrapper = document.createElement( "div" );
    this.singleTagInput = document.createElement( "input" );
    this.singleTagInput.setAttribute( "id", "tag_chooser_input_" + this.numInstances );
    this.singleTagInput.setAttribute( "type", "text" );
    this.singleTagInput.classList.add( "form-control" );
    this.singleTagInput.classList.add( "text" );
    this.wrapper.appendChild( this.singleTagInput );
    this.parent.appendChild( this.wrapper );

    let tags = this.split( this.input.value );
    tags.forEach( this.renderTag.bind( this ) );

    this.singleTagInput.addEventListener( "keydown", this.addTag.bind( this ) );
  
  }

  addTag( e ) {
    if (e.key === 'Enter' || e.keyCode === 13) {
      e.preventDefault();
      console.log( e.target.value );
      if( e.target.value.length > 0 )
      this.renderTag( e.target.value );
      e.target.value = "";
    }
  }

  removeTag( e ) {
    console.log( "removeTag", e.target.closest( ".badge" ) );
    e.target.closest( ".badge" ).remove();
    this.onChange();
  }

  renderTag( tagName ) {
    tagName = tagName.trim();
    if( tagName.length < 1 ) {
      return;
    }
    let tagItem = document.createElement( "div" );
    tagItem.classList.add( "badge" );
    tagItem.classList.add( "badge-info" );
    tagItem.classList.add( "badge-pill" );
    tagItem.style.fontSize = "1.25rem";
    let removeBtn = document.createElement( "div" );
    removeBtn.classList.add( "d-inline-flex" );
    removeBtn.classList.add( "remove_btn" );
    removeBtn.classList.add( "px-1" );
    removeBtn.append( "x" );
    removeBtn.addEventListener( "click", this.removeTag.bind( this ) );
    tagItem.appendChild( removeBtn );
    tagItem.append( tagName );
    //tagItem.innerHtml = removeBtn + tagName;
    tagItem.dataset.tag_name = tagName;
    this.wrapper.appendChild( tagItem );
    
    this.onChange();
  }

  onChange(){
    let tagItems = this.wrapper.querySelectorAll( "[data-tag_name]" );
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
  extractLast( t ) {
    return this.split( t ).pop();
  }
};
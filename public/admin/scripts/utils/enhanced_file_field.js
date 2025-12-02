/**
 * 
 * EnhancedFileField
 * 
 * This utility enhances file input fields that are associated with image thumbnails.
 * When a user selects a new image file, the thumbnail is updated to display the selected image.
 * 
 * Usage:
 * Call `window.UTILS.EnhancedFileField.init();` to initialize all enhanced file fields on the page.
 * You mey call init() multiple times (e.g. after AJAX content load).
 * 
 */


window.UTILS = window.UTILS || { };

window.UTILS.EnhancedFileField = class {

  static formGroupSelector = ".form-group:has(input[type='file']):has(.img-thumbnail):not(.enhanced-file-field):not([multiple])";
  formGroup;
  fileField;
  thumbnail;
  thumbnailLink;

  constructor( formGroup ) {
    this.formGroup = formGroup;
    this.formGroup.classList.add( "enhanced-file-field" );
    this.fileField = this.formGroup.querySelector( "input[type='file']" );
    this.thumbnail = this.formGroup.querySelector( ".img-thumbnail" );
    this.thumbnailLink = this.thumbnail.closest( "a" );

    this.fileField.addEventListener( "change", this.onFileFieldChange.bind( this ) );
  }

  /**
   * Handle change event on the file input field
   * @param {*} event 
   */
  onFileFieldChange( event ) {
    const file = event.target.files[0];
    if ( file ) {
      const reader = new FileReader();
      reader.onload = ( e ) => {
        this.thumbnail.src = e.target.result;
        this.thumbnailLink.removeAttribute( "href" );
        this.thumbnailLink.style.cursor = "default";
      };
      reader.readAsDataURL( file );
    }
  }

  /**
   * Initialize all enhanced file fields on the page
   * Usage: window.UTILS.EnhancedFileField.init();
   */
  static init() {
    console.log("EnhancedFileField initialized");
    const groups = document.querySelectorAll( this.formGroupSelector );
    [...groups].forEach( ( group ) => {
      group.classList.add( "enhanced-file-field" );
      new window.UTILS.EnhancedFileField( group );
    } );
  }
};
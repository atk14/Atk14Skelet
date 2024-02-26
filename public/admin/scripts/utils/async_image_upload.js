/*
 * Async multiple images uploader
 * Usage:
 * window.UTILS.AsyncImageUploader.init();
*/

window.UTILS = window.UTILS || { };

window.UTILS.AsyncImageUploader = class {
  form;
	wrap;
	dropZone;
  url;
	progress;
	list;
	input;
  uploads = [];

  constructor( form ) {
    this.form = form;
    this.dropZone = this.form.closest( ".drop-zone" );
    this.wrap = this.form.closest( ".js--image_gallery_wrap" );
    this.url = this.form.getAttribute( "action" );
    this.progress = this.wrap.querySelector( ".progress-bar" );
    this.list = this.wrap.querySelector( ".list-group-images" );
    this.input = this.form.querySelector( "input" );
    this.input.dataset.url = this.url;
    this.addUIhandlers();
  }

  // Sets handlers for UI interactions - file select, drag + drop
  addUIhandlers() {
    ;["dragenter", "dragover"].forEach( eventName => {
      this.dropZone.addEventListener( eventName, this.highlight.bind( this ), false );
    } );
    
    ;["dragleave", "drop"].forEach( eventName => {
      this.dropZone.addEventListener( eventName, this.unhighlight.bind( this ), false );
    } );

    this.dropZone.addEventListener( "drop", this.onFilesDrop.bind( this ), false );

    this.input.addEventListener( "change", this.onFilesSelect.bind( this) );
  }

  // Files from drag + drop
  onFilesDrop( e ){
    e.preventDefault();
    let dt = e.dataTransfer;
    let files = dt.files;
    this.startUpload( files );
  }

  // Files selected by file input
  onFilesSelect() {
    this.startUpload( this.input.files );
  }

  // Process dropped/selected files
  startUpload( files ) {
    if( files ){
      [...files].forEach( this.uploadFile.bind( this ) );
    }
  }

  // Initiates upload
  uploadFile( file ) {
    let xhr = new XMLHttpRequest();
    // Add xhr to uploads list
    this.uploads.push( xhr );

    // Setup xhr request
    xhr.open( "POST", this.url, true );
    xhr.responseType = "json";
    xhr.upload.addEventListener( "progress", this.onUploadProgress.bind( this ) );
    xhr.addEventListener( "readystatechange", this.onReadyStateChange.bind( this ) );
    xhr.setRequestHeader( "Content-Type", file.type);
    xhr.setRequestHeader( "Accept", "application/json, text/javascript, text/plain, */*" );
    xhr.setRequestHeader( "Content-Disposition", "attachment; filename=" + encodeURIComponent( file.name ) );
    xhr.setRequestHeader( "X-Requested-With", "XMLHttpRequest" );
    
    xhr.send(file);
  }

  // Updates progressbar
  onUploadProgress( e ) {
    let loaded = 0;
    let total = 0;
    e.target.bytesLoaded = e.loaded;
    e.target.bytesTotal = e.total;
    // Sums progress from all current uploads in uploads list
    for( let i = 0; i < this.uploads.length; i++) {
      loaded += this.uploads[ i ].upload.bytesLoaded;
      total += this.uploads[ i ].upload.bytesTotal;
    }
    this.progress.style.width = loaded * 100 / total + "%";
  }

  // Upload status change
  onReadyStateChange( e ) {
    if ( e.target.response ) {
      if ( e.target.readyState === 4 && e.target.status >= 200 && e.target.status < 400 ) {
        // upload success
        this.onUploadSuccess( e.target.response.image_gallery_item );
      } else if( e.target.readyState === 4 ) {
        // upload error;
        this.onUploadError( e.target.response.error_message );
      }
    } else {
      // unspecifies error no e.target.response 
      console.log( "unspecified error", e );
      //this.onUploadError( "Upload error" );
    }
    this.checkComplete();
  }

  // Shows thumbnail and info about uploaded file
  onUploadSuccess( listItem ){
    // Show list item with thumbnail
    this.list.insertAdjacentHTML( "beforeend", listItem );
    // Add drag handle
    let dragHandle = "<span class='fas fa-grip-vertical text-secondary handle pr-3' " +
        " title='sorting'></span>";
    let itemsWithoutHandles = document.querySelectorAll( ".drop-zone .list-group-item:not(:has(.handle))" );
    for( let i = 0; i < itemsWithoutHandles.length; i++ ){
      itemsWithoutHandles[ i ].insertAdjacentHTML( "afterbegin", dragHandle );
    }
  }

  // Display error message
  onUploadError( errorMessage ) {
    let errorAlert = document.createElement( "div" );
    errorAlert.classList.add( "alert" );
    errorAlert.classList.add( "alert-danger" );
    errorAlert.classList.add( "alert-dismissible" );
    errorAlert.classList.add( "fade" );
    errorAlert.classList.add( "show" );
    let closeBtn = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
    errorAlert.insertAdjacentHTML( "afterbegin", errorMessage + closeBtn );
    this.form.appendChild( errorAlert );
  }

  // Checks if all uploads are complete to reset progressbar and uploads list 
  checkComplete() {
    let completedUploads = 0;
    for( let i = 0; i < this.uploads.length; i++) {
      if( this.uploads[i].readyState === 4 && this.uploads[i].status >= 200 ) {
        completedUploads ++;
      }
    }
    if( this.uploads.length === completedUploads || this.uploads.length === 0 ) {
      // reset progress bar
      this.progress.classList.add( "progress-bar--noanim" );
      this.progress.style.width = "0%";
      this.progress.classList.remove( "progress-bar--noanim" );
      // reset uploads list
      this.uploads = [];
    }
  }

  // Highlights dropzone
  highlight( e ) {
    e.preventDefault();
    if( e.dataTransfer.items[ 0 ].kind === "file" ) {
      this.dropZone.classList.add( "drop-zone-highlight" );
    }
  }

  // Unhighlights dropzone
  unhighlight( e ) {
    if( e.dataTransfer.items[ 0 ].kind === "file" ) {
      this.dropZone.classList.remove( "drop-zone-highlight" );
    }
  }

  // Init - call this to start 
  // This is static method - do NOT use new window.UTILS.AsyncImageUploader(), just call
  // window.UTILS.AsyncImageUploader.init();
  static init() {
    let elems = document.querySelectorAll( ".js--xhr_upload_image_form" );
    [...elems].forEach(function( el ){
      new window.UTILS.AsyncImageUploader( el );
    } );
  }
};
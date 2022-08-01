// This is a place for some tools required in the application

window.UTILS = window.UTILS || { };

window.UTILS.enableRevealPassword = function() {

  var setPwRevealPositions = function () {
    $.each( $( ".password-input-container" ), function( i, el ){
      console.log( "copyLayoutProperties" );
      var pwContainer = $( el );
      var pwInput = pwContainer.find( "input" );
      var revealButton = pwContainer.find( ".password-reveal-button" );
      // pwContainer.css( "display", pwInput.css( "display" ) );
      var posH = pwInput.offset().left - pwContainer.offset().left + pwContainer.width() - revealButton.width();
      console.log( "posH", posH  );
      revealButton.css( "left", posH + "px" );
      
    } )
  };


	console.log( "enableRevealPassword" );
  var pwFields = $( "input[type='password']" );
  $.each( pwFields, function( i, el ) {

    // Get password input
    var pwInput = $( el );

    // Create container for password input. Set css display property the same as password input to get layout consistency
    var pwContainer = $( "<div class='password-input-container'></div>" ).insertAfter( pwInput );
    pwContainer.css( "background-color", "yellow" );
    pwContainer.css( "display", pwInput.css( "display" ) );
    pwInput.css( "display", "block" )

    // Move password input to new container
    pwContainer.append( pwInput );

    console.log( "hi", pwInput.position(), pwInput.width(), pwInput.height() );


    // Now create reveal icon
    var icon = $( "<div class=\"password-reveal-button\">R</div>" );
    pwContainer.append( icon );
    console.log( "icon", icon );

  } );

  setPwRevealPositions();

};

window.UTILS.enableRevealPassword();

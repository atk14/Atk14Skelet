/*
  Password field reveal button.
  Requires styles from shared/_reveal_password_field.scss
*/

window.UTILS = window.UTILS || { };

window.UTILS.enableRevealPassword = function() {

  /*
    Selector for password fields.
    You may set it to something more specific if you want to
    enable reveal only for certain password fields
  */
  var passwordFieldSelector = "input[type='password']";

  /*
    Sets positioning and sizing for password reveal button
    Called on init, then on window.resize.
    Also should be called when some layout changes affects password field and its container.
  */
  var setPwRevealPositions = function () {
    $.each( $( ".password-input-container" ), function( i, el ){

      var pwContainer = $( el );
      var pwInput = pwContainer.find( "input" );
      var revealButton = pwContainer.find( ".password-reveal-button" );

      // reset container style attr to its original state
      pwContainer.removeAttr( "style" );
      pwContainer.attr( "style", pwContainer.attr( "data-style" ) );

      // if container position is static, set it to relative to enable abs. positioning of reveal button
      if( pwContainer.css( "position" ) === "static" ){
        pwContainer.css( "position", "relative" );
      }

      // position and reveal button 
      revealButton.css( "height", pwInput.outerHeight() + "px" );
      var posH = pwInput.offset().left - pwContainer.offset().left + pwInput.outerWidth() - revealButton.width();
      var posV = pwInput.offset().top - pwContainer.offset().top;
      revealButton.css( "left", posH + "px" );
      revealButton.css( "top", posV + "px" );

      // copy input text color to button icon color
      revealButton.css( "color", pwInput.css( "color" ) );

    } )
  };

  /*
    Toggles password visibility
  */
  togglePasswordReveal = function() {

    var revealButton = $( this );
    var pwInput = revealButton.closest( ".password-input-container" ).find( ".input--password" );

    if( pwInput.attr( "type" ) === "password" ) {
      revealButton.addClass( "revealed" );
      pwInput.attr( "type", "text" );
    } else {
      revealButton.removeClass( "revealed" );
      pwInput.attr( "type", "password" );
    }

  }

  /*
    Initialization of password reveal
  */
  var pwFields = $( passwordFieldSelector );
  $.each( pwFields, function( i, el ) {

    // Get password input
    var pwInput = $( el );
    pwInput.addClass( "input--password" );

    var pwContainer = pwInput.parent();
    pwContainer.addClass( "password-input-container" );

    // backup style attribute if present
    pwContainer.attr( "data-style", pwContainer.attr( "style" ) );

    // Now create reveal button
    // Icons for use with FontAwesome icons
    var iconHidden = "<span class=\"password-reveal-button__hidden\"><i class=\"fa-solid fa-eye\"></i></span>";
    var iconVisible = "<span class=\"password-reveal-button__visible\"><i class=\"fa-solid fa-eye-slash\"></i></span>";

    var revealButton = $( "<div class=\"password-reveal-button\">" + iconHidden + iconVisible + "</div>" );
    pwContainer.append( revealButton );

    revealButton.on( "click", togglePasswordReveal ); 

  } );

  setPwRevealPositions();
  $(window).on( "resize", setPwRevealPositions );

};

window.UTILS.enableRevealPassword();

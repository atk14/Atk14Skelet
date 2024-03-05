/* Suggestions class
 * handles admin autocomplete for tags, categories and general inputs
 * Usage:
 * 
 * General suggestions:
 * window.UTILS.Suggestions.handleSuggestions();
 * 
 * Tag suggestions:
 * window.UTILS.Suggestions.handleTagsSuggestions();
 * 
 * Tag suggestions for specific input:
 * window.UTILS.Suggestions.tagSuggestions( input:HTMLElement );
 * 
 * Category suggestions:
 * window.UTILS.Suggestions.handleCategoriesSuggestions();
 * 
 * Dependencies: autocompleter https://github.com/kraaden/autocomplete
 */

import autocomplete from "autocompleter";
window.UTILS = window.UTILS || { };

window.UTILS.Suggestions = class {

  // Suggests anything according by an url
  static handleSuggestions() {
    let inputs = document.querySelectorAll( "[data-suggesting='yes']" );
    [...inputs].forEach( ( input ) => {
      input.setAttribute( "autocomplete", "off" );

      let url = input.dataset.suggesting_url;
                
      // eslint-disable-next-line no-undef
      autocomplete( {
        input: input,
        fetch: async function( text, update ) {
          text = text.toLowerCase();
          try {
            let response = await fetch( url + "&q=" + text );
            let result = await response.json();
            update( result );
          } catch ( error ) {
            console.error( "Error fetching suggestions:", error );
          };
        },
        render: function( item ) {
          var div = document.createElement( "div" );
          div.textContent = item;
          return div;
        },
        onSelect: function( item, input ) {
            input.value = item;
        },
        preventSubmit: 2,
        disableAutoSelect: true,
        debounceWaitMs: 100,
      } );
    } );
  }

  // Suggests tags
  static handleTagsSuggestions() {
    let inputs = document.querySelectorAll( "[data-tags_suggesting='yes']" );
    [...inputs].forEach( ( input )=>{
      this.tagSuggestions( input );
    } );    
  }

  // Suggests tags for specific input
  static tagSuggestions( input ) {
    let lang = document.querySelector( "html" ).getAttribute( "lang" );
    let url = "/api/" + lang + "/tags_suggestions/?format=json&q=";
    let cache = {};
    let term;
    let terms;

    input.setAttribute( "autocomplete", "off" );

    // eslint-disable-next-line no-undef
    autocomplete( {
      input: input,
      fetch: async function( text, update ) {
        term = this.extractLast( text.toLowerCase() );
        if ( term.length > 0 ) {
          if ( term in cache ) {
            update( cache[ term ] );
          } else {
            try {
              let response = await fetch( url + "&q=" + term );
              let result = await response.json();
              cache[ term ] = result;
              update( result );
            } catch ( error ) {
              console.error( "Error fetching suggestions:", error );
            };
          }
        }
      }.bind( this ),
      render: function( item ) {
        var div = document.createElement( "div" );
        div.textContent = item;
        return div;
      },
      onSelect: function( item, input ) {
        terms = this.split( input.value );
        terms.pop(); 
        terms.push( item );
        terms.push( "" );
        input.value = terms.join( ", " );
    }.bind( this ),
    preventSubmit: 1,
    disableAutoSelect: true,
    debounceWaitMs: 100,
    minLength: 1,
    } );
  }

  // Suggest categories
  static handleCategoriesSuggestions(){
    let inputs = document.querySelectorAll( "[data-suggesting_categories='yes']" );
    [...inputs].forEach( ( input ) => {
      input.setAttribute( "autocomplete", "off" );
      
      let url = input.dataset.suggesting_url,
					cache = {},
					term;
      
      // eslint-disable-next-line no-undef
      autocomplete( {
        input: input,
        fetch: async function( text, update ) {
          term = text.toLowerCase();
          if ( term in cache ) {
            update( cache[ term ] );
          } else {
            try {
              let response = await fetch( url + "&q=" + term );
              let result = await response.json();
              cache[ term ] = result;
              update( result );
            } catch ( error ) {
              console.error( "Error fetching suggestions:", error );
            };
          }
        }.bind( this ),
        render: function( item ) {
          var div = document.createElement( "div" );
          div.textContent = item;
          return div;
        },
        onSelect: function( item, input ) {
            input.value = item;
        },
        preventSubmit: 2,
        disableAutoSelect: true,
        debounceWaitMs: 100,
        minLength: 1,
      } );
    } );
  }

  // Helper functions for parsing tags lists
  static split( val ) {
    return val.split( /,\s*/ );
  }
  static extractLast( t ) {
    return this.split( t ).pop();
  }

};

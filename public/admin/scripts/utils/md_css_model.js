/**
 * Model of a Custom CSS block: a <style> element in a DrInk Markdown source
 * (DrInk Markdown keeps it as it is, see the html block in md_block_tokenizer.js).
 * Its CSS is split into sections edited separately (in tabs of the CSS designer, see css_designer.js):
 * rules for all widths and the content of a @media (min-width: ...) for every breakpoint.
 *
 *   <style>
 *   .promo { padding: 1rem; }
 *
 *   @media (min-width: 768px) {
 *     .promo { padding: 2rem; }
 *   }
 *   </style>
 *
 *   let breakpoints = [ { name: "sm", min: 576 }, { name: "md", min: 768 } ];
 *   let model = UTILS.MDCssModel.parse( raw, breakpoints );
 *   model.sections; // { "": ".promo { padding: 1rem; }", sm: "", md: ".promo { padding: 2rem; }" }
 *   UTILS.MDCssModel.build( model, UTILS.MDCssModel.joinSections( sections, breakpoints ) ); // the new source
 *   UTILS.MDCssModel.create( css );
 *
 * The CSS can be split only when it has the order the sections are written in: other rules first
 * (they may contain any at-rules: @media (max-width: ...), @keyframes...), then the breakpoints'
 * @media blocks in ascending order, one for each. Otherwise model.sections is null and the CSS
 * is edited as a whole (splitting it would change the order of the rules, so the cascade).
 *
 * Works in the browser (window.UTILS.MDCssModel) and in Node.js (module.exports).
 * Keep the syntax runnable in Node.js 9.
 */
( function( root ) {

  // the whole block is a single <style> element
  let STYLE = /^([ \t]*)(<style\b[^>]*>)([\s\S]*)(<\/style>)([ \t\r]*)$/i;

  // @media header of a breakpoint: "@media (min-width: 768px) {", also "@media screen and (min-width: 768px) {"
  let BREAKPOINT_MEDIA = /^@media[ \t]+(?:(?:only[ \t]+)?(?:screen|all)[ \t]+and[ \t]*)?\([ \t]*min-width[ \t]*:[ \t]*(\d+(?:\.\d+)?)px[ \t]*\)[ \t]*\{$/i;

  /**
   * Splits CSS into top-level statements: a rule or an at-rule (up to its closing brace or semicolon)
   * with the whitespace and comments before it; strings and comments are skipped
   * @param {String} css
   * @returns {Object} { items: [ { start, statementStart, headerEnd, end } ], rest } (rest: text after the last statement)
   */
  let statements = function( css ) {
    let items = [];
    let depth = 0;
    let start = 0;
    let statementStart = -1;
    let headerEnd = -1;
    let i = 0;
    while ( i < css.length ) {
      let c = css[ i ];
      if ( c === "/" && css[ i + 1 ] === "*" ) {
        let end = css.indexOf( "*/", i + 2 );
        i = end < 0 ? css.length : end + 2;
        continue;
      }
      if ( c === "\"" || c === "'" ) {
        i++;
        while ( i < css.length && css[ i ] !== c && css[ i ] !== "\n" ) {
          i += css[ i ] === "\\" ? 2 : 1;
        }
        i++;
        continue;
      }
      if ( depth === 0 && statementStart < 0 && !/\s/.test( c ) ) {
        statementStart = i;
      }
      if ( c === "{" ) {
        if ( depth === 0 ) {
          headerEnd = i + 1;
        }
        depth++;
      } else if ( c === "}" ) {
        depth = Math.max( 0, depth - 1 );
        if ( depth === 0 && statementStart >= 0 ) {
          items.push( { start: start, statementStart: statementStart, headerEnd: headerEnd, end: i + 1 } );
          start = i + 1;
          statementStart = -1;
          headerEnd = -1;
        }
      } else if ( c === ";" && depth === 0 && statementStart >= 0 ) {
        items.push( { start: start, statementStart: statementStart, headerEnd: -1, end: i + 1 } );
        start = i + 1;
        statementStart = -1;
      }
      i++;
    }
    return { items: items, rest: css.slice( start ) };
  };

  // Removes blank lines around the text and the indentation common to its lines
  let dedent = function( text ) {
    let lines = text.replace( /\r/g, "" ).split( "\n" );
    while ( lines.length && !lines[ 0 ].trim() ) {
      lines.shift();
    }
    while ( lines.length && !lines[ lines.length - 1 ].trim() ) {
      lines.pop();
    }
    let indent = Math.min.apply( null, lines.filter( line => line.trim() ).map( line => line.match( /^[ \t]*/ )[ 0 ].length ) );
    return lines.map( line => line.slice( Math.min( indent, line.length ) ).replace( /[ \t]+$/, "" ) ).join( "\n" );
  };

  let indentLines = ( text, indent ) => text.split( "\n" ).map( line => line ? indent + line : line ).join( "\n" );

  let MDCssModel = {

    /**
     * @param {String} raw - source of the block
     * @param {Array} breakpoints - [ { name, min } ] ascending
     * @returns {Object|null} { indent, open, css, close, trailing, sections, headers } or null when it's not a <style> element
     */
    parse( raw, breakpoints ) {
      let m = String( raw || "" ).match( STYLE );
      if ( !m || /<\/style>/i.test( m[ 3 ] ) ) {
        return null;
      }
      let model = { indent: m[ 1 ], open: m[ 2 ], css: dedent( m[ 3 ] ), close: m[ 4 ], trailing: m[ 5 ] };
      let split = MDCssModel.splitSections( model.css, breakpoints || [] );
      model.sections = split ? split.sections : null;
      model.headers = split ? split.headers : {};
      return model;
    },

    /**
     * Splits CSS into the sections: "" (all widths) and the breakpoints' names
     * @param {String} css
     * @param {Array} breakpoints - [ { name, min } ]
     * @returns {Object|null} { sections, headers } (headers: the original @media headers, kept when the CSS is joined again)
     */
    splitSections( css, breakpoints ) {
      let sections = { "": "" };
      let headers = {};
      breakpoints.forEach( bp => {
        sections[ bp.name ] = "";
      } );
      let parsed = statements( css );
      let baseEnd = null; // the end of the rules for all widths
      let last = -1; // index of the last breakpoint found
      for ( let k = 0; k < parsed.items.length; k++ ) {
        let item = parsed.items[ k ];
        let header = item.headerEnd > 0 ? css.slice( item.statementStart, item.headerEnd ).replace( /\s+/g, " " ) : "";
        let m = header.match( BREAKPOINT_MEDIA );
        let index = m ? breakpoints.findIndex( bp => bp.min === parseFloat( m[ 1 ] ) ) : -1;
        if ( index < 0 ) {
          if ( baseEnd !== null ) {
            return null; // a rule after a breakpoint
          }
          continue;
        }
        if ( baseEnd === null ) {
          baseEnd = item.statementStart;
        } else if ( css.slice( item.start, item.statementStart ).trim() ) {
          return null; // a comment between breakpoints
        }
        if ( index <= last ) {
          return null; // not in ascending order, or a breakpoint twice
        }
        last = index;
        headers[ breakpoints[ index ].name ] = header;
        sections[ breakpoints[ index ].name ] = dedent( css.slice( item.headerEnd, item.end - 1 ) );
      }
      if ( baseEnd !== null && parsed.rest.trim() ) {
        return null; // something unfinished after the breakpoints
      }
      sections[ "" ] = dedent( baseEnd === null ? css : css.slice( 0, baseEnd ) );
      return { sections: sections, headers: headers };
    },

    /**
     * Joins the sections into CSS (the rules for all widths, then the breakpoints in ascending order)
     * @param {Object} sections - { "": css, md: css... }
     * @param {Array} breakpoints - [ { name, min } ]
     * @param {Object} headers - the original @media headers (see splitSections())
     * @returns {String}
     */
    joinSections( sections, breakpoints, headers ) {
      let parts = [];
      let base = dedent( sections[ "" ] || "" );
      if ( base ) {
        parts.push( base );
      }
      breakpoints.forEach( bp => {
        let css = dedent( sections[ bp.name ] || "" );
        if ( css ) {
          let header = ( headers && headers[ bp.name ] ) || "@media (min-width: " + bp.min + "px) {";
          parts.push( header + "\n" + indentLines( css, "  " ) + "\n}" );
        }
      } );
      return parts.join( "\n\n" );
    },

    /**
     * The block with the given CSS
     * @param {Object} model
     * @param {String} css
     * @returns {String}
     */
    build( model, css ) {
      css = dedent( css || "" );
      return model.indent + model.open + "\n" + ( css ? css + "\n" : "" ) + model.close + model.trailing;
    },

    create( css ) {
      return MDCssModel.build( { indent: "", open: "<style>", close: "</style>", trailing: "" }, css );
    },

    /**
     * Is the source a Custom CSS block?
     */
    isCss( raw ) {
      return !!MDCssModel.parse( raw, [] );
    },
  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDCssModel;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDCssModel = MDCssModel;
  }

} )( typeof window !== "undefined" ? window : this );

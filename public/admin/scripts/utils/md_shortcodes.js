/**
 * DrInk Markdown shortcodes parser
 * Finds shortcode tags ([tabs], [tab name="..."], [/tabs]) in a Markdown source,
 * parses their attributes and builds a tree of paired (block) shortcodes.
 * All positions are offsets into the original source, so callers can slice or
 * replace parts of the source without losing anything else.
 *
 * Usage:
 *
 *   let tags = UTILS.MDShortcodes.scanTags( source, [ "tabs", "tab" ] );
 *   let tree = UTILS.MDShortcodes.parseTree( source, [ "tabs", "tab" ] );
 *   let attrs = UTILS.MDShortcodes.parseAttributes( "name=\"Tab 1\" class='x'" );
 *   let tag = UTILS.MDShortcodes.buildOpeningTag( "tab", { name: "Tab 1" } );
 *
 * Works in the browser (window.UTILS.MDShortcodes) and in Node.js (module.exports),
 * so it can be tested without a browser. Keep the syntax runnable in Node.js 9.
 */
( function( root ) {

  let escapeRegExp = function( string ) {
    return string.replace( /[.*+?^${}()|[\]\\]/g, "\\$&" );
  };

  // Replaces every character of the given string by a space, except line breaks
  let blank = function( string ) {
    return string.replace( /[^\r\n]/g, " " );
  };

  let MDShortcodes = {

    /**
     * Returns the source with fenced code blocks and inline code spans blanked out
     * (replaced by spaces), so shortcode-like text inside code is not taken for a tag.
     * The returned string has the same length and line breaks as the source.
     * @param {String} source
     * @returns {String}
     */
    maskCode( source ) {
      let lines = source.split( "\n" );
      let fence = null;
      lines = lines.map( line => {
        let m = line.match( /^ {0,3}(`{3,}|~{3,})/ );
        if ( fence ) {
          if ( m && m[ 1 ][ 0 ] === fence[ 0 ] && m[ 1 ].length >= fence.length && /^\s*$/.test( line.slice( line.indexOf( m[ 1 ] ) + m[ 1 ].length ) ) ) {
            fence = null;
          }
          return blank( line );
        }
        if ( m ) {
          fence = m[ 1 ];
          return blank( line );
        }
        return MDShortcodes.maskInlineCode( line );
      } );
      return lines.join( "\n" );
    },

    /**
     * Blanks out inline code spans (`code`, ``code``) in the given text
     * @param {String} text
     * @returns {String}
     */
    maskInlineCode( text ) {
      return text.replace( /(`+)[^`][\s\S]*?\1/g, blank );
    },

    /**
     * Finds all opening and closing tags of the given shortcodes.
     * Mirrors MarkdownShortcodesPrefilter of DrInk Markdown: an opening tag is "[name]"
     * or "[name params]", a closing tag is "[/name]".
     * @param {String} source
     * @param {Array} names - shortcode names, e.g. [ "row", "col" ]
     * @param {Object} options - { maskCode: true }
     * @returns {Array} [ { name, closing, attrs, params, raw, start, end } ]
     */
    scanTags( source, names, options ) {
      options = Object.assign( { maskCode: true }, options );
      if ( !names || !names.length ) {
        return [];
      }
      let haystack = options.maskCode ? MDShortcodes.maskCode( source ) : source;
      let namesStr = names.map( escapeRegExp ).join( "|" );
      let re = new RegExp( "\\[(?:\\/(" + namesStr + ")\\]|(" + namesStr + ")(\\s[^\\]]*)?\\])", "g" );
      let tags = [];
      let m;
      while ( ( m = re.exec( haystack ) ) ) {
        let closing = !!m[ 1 ];
        let params = closing ? "" : ( m[ 3 ] || "" );
        tags.push( {
          name: closing ? m[ 1 ] : m[ 2 ],
          closing: closing,
          params: params,
          attrs: closing ? {} : MDShortcodes.parseAttributes( params ),
          raw: source.slice( m.index, m.index + m[ 0 ].length ),
          start: m.index,
          end: m.index + m[ 0 ].length,
        } );
      }
      return tags;
    },

    /**
     * Builds a tree of paired shortcodes.
     * Unpaired closing tags and opening tags without a closing tag are reported in errors,
     * an unclosed node gets closeTag = null and spans to the end of the source.
     * @param {String} source
     * @param {Array} names
     * @returns {Object} { children: [ node ], errors: [ { message, tag } ] }
     *   node: { name, attrs, params, openTag, closeTag, start, end, innerStart, innerEnd, children }
     */
    parseTree( source, names ) {
      let rootNode = { children: [] };
      let stack = [ rootNode ];
      let errors = [];
      MDShortcodes.scanTags( source, names ).forEach( tag => {
        if ( !tag.closing ) {
          let node = {
            name: tag.name,
            attrs: tag.attrs,
            params: tag.params,
            openTag: tag,
            closeTag: null,
            start: tag.start,
            end: source.length,
            innerStart: tag.end,
            innerEnd: source.length,
            children: [],
          };
          stack[ stack.length - 1 ].children.push( node );
          stack.push( node );
          return;
        }
        // find the nearest open node of the same name
        let i = stack.length - 1;
        while ( i > 0 && stack[ i ].name !== tag.name ) {
          i--;
        }
        if ( i === 0 ) {
          errors.push( { message: "unexpected closing tag [/" + tag.name + "]", tag: tag } );
          return;
        }
        // nodes opened after the matched one are left unclosed
        while ( stack.length - 1 > i ) {
          let unclosed = stack.pop();
          errors.push( { message: "unclosed tag [" + unclosed.name + "]", tag: unclosed.openTag } );
        }
        let node = stack.pop();
        node.closeTag = tag;
        node.end = tag.end;
        node.innerEnd = tag.start;
      } );
      while ( stack.length > 1 ) {
        let unclosed = stack.pop();
        errors.push( { message: "unclosed tag [" + unclosed.name + "]", tag: unclosed.openTag } );
      }
      return { children: rootNode.children, errors: errors };
    },

    /**
     * Parses shortcode params the same way as MarkdownShortcodesPostfilter::parseParams()
     *   'name="Tab \"1\"" class=highlight id=\'x\'' -> { name: "Tab \"1\"", class: "highlight", id: "x" }
     * A param without a value (e.g. "[tab active]") gets an empty string.
     * @param {String} params
     * @returns {Object}
     */
    parseAttributes( params ) {
      let attrs = {};
      let re = /([a-z0-9_-]+)(?:\s*=\s*("(?:[^"\\]|\\.)*"|'(?:[^'\\]|\\.)*'|[^\s"']*))?/gi;
      let m;
      while ( ( m = re.exec( params || "" ) ) ) {
        let value = m[ 2 ] === undefined ? "" : m[ 2 ];
        if ( /^"[\s\S]*"$/.test( value ) ) {
          value = value.slice( 1, -1 ).replace( /\\"/g, "\"" );
        } else if ( /^'[\s\S]*'$/.test( value ) ) {
          value = value.slice( 1, -1 ).replace( /\\'/g, "'" );
        }
        attrs[ m[ 1 ] ] = value;
      }
      return attrs;
    },

    /**
     * Builds an opening tag, values are always double quoted
     *   ( "tab", { name: "Say \"hi\"" } ) -> '[tab name="Say \"hi\""]'
     * Note that DrInk Markdown doesn't allow "]" in params, callers must not pass it.
     * @param {String} name
     * @param {Object} attrs
     * @returns {String}
     */
    buildOpeningTag( name, attrs ) {
      let params = Object.keys( attrs || {} ).map( key => {
        let value = String( attrs[ key ] ).replace( /"/g, "\\\"" );
        return key + "=\"" + value + "\"";
      } );
      return "[" + name + ( params.length ? " " + params.join( " " ) : "" ) + "]";
    },

  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDShortcodes;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDShortcodes = MDShortcodes;
  }

} )( typeof window !== "undefined" ? window : this );

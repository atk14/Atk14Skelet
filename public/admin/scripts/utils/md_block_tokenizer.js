/**
 * Markdown block tokenizer
 * Splits a DrInk Markdown source into top-level blocks (paragraphs, headings, lists,
 * fenced code, tables, block shortcodes, iobjects...) for the block editor.
 *
 * It doesn't interpret Markdown, it only finds block boundaries. Every block keeps
 * its exact source (raw) and the whitespace separating it from the next block
 * (separator), so joining an untouched document gives back the original source:
 *
 *   let doc = UTILS.MDBlockTokenizer.tokenize( source, { blockShortcodes: [ "row", "col", "div", "tabs", "tab" ] } );
 *   UTILS.MDBlockTokenizer.join( doc ) === source; // true
 *
 *   doc = { leading: "", blocks: [ block ], trailing: "\n", definitions: [ "[1]: http://..." ] }
 *   block = { type, raw, separator, line, lineCount, ...type specific fields }
 *
 * Block types and their specific fields:
 *   heading (level), paragraph, list (ordered), quote, table, code, hr, html (tag),
 *   shortcode (name, attrs, params, unclosed), iobject (id), definitions
 *
 * Block shortcodes are kept whole, including blank lines and nested shortcodes inside.
 * Positions (line, lineCount) are 0-based line numbers in the original source.
 *
 * After blocks are moved, inserted or removed, call normalize( doc ) so that every
 * block is separated by a blank line.
 *
 * Dependencies: md_shortcodes.js
 * Works in the browser (window.UTILS.MDBlockTokenizer) and in Node.js (module.exports).
 * Keep the syntax runnable in Node.js 9.
 */
( function( root ) {

  let MDShortcodes = ( typeof module !== "undefined" && module.exports ) ? require( "./md_shortcodes.js" ) : root.UTILS.MDShortcodes;

  // Block level HTML elements; an HTML block lasts until its element is closed
  let HTML_BLOCK_TAGS = [
    "address", "article", "aside", "audio", "blockquote", "details", "dialog", "div", "dl", "fieldset",
    "figure", "footer", "form", "h1", "h2", "h3", "h4", "h5", "h6", "header", "hr", "iframe", "nav",
    "ol", "p", "picture", "pre", "script", "section", "style", "table", "ul", "video",
  ];

  let RE = {
    blank: /^[ \t\r]*$/,
    fence: /^ {0,3}(`{3,}|~{3,})/,
    atxHeading: /^ {0,3}(#{1,6})(?:[ \t]|\r?$)/,
    setextUnderline: /^ {0,3}(=+|-+)[ \t\r]*$/,
    hr: /^ {0,3}([-*_])(?:[ \t]*\1){2,}[ \t\r]*$/,
    listItem: /^ {0,3}(?:([-*+])|(\d{1,9})[.)])(?:[ \t]|\r?$)/,
    indented: /^(?: {2,}|\t)\S/,
    quote: /^ {0,3}>/,
    tableRow: /^ {0,3}\|/,
    tableDelimiter: /^ {0,3}\|?[ \t]*:?-+:?[ \t]*(?:\|[ \t]*:?-+:?[ \t]*)+\|?[ \t\r]*$/,
    definition: /^ {0,3}\[[^\]]+\]:[ \t]*\S/,
    iobject: /^\[#(\d+)[^\]]*\]$/,
    htmlOpen: /^ {0,3}<([a-zA-Z][a-zA-Z0-9-]*)(?:[\s>/]|$)/,
  };

  let escapeRegExp = function( string ) {
    return string.replace( /[.*+?^${}()|[\]\\]/g, "\\$&" );
  };

  // "bullet" or "ordered" for a list item line, null otherwise
  let listKind = function( line ) {
    let m = line.match( RE.listItem );
    if ( !m || RE.hr.test( line ) ) {
      return null;
    }
    return m[ 1 ] ? "bullet" : "ordered";
  };

  // Returns the fence string (e.g. "```") when the line opens a fenced code block
  let fenceOpening = function( line ) {
    let m = line.match( RE.fence );
    return m ? m[ 1 ] : null;
  };

  let isFenceClosing = function( line, fence ) {
    let m = line.match( RE.fence );
    if ( !m || m[ 1 ][ 0 ] !== fence[ 0 ] || m[ 1 ].length < fence.length ) {
      return false;
    }
    return RE.blank.test( line.slice( line.indexOf( m[ 1 ] ) + m[ 1 ].length ) );
  };

  /**
   * Tokenizer for a single document
   */
  let Tokenizer = class {

    constructor( source, options ) {
      this.source = source;
      this.blockShortcodes = options.blockShortcodes;
      this.lines = source.split( "\n" );
      this.lineStarts = [];
      let pos = 0;
      this.lines.forEach( line => {
        this.lineStarts.push( pos );
        pos += line.length + 1;
      } );
    }

    lineEnd( i ) {
      return this.lineStarts[ i ] + this.lines[ i ].length;
    }

    // Block shortcode tags on a single line (inline code is ignored)
    tagsOnLine( line ) {
      return MDShortcodes.scanTags( MDShortcodes.maskInlineCode( line ), this.blockShortcodes, { maskCode: false } );
    }

    // Change of block shortcode nesting depth caused by the line
    depthChange( line ) {
      return this.tagsOnLine( line ).reduce( ( sum, tag ) => sum + ( tag.closing ? -1 : 1 ), 0 );
    }

    // The opening tag when the line starts with a block shortcode, null otherwise
    shortcodeOpening( line ) {
      let tags = this.tagsOnLine( line );
      let indent = line.match( /^[ \t]*/ )[ 0 ].length;
      if ( tags.length && !tags[ 0 ].closing && tags[ 0 ].start === indent ) {
        return tags[ 0 ];
      }
      return null;
    }

    tokenize() {
      let n = this.lines.length;
      let doc = { leading: "", blocks: [], trailing: "", definitions: this.findDefinitions() };
      let i = 0;
      while ( i < n && RE.blank.test( this.lines[ i ] ) ) {
        i++;
      }
      doc.leading = this.source.slice( 0, i < n ? this.lineStarts[ i ] : this.source.length );

      while ( i < n ) {
        let last = this.consumeBlock( i );
        let block = this.classify( i, last );
        let j = last + 1;
        while ( j < n && RE.blank.test( this.lines[ j ] ) ) {
          j++;
        }
        let separator = this.source.slice( this.lineEnd( last ), j < n ? this.lineStarts[ j ] : this.source.length );
        if ( j < n ) {
          block.separator = separator;
        } else {
          doc.trailing = separator;
        }
        doc.blocks.push( block );
        i = j;
      }
      return doc;
    }

    /**
     * Finds the last line of the block starting at line i
     * @returns {Number}
     */
    consumeBlock( i ) {
      let n = this.lines.length;
      let first = this.lines[ i ];

      // fenced code
      let fence = fenceOpening( first );
      if ( fence ) {
        let k = i + 1;
        while ( k < n && !isFenceClosing( this.lines[ k ], fence ) ) {
          k++;
        }
        return Math.min( k, n - 1 );
      }

      // block shortcode: until its closing tag
      if ( this.shortcodeOpening( first ) ) {
        return this.consumeShortcode( i );
      }

      if ( RE.atxHeading.test( first ) || RE.hr.test( first ) ) {
        return i;
      }

      // HTML block: until the element is closed
      let m = first.match( RE.htmlOpen );
      if ( m && HTML_BLOCK_TAGS.indexOf( m[ 1 ].toLowerCase() ) >= 0 ) {
        let last = this.consumeHtml( i, m[ 1 ] );
        if ( last !== null ) {
          return last;
        }
      }

      return this.consumeGeneric( i );
    }

    consumeShortcode( i ) {
      let n = this.lines.length;
      let depth = 0;
      let fence = null;
      for ( let k = i; k < n; k++ ) {
        let line = this.lines[ k ];
        if ( fence ) {
          if ( isFenceClosing( line, fence ) ) {
            fence = null;
          }
          continue;
        }
        if ( k > i && ( fence = fenceOpening( line ) ) ) {
          continue;
        }
        depth = Math.max( 0, depth + this.depthChange( line ) );
        if ( depth === 0 ) {
          return k;
        }
      }
      return n - 1; // unclosed shortcode spans to the end of the document
    }

    // Returns null when the element is not closed till the end of the document
    consumeHtml( i, tagName ) {
      let n = this.lines.length;
      let reOpen = new RegExp( "<" + escapeRegExp( tagName ) + "(?=[\\s>/]|$)[^>]*?(/)?>", "gi" );
      let reClose = new RegExp( "</" + escapeRegExp( tagName ) + "\\s*>", "gi" );
      let balance = 0;
      for ( let k = i; k < n; k++ ) {
        let line = this.lines[ k ];
        let m;
        while ( ( m = reOpen.exec( line ) ) ) {
          if ( !m[ 1 ] ) {
            balance++;
          }
        }
        balance -= ( line.match( reClose ) || [] ).length;
        if ( balance <= 0 ) {
          return k;
        }
      }
      return null;
    }

    // Paragraphs, lists, quotes, tables...: until a blank line
    consumeGeneric( i ) {
      let n = this.lines.length;
      let kind = listKind( this.lines[ i ] );
      let depth = 0;
      let fence = null;
      let k = i;
      for ( ;; ) {
        let line = this.lines[ k ];
        if ( fence ) {
          if ( isFenceClosing( line, fence ) ) {
            fence = null;
          }
        } else if ( k > i && ( fence = fenceOpening( line ) ) ) {
          // a fence inside the block, blank lines in it don't end the block
        } else {
          depth = Math.max( 0, depth + this.depthChange( line ) );
        }

        let next = k + 1;
        if ( next >= n ) {
          return k;
        }
        if ( fence || depth > 0 ) {
          k = next;
          continue;
        }
        let nextLine = this.lines[ next ];
        if ( RE.blank.test( nextLine ) ) {
          // a loose list continues after blank lines with an item of the same kind or an indented line
          if ( kind ) {
            let j = next;
            while ( j < n && RE.blank.test( this.lines[ j ] ) ) {
              j++;
            }
            if ( j < n && ( listKind( this.lines[ j ] ) === kind || RE.indented.test( this.lines[ j ] ) ) ) {
              k = j;
              continue;
            }
          }
          return k;
        }
        // a heading or a block shortcode starts a new block even without a blank line
        if ( RE.atxHeading.test( nextLine ) || this.shortcodeOpening( nextLine ) ) {
          return k;
        }
        k = next;
      }
    }

    classify( first, last ) {
      let lines = this.lines.slice( first, last + 1 );
      let raw = this.source.slice( this.lineStarts[ first ], this.lineEnd( last ) );
      let block = { type: "paragraph", raw: raw, separator: "\n\n", line: first, lineCount: last - first + 1 };
      let firstLine = lines[ 0 ];
      let m;

      if ( fenceOpening( firstLine ) ) {
        block.type = "code";
      } else if ( ( m = this.shortcodeOpening( firstLine ) ) ) {
        block.type = "shortcode";
        block.name = m.name;
        block.attrs = m.attrs;
        block.params = m.params;
        let tree = MDShortcodes.parseTree( raw, this.blockShortcodes );
        block.unclosed = !tree.children.length || !tree.children[ 0 ].closeTag;
      } else if ( ( m = firstLine.match( RE.atxHeading ) ) ) {
        block.type = "heading";
        block.level = m[ 1 ].length;
      } else if ( lines.length === 2 && ( m = lines[ 1 ].match( RE.setextUnderline ) ) && !listKind( firstLine ) ) {
        block.type = "heading";
        block.level = m[ 1 ][ 0 ] === "=" ? 1 : 2;
      } else if ( RE.hr.test( firstLine ) && lines.length === 1 ) {
        block.type = "hr";
      } else if ( RE.iobject.test( raw.trim() ) ) {
        block.type = "iobject";
        block.id = parseInt( raw.trim().match( RE.iobject )[ 1 ], 10 );
      } else if ( lines.every( line => RE.definition.test( line ) || RE.blank.test( line ) ) ) {
        block.type = "definitions";
      } else if ( ( m = listKind( firstLine ) ) ) {
        block.type = "list";
        block.ordered = m === "ordered";
      } else if ( RE.quote.test( firstLine ) ) {
        block.type = "quote";
      } else if ( RE.tableRow.test( firstLine ) || ( lines.length > 1 && RE.tableDelimiter.test( lines[ 1 ] ) ) ) {
        block.type = "table";
      } else if ( ( m = firstLine.match( RE.htmlOpen ) ) && HTML_BLOCK_TAGS.indexOf( m[ 1 ].toLowerCase() ) >= 0 ) {
        block.type = "html";
        block.tag = m[ 1 ].toLowerCase();
      }
      return block;
    }

    // Link reference definitions anywhere in the document (outside fenced code)
    findDefinitions() {
      let definitions = [];
      let fence = null;
      this.lines.forEach( line => {
        if ( fence ) {
          if ( isFenceClosing( line, fence ) ) {
            fence = null;
          }
          return;
        }
        if ( ( fence = fenceOpening( line ) ) ) {
          return;
        }
        if ( RE.definition.test( line ) ) {
          definitions.push( line.trim() );
        }
      } );
      return definitions;
    }
  };

  let MDBlockTokenizer = {

    // Block shortcodes built into DrInk Markdown; the full list depends on app/helpers/block.drink_shortcode__*.php
    defaultBlockShortcodes: [ "row", "col", "div" ],

    /**
     * Splits the source into blocks
     * @param {String} source
     * @param {Object} options - { blockShortcodes: [ "row", "col", "div", ... ] }
     * @returns {Object} { leading, blocks, trailing, definitions }
     */
    tokenize( source, options ) {
      options = Object.assign( { blockShortcodes: MDBlockTokenizer.defaultBlockShortcodes }, options );
      return new Tokenizer( String( source || "" ), options ).tokenize();
    },

    /**
     * Joins the blocks back into a Markdown source
     * @param {Object} doc - { leading, blocks, trailing } as returned by tokenize()
     * @returns {String}
     */
    join( doc ) {
      let blocks = doc.blocks;
      let out = doc.leading || "";
      blocks.forEach( ( block, index ) => {
        out += block.raw;
        if ( index < blocks.length - 1 ) {
          out += typeof block.separator === "string" ? block.separator : "\n\n";
        }
      } );
      return out + ( doc.trailing || "" );
    },

    /**
     * Makes sure every block is separated from the next one by a blank line.
     * Needed after blocks were moved, inserted or removed: e.g. a paragraph
     * followed by a heading on the very next line is fine, but the same paragraph
     * followed by another paragraph would be merged with it.
     * When indexes are given, only separators following these blocks are fixed,
     * so the rest of the document is left untouched.
     * @param {Object} doc
     * @param {Array} indexes - optional, e.g. [ index - 1, index ] around a changed block
     * @returns {Object} the same doc
     */
    normalize( doc, indexes ) {
      doc.blocks.forEach( ( block, index ) => {
        if ( indexes && indexes.indexOf( index ) < 0 ) {
          return;
        }
        if ( typeof block.separator !== "string" || !/\n[ \t\r]*\n/.test( block.separator ) ) {
          block.separator = "\n\n";
        }
      } );
      return doc;
    },

  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDBlockTokenizer;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDBlockTokenizer = MDBlockTokenizer;
  }

} )( typeof window !== "undefined" ? window : this );

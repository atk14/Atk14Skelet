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
 *   shortcode (name, attrs, params, unclosed, single), iobject (id), definitions
 *
 * A paragraph consisting only of one function shortcode (options.functionShortcodes, shortcodes without
 * a closing tag, e.g. [spacer height="2rem"]) is a shortcode block too, with single = true.
 *
 * Block shortcodes are kept whole, including blank lines and nested shortcodes inside.
 * Positions (line, lineCount) are 0-based line numbers in the original source.
 *
 * Container shortcodes (options.containers, by default [row] with [col] children, [tabs] with [tab] children
 * and [div] with its own content as the only child) get their children's
 * content tokenized too, so it can be edited as blocks:
 *
 *   block.container = {
 *     head: "[row class=\"x\"]\n",                  // the source before the first child
 *     children: [ { name: "col", attrs, open: "[col]", doc: { leading, blocks, trailing }, close: "[/col]", after: "\n" } ],
 *     tail: "[/row]",                                // the closing tag
 *   }
 *
 * join() builds the raw of such a block from its parts (see blockRaw()), so a change in a child's doc
 * gets into the source. A container block whose raw is replaced as a whole must lose its container
 * (delete block.container). A shortcode not having the expected structure (text between children,
 * other shortcodes as children, unpaired tags...) is left as a plain block.
 *
 * After blocks are moved, inserted or removed, call normalize( doc ) so that every
 * block is separated by a blank line (and content of a container child spanning more lines
 * starts and ends on its own line).
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

  // Moves line numbers of all blocks (nested ones included) by the given number of lines
  let shiftLines = function( doc, delta ) {
    doc.blocks.forEach( block => {
      block.line += delta;
      if ( block.container ) {
        block.container.children.forEach( child => shiftLines( child.doc, delta ) );
      }
    } );
  };

  /**
   * Tokenizer for a single document
   */
  let Tokenizer = class {

    constructor( source, options ) {
      this.source = source;
      this.options = options;
      this.blockShortcodes = options.blockShortcodes;
      this.functionShortcodes = options.functionShortcodes || [];
      this.containers = options.containers || {};
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

    // The tag when the whole text is one function shortcode (e.g. [spacer height="2rem"]), null otherwise
    functionShortcode( text ) {
      let tags = MDShortcodes.scanTags( text, this.functionShortcodes );
      if ( tags.length === 1 && !tags[ 0 ].closing && tags[ 0 ].start === 0 && tags[ 0 ].end === text.length ) {
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
        let container = this.containers[ m.name ] && !block.unclosed && this.parseContainer( raw, tree, this.containers[ m.name ], first );
        if ( container ) {
          block.container = container;
        }
      } else if ( ( m = this.functionShortcode( raw.trim() ) ) ) {
        block.type = "shortcode";
        block.name = m.name;
        block.attrs = m.attrs;
        block.params = m.params;
        block.unclosed = false;
        block.single = true;
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

    /**
     * Splits a container shortcode into its children and tokenizes their content;
     * returns null when the shortcode doesn't have the expected structure
     * @param {String} raw - source of the block
     * @param {Object} tree - MDShortcodes.parseTree() of raw
     * @param {Object} config - e.g. { children: "col" }
     * @param {Number} firstLine - line of the block in the source
     * @returns {Object|null}
     */
    parseContainer( raw, tree, config, firstLine ) {
      if ( tree.errors.length || tree.children.length !== 1 ) {
        return null;
      }
      let node = tree.children[ 0 ];
      let indent = raw.match( /^[ \t]*/ )[ 0 ].length;
      let blockStart = this.lineStarts[ firstLine ];
      if ( config.content ) {
        // the shortcode's own content are blocks, e.g. [div]; it's the only "child"
        if ( node.start !== indent || raw.slice( node.end ).trim() ) {
          return null;
        }
        return {
          head: raw.slice( 0, node.start ),
          children: [ this.containerChild( raw, node, blockStart ) ],
          tail: raw.slice( node.end ),
        };
      }
      // kept children (e.g. [collapse_title] of a [collapse]) stay in the source around the children, as they are
      let keep = config.keep || [];
      let contentChildren = node.children.filter( child => keep.indexOf( child.name ) < 0 );
      if ( node.start !== indent || raw.slice( node.end ).trim() || !contentChildren.length ) {
        return null;
      }
      // text between two positions without the kept children in it
      let gap = ( from, to ) => {
        let text = "";
        node.children.forEach( child => {
          if ( keep.indexOf( child.name ) >= 0 && child.start >= from && child.end <= to ) {
            text += raw.slice( from, child.start );
            from = child.end;
          }
        } );
        return text + raw.slice( from, to );
      };
      let children = [];
      let pos = node.innerStart;
      for ( let child of contentChildren ) {
        // only whitespace (and kept children) may be between children
        if ( child.name !== config.children || gap( pos, child.start ).trim() ) {
          return null;
        }
        children.push( this.containerChild( raw, child, blockStart ) );
        if ( children.length > 1 ) {
          children[ children.length - 2 ].after = raw.slice( pos, child.start );
        }
        pos = child.end;
      }
      if ( gap( pos, node.innerEnd ).trim() ) {
        return null;
      }
      children[ children.length - 1 ].after = raw.slice( pos, node.innerEnd );
      return {
        head: raw.slice( 0, contentChildren[ 0 ].start ),
        children: children,
        tail: raw.slice( node.innerEnd ),
      };
    }

    /**
     * A child of a container with its content tokenized
     * @param {String} raw - source of the container block
     * @param {Object} node - the child's node of MDShortcodes.parseTree( raw )
     * @param {Number} blockStart - offset of the container block in the source
     * @returns {Object} { name, attrs, params, open, doc, close, after }
     */
    containerChild( raw, node, blockStart ) {
      let doc = new Tokenizer( raw.slice( node.innerStart, node.innerEnd ), this.options ).tokenize();
      // lines of the child's blocks in the whole source
      shiftLines( doc, this.source.slice( 0, blockStart + node.innerStart ).split( "\n" ).length - 1 );
      doc.nested = true;
      return {
        name: node.name,
        attrs: node.attrs,
        params: node.params,
        open: raw.slice( node.start, node.innerStart ),
        doc: doc,
        close: raw.slice( node.innerEnd, node.end ),
        after: "",
      };
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

  // The first line of a heading: 1 = text (with "#"s), 2 = special attributes in braces, 3 = trailing whitespace
  let HEADING_ATTRS = /^(.*?)(?:[ ]+\{((?:[ ]*[#.a-z][-_:a-zA-Z0-9=]+)+)[ ]*\})?([ \t]*\r?)$/;

  let MDBlockTokenizer = {

    // Block shortcodes built into DrInk Markdown; the full list depends on app/helpers/block.drink_shortcode__*.php
    defaultBlockShortcodes: [ "row", "col", "div" ],

    // Function shortcodes (without a closing tag); the editor gets the list from app/helpers/function.drink_shortcode__*.php
    defaultFunctionShortcodes: [],

    // Container shortcodes whose children's content is tokenized too
    // (children: name of the children shortcode, or content: the shortcode's own content are blocks;
    // nestable: a container of the same kind can be put into it in the editor;
    // keep: other children allowed among the children, kept in the source as they are, e.g. the title of a [collapse])
    defaultContainers: {
      row: { children: "col" },
      tabs: { children: "tab" },
      div: { content: true, nestable: true },
      collapse: { children: "collapse_content", keep: [ "collapse_title" ] },
    },

    /**
     * Splits the source into blocks
     * @param {String} source
     * @param {Object} options - { blockShortcodes: [ "row", "col", "div", ... ], functionShortcodes: [ "spacer" ], containers: { row: { children: "col" } } }
     * @returns {Object} { leading, blocks, trailing, definitions }
     */
    tokenize( source, options ) {
      options = Object.assign( { blockShortcodes: MDBlockTokenizer.defaultBlockShortcodes, functionShortcodes: MDBlockTokenizer.defaultFunctionShortcodes, containers: MDBlockTokenizer.defaultContainers }, options );
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
        out += MDBlockTokenizer.blockRaw( block );
        if ( index < blocks.length - 1 ) {
          out += typeof block.separator === "string" ? block.separator : "\n\n";
        }
      } );
      return out + ( doc.trailing || "" );
    },

    /**
     * Source of a heading with another level: an ATX heading gets other "#"s, a setext heading keeps
     * its underline for levels 1 and 2 ("=" or "-" of the same length) and becomes an ATX one for the others
     *   ( "## Title", 3 ) -> "### Title"
     *   ( "Title\n=====", 2 ) -> "Title\n-----"
     * @param {String} raw - source of a heading block
     * @param {Number} level - 1 to 6
     * @returns {String}
     */
    setHeadingLevel( raw, level ) {
      let lines = raw.split( "\n" );
      let m;
      if ( lines.length === 2 && ( m = lines[ 1 ].match( /^( {0,3})(=+|-+)([ \t\r]*)$/ ) ) ) {
        if ( level <= 2 ) {
          return lines[ 0 ] + "\n" + m[ 1 ] + ( level === 1 ? "=" : "-" ).repeat( m[ 2 ].length ) + m[ 3 ];
        }
        return "#".repeat( level ) + " " + lines[ 0 ].trim();
      }
      return raw.replace( /^( {0,3})#{1,6}(?=[ \t]|\r?$)/, ( all, indent ) => indent + "#".repeat( level ) );
    },

    /**
     * Special attributes of a heading (PHP Markdown Extra, kept by DrinkMarkdown): "## Title {#anchor .lead .mb-4}"
     *   ( "## Title ## {#intro .lead}" ) -> { classes: [ "lead" ], id: "intro" }
     * @param {String} raw - source of a heading block
     * @returns {Object} { classes, id }
     */
    headingAttrs( raw ) {
      let m = raw.split( "\n" )[ 0 ].match( HEADING_ATTRS );
      let out = { classes: [], id: "" };
      ( m && m[ 2 ] ? m[ 2 ].match( /[#.a-z][-_:a-zA-Z0-9=]+/g ) : [] ).forEach( el => {
        if ( el[ 0 ] === "." ) {
          out.classes.push( el.slice( 1 ) );
        } else if ( el[ 0 ] === "#" && !out.id ) {
          out.id = el.slice( 1 );
        }
      } );
      return out;
    },

    /**
     * Source of a heading with the given classes and id; other attributes in the braces are kept,
     * the braces are removed when nothing's left. Only characters Markdown Extra allows are written:
     * classes with others are left out, an id is made of the allowed ones ("Úvod a cíl" -> "Uvod-a-cil").
     * @param {String} raw - source of a heading block
     * @param {Object} attrs - { classes: [ "lead" ] or "lead mb-4", id: "intro" }
     * @returns {String}
     */
    setHeadingAttrs( raw, attrs ) {
      let lines = raw.split( "\n" );
      let m = lines[ 0 ].match( HEADING_ATTRS );
      let classes = typeof attrs.classes === "string" ? attrs.classes.split( /\s+/ ) : attrs.classes || [];
      classes = classes.filter( c => /^[-_:a-zA-Z0-9]+$/.test( c ) );
      let id = String( attrs.id || "" ).trim().normalize( "NFD" ).replace( /[\u0300-\u036f]/g, "" )
        .replace( /\s+/g, "-" ).replace( /[^-_:a-zA-Z0-9]/g, "" );
      let others = ( m[ 2 ] ? m[ 2 ].match( /[#.a-z][-_:a-zA-Z0-9=]+/g ) : [] ).filter( el => el[ 0 ] !== "." && el[ 0 ] !== "#" );
      let elements = ( id ? [ "#" + id ] : [] ).concat( classes.map( c => "." + c ), others );
      let current = MDBlockTokenizer.headingAttrs( raw );
      if ( current.id === id && current.classes.join( " " ) === classes.join( " " ) ) {
        return raw; // nothing changed, the source is kept exactly as it was
      }
      lines[ 0 ] = m[ 1 ] + ( elements.length ? " {" + elements.join( " " ) + "}" : "" ) + m[ 3 ];
      return lines.join( "\n" );
    },

    /**
     * Source of a block; the raw of a container block is built from its parts (and updated)
     * @param {Object} block
     * @returns {String}
     */
    blockRaw( block ) {
      if ( block.container ) {
        let c = block.container;
        block.raw = c.head + c.children.map( child => child.open + MDBlockTokenizer.join( child.doc ) + child.close + child.after ).join( "" ) + c.tail;
      }
      return block.raw;
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
      let multiline = doc.blocks.length > 1 || doc.blocks.some( block => MDBlockTokenizer.blockRaw( block ).indexOf( "\n" ) >= 0 );
      let onOwnLines = doc.blocks.length && ( doc.leading.indexOf( "\n" ) >= 0 || doc.trailing.indexOf( "\n" ) >= 0 );
      if ( doc.nested && ( multiline || onOwnLines ) ) {
        // more lines inside a container child (e.g. "[col]Text[/col]") go on their own lines,
        // and content starting on its own line ends on its own line too (e.g. "[div]\nText[/div]")
        if ( doc.leading.indexOf( "\n" ) < 0 ) {
          doc.leading = "\n";
        }
        if ( doc.trailing.indexOf( "\n" ) < 0 ) {
          doc.trailing = "\n";
        }
      }
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

    /**
     * Puts the blocks into a new order, e.g. after a block was dragged elsewhere.
     * Separators are fixed (see normalize()) only between blocks that became neighbours,
     * the rest of the document is left untouched.
     * @param {Object} doc
     * @param {Array} order - current indexes of the blocks in their new order, e.g. [ 0, 2, 1 ]
     * @returns {Object} the same doc
     */
    reorder( doc, order ) {
      if ( order.length !== doc.blocks.length || order.slice().sort( ( a, b ) => a - b ).some( ( index, i ) => index !== i ) ) {
        throw new Error( "MDBlockTokenizer.reorder(): the order must contain every block index exactly once" );
      }
      let blocks = doc.blocks;
      doc.blocks = order.map( index => blocks[ index ] );
      let changed = [];
      order.forEach( ( index, i ) => {
        if ( i < order.length - 1 && order[ i + 1 ] !== index + 1 ) {
          changed.push( i );
        }
      } );
      return MDBlockTokenizer.normalize( doc, changed );
    },

  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDBlockTokenizer;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDBlockTokenizer = MDBlockTokenizer;
  }

} )( typeof window !== "undefined" ? window : this );

/**
 * Layout of a DrInk Markdown [row] shortcode
 * Reads and writes widths of [col] columns per Bootstrap breakpoint, keeping everything else:
 * content of the columns, other attributes and classes, whitespace between the tags.
 * Used by the layout designer (layout_designer.js) to edit an existing [row].
 *
 *   let model = UTILS.MDRowLayout.parse( "[row]\n[col]A[/col]\n[col]B[/col]\n[/row]" );
 *   model.cells[ 0 ].sizes; // { xs: 12, sm: 12, md: 6, lg: 6, xl: 6 } (default classes of a 2-column row)
 *   UTILS.MDRowLayout.serialize( model, [ { xs: 12, sm: 12, md: 4, lg: 4, xl: 4 }, model.cells[ 1 ].sizes ] );
 *   // "[row]\n[col class=\"col-12 col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4\" defaultclasses=0]A[/col]\n[col]B[/col]\n[/row]"
 *
 * Sizes are spans 1-12 for breakpoints xs, sm, md, lg, xl; 0 means a hidden column.
 *
 * Dependencies: md_shortcodes.js
 * Works in the browser (window.UTILS.MDRowLayout) and in Node.js (module.exports).
 */
( function( root ) {

  let MDShortcodes = ( typeof module !== "undefined" && module.exports ) ? require( "./md_shortcodes.js" ) : root.UTILS.MDShortcodes;

  let BREAKPOINTS = [ "xs", "sm", "md", "lg", "xl" ];

  let MDRowLayout = {

    breakpoints: BREAKPOINTS,

    /**
     * Classes a [col] gets from DrInk Markdown unless it has defaultclasses=0
     * (see shared/helpers/drink_shortcodes/_col.tpl)
     * @param {Number} count - number of columns in the row
     * @returns {Array}
     */
    defaultClasses( count ) {
      if ( count === 4 ) {
        return [ "col-12", "col-xs-12", "col-md-6", "col-lg-3" ];
      }
      if ( count === 6 ) {
        return [ "col-12", "col-xs-12", "col-md-6", "col-lg-4", "col-xl-2" ];
      }
      return [ "col-12", "col-xs-12", "col-md-" + Math.round( 12 / count ) ];
    },

    /**
     * Widths per breakpoint from Bootstrap grid classes; other classes are returned as extra.
     * Returns null when the classes can't be represented (auto-width columns, conflicting widths).
     *   [ "col-12", "col-md-6", "d-none", "d-md-block", "text-center" ]
     *     -> { sizes: { xs: 0, sm: 0, md: 6, lg: 6, xl: 6 }, extra: [ "text-center" ] }
     * @param {Array} classes
     * @returns {Object|null} { sizes, extra }
     */
    sizesFromClasses( classes ) {
      let spans = {};
      let display = {};
      let extra = [];
      for ( let cls of classes ) {
        let m;
        if ( ( m = cls.match( /^col(?:-(xs|sm|md|lg|xl))?-(\d{1,2})$/ ) ) ) {
          let bp = m[ 1 ] || "xs";
          let span = parseInt( m[ 2 ], 10 );
          if ( span < 1 || span > 12 || ( spans[ bp ] !== undefined && spans[ bp ] !== span ) ) {
            return null;
          }
          spans[ bp ] = span;
        } else if ( /^col(?:-(?:xs|sm|md|lg|xl))?(?:-auto)?$/.test( cls ) ) {
          return null; // auto width
        } else if ( ( m = cls.match( /^d(?:-(xs|sm|md|lg|xl))?-(none|block)$/ ) ) ) {
          display[ m[ 1 ] || "xs" ] = m[ 2 ];
        } else if ( extra.indexOf( cls ) < 0 ) {
          extra.push( cls );
        }
      }
      // mobile first: a width or visibility applies up to the next breakpoint changing it
      let sizes = {};
      let span = 12;
      let hidden = false;
      BREAKPOINTS.forEach( bp => {
        if ( spans[ bp ] !== undefined ) {
          span = spans[ bp ];
        }
        if ( display[ bp ] ) {
          hidden = display[ bp ] === "none";
        }
        sizes[ bp ] = hidden ? 0 : span;
      } );
      return { sizes: sizes, extra: extra };
    },

    /**
     * Bootstrap grid classes for the given widths (the format the layout designer has always produced)
     *   { xs: 0, sm: 0, md: 4, lg: 3, xl: 2 } -> "d-none d-xs-none d-sm-none col-md-4 d-md-block col-lg-3 col-xl-2"
     * @param {Object} sizes
     * @returns {String}
     */
    classesFromSizes( sizes ) {
      let classes = [];
      let hidden = false;
      BREAKPOINTS.forEach( bp => {
        let span = sizes[ bp ];
        if ( span === 0 ) {
          hidden = true;
          if ( bp === "xs" ) {
            classes.push( "d-none" );
          }
          classes.push( "d-" + bp + "-none" );
        } else {
          if ( bp === "xs" ) {
            classes.push( "col-" + span );
          }
          classes.push( "col-" + bp + "-" + span );
          if ( hidden ) {
            classes.push( "d-" + bp + "-block" );
            hidden = false;
          }
        }
      } );
      return classes.join( " " );
    },

    sameSizes( a, b ) {
      return BREAKPOINTS.every( bp => a[ bp ] === b[ bp ] );
    },

    /**
     * Parses a [row] shortcode; returns null when it's not a [row] made of [col] columns only
     * (text between columns, unpaired tags, more than 12 columns, widths not representable...)
     * @param {String} raw
     * @returns {Object|null} { head, tail, cells: [ { open, attrs, classes, defaults, content, close, after, sizes, extra } ] }
     */
    parse( raw ) {
      let source = raw.trim();
      let tree = MDShortcodes.parseTree( source, [ "row", "col" ] );
      if ( tree.errors.length || tree.children.length !== 1 ) {
        return null;
      }
      let row = tree.children[ 0 ];
      let nodes = row.children;
      if ( row.name !== "row" || row.start !== 0 || row.end !== source.length || !nodes.length || nodes.length > 12 ) {
        return null;
      }
      let cells = [];
      for ( let i = 0; i < nodes.length; i++ ) {
        let node = nodes[ i ];
        let previousEnd = i ? nodes[ i - 1 ].end : row.innerStart;
        if ( node.name !== "col" || source.slice( previousEnd, node.start ).trim() ) {
          return null;
        }
        let classes = ( node.attrs.class || "" ).split( /\s+/ ).filter( c => c );
        let defaults = !/^(0|false|)$/i.test( node.attrs.defaultclasses === undefined ? "1" : node.attrs.defaultclasses );
        let layout = MDRowLayout.sizesFromClasses( ( defaults ? MDRowLayout.defaultClasses( nodes.length ) : [] ).concat( classes ) );
        if ( !layout ) {
          return null;
        }
        // only own classes are kept (the default ones are grid classes)
        let own = MDRowLayout.sizesFromClasses( classes );
        cells.push( {
          open: source.slice( node.start, node.innerStart ),
          attrs: node.attrs,
          classes: classes,
          defaults: defaults,
          content: source.slice( node.innerStart, node.innerEnd ),
          close: source.slice( node.innerEnd, node.end ),
          after: source.slice( node.end, i < nodes.length - 1 ? nodes[ i + 1 ].start : row.innerEnd ),
          sizes: layout.sizes,
          extra: own ? own.extra : layout.extra,
        } );
      }
      if ( source.slice( nodes[ nodes.length - 1 ].end, row.innerEnd ).trim() ) {
        return null;
      }
      return { head: source.slice( 0, nodes[ 0 ].start ), tail: source.slice( row.innerEnd ), cells: cells };
    },

    /**
     * Builds the [row] with the given widths of columns.
     * A column keeping its widths keeps its opening tag as it was; a changed one gets explicit classes
     * (and defaultclasses=0), its other attributes and classes are kept. Added columns are empty,
     * content of removed columns is moved to the last column, so nothing is lost.
     * @param {Object} model - from parse()
     * @param {Array} sizesList - widths of the columns, the number of columns may differ from the model
     * @returns {String}
     */
    serialize( model, sizesList ) {
      let original = model.cells;
      let count = sizesList.length;
      let cells = sizesList.map( ( sizes, i ) => {
        let o = original[ i ];
        // default classes depend on the number of columns, so a column relying on them is checked with the new number
        let current = o && MDRowLayout.sizesFromClasses( ( o.defaults ? MDRowLayout.defaultClasses( count ) : [] ).concat( o.classes ) );
        let open = current && MDRowLayout.sameSizes( current.sizes, sizes ) ? o.open : MDRowLayout.buildOpeningTag( o ? o.attrs : {}, o ? o.extra : [], sizes );
        return { open: open, content: o ? o.content : "", close: o ? o.close : "[/col]" };
      } );

      let moved = original.slice( count ).map( c => c.content );
      if ( moved.some( content => content.trim() ) ) {
        let parts = [ cells[ count - 1 ].content ].concat( moved )
          .map( content => content.replace( /^(?:[ \t]*\r?\n)+/, "" ).replace( /\s+$/, "" ) )
          .filter( content => content );
        cells[ count - 1 ].content = "\n" + parts.join( "\n\n" ) + "\n";
      }

      let gap = original.length > 1 ? original[ 0 ].after : "\n";
      let lastAfter = original[ original.length - 1 ].after;
      return model.head + cells.map( ( cell, i ) => {
        let after = i === count - 1 ? lastAfter : ( i < original.length - 1 ? original[ i ].after : gap );
        return cell.open + cell.content + cell.close + after;
      } ).join( "" ) + model.tail;
    },

    /**
     * Opening tag of a column with explicit widths, keeping other attributes and classes
     * @returns {String}
     */
    buildOpeningTag( attrs, extra, sizes ) {
      let classes = [ MDRowLayout.classesFromSizes( sizes ) ].concat( extra ).join( " " );
      let params = [ "class=\"" + classes + "\"", "defaultclasses=0" ];
      Object.keys( attrs ).forEach( key => {
        if ( key !== "class" && key !== "defaultclasses" ) {
          params.push( key + "=\"" + String( attrs[ key ] ).replace( /"/g, "\\\"" ) + "\"" );
        }
      } );
      return "[col " + params.join( " " ) + "]";
    },
  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDRowLayout;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDRowLayout = MDRowLayout;
  }

} )( typeof window !== "undefined" ? window : this );

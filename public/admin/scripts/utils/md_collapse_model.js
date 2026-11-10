/**
 * Model of a DrInk Markdown [collapse] shortcode for editing its settings without touching the content:
 * the title (its text and attributes) and attributes of [collapse] and [collapse_content].
 * Only the changed tags are rebuilt, everything else is kept exactly (content, whitespace, order of the parts).
 * Used by the collapse designer (collapse_designer.js); the content is edited as blocks in the block view.
 *
 *   [collapse class="someclass"]
 *     [collapse_title class="btn btn-primary"]Read more[/collapse_title]
 *     [collapse_content]Hidden content[/collapse_content]
 *   [/collapse]
 *
 *   let model = UTILS.MDCollapseModel.parse( raw );
 *   model.title.text; // "Read more"
 *   UTILS.MDCollapseModel.update( model, { title: "More", titleAttrs: { class: "btn btn-link" } } ); // the new source
 *   UTILS.MDCollapseModel.create( { title: "More" } );
 *
 * Dependencies: md_shortcodes.js
 * Works in the browser (window.UTILS.MDCollapseModel) and in Node.js (module.exports).
 */
( function( root ) {

  let MDShortcodes = ( typeof module !== "undefined" && module.exports ) ? require( "./md_shortcodes.js" ) : root.UTILS.MDShortcodes;

  let NAMES = [ "collapse", "collapse_title", "collapse_content" ];

  // DrInk Markdown doesn't allow "]" in shortcode params
  let cleanParam = value => String( value || "" ).replace( /\]/g, "" ).trim();

  // the title is on one line and can't contain its closing tag
  let cleanTitle = text => String( text || "" ).replace( /\s*\r?\n\s*/g, " " ).replace( /\[\/collapse_title\]/gi, "" ).trim();

  /**
   * Attributes with the given ones set (a key with an empty value is removed); null when nothing changes
   */
  let mergeAttrs = ( attrs, changes ) => {
    let out = Object.assign( {}, attrs );
    let changed = false;
    Object.keys( changes || {} ).forEach( key => {
      let value = cleanParam( changes[ key ] );
      if ( cleanParam( out[ key ] ) === value ) {
        return;
      }
      changed = true;
      if ( value ) {
        out[ key ] = value;
      } else {
        delete out[ key ];
      }
    } );
    return changed ? out : null;
  };

  let part = ( raw, node ) => ( {
    attrs: node.attrs,
    openStart: node.start,
    openEnd: node.innerStart,
    innerStart: node.innerStart,
    innerEnd: node.innerEnd,
  } );

  let MDCollapseModel = {

    /**
     * Parses a [collapse] with one [collapse_title] and one [collapse_content] (in any order, only whitespace around them)
     * @param {String} raw
     * @returns {Object|null} { raw, attrs, collapse, title: { attrs, text, ... }, content: { attrs, ... } }
     */
    parse( raw ) {
      let tree = MDShortcodes.parseTree( raw, NAMES );
      if ( tree.errors.length || tree.children.length !== 1 ) {
        return null;
      }
      let node = tree.children[ 0 ];
      if ( node.name !== "collapse" || raw.slice( 0, node.start ).trim() || raw.slice( node.end ).trim() ) {
        return null;
      }
      let titles = node.children.filter( child => child.name === "collapse_title" );
      let contents = node.children.filter( child => child.name === "collapse_content" );
      if ( titles.length !== 1 || contents.length !== 1 || node.children.length !== 2 ) {
        return null;
      }
      let pos = node.innerStart;
      for ( let child of node.children ) {
        if ( raw.slice( pos, child.start ).trim() ) {
          return null;
        }
        pos = child.end;
      }
      if ( raw.slice( pos, node.innerEnd ).trim() ) {
        return null;
      }
      let title = part( raw, titles[ 0 ] );
      title.text = raw.slice( title.innerStart, title.innerEnd );
      return {
        raw: raw,
        attrs: node.attrs,
        collapse: part( raw, node ),
        title: title,
        content: part( raw, contents[ 0 ] ),
      };
    },

    /**
     * The source with the given changes; an unchanged one is returned exactly as it was
     * @param {Object} model - from parse()
     * @param {Object} changes - { attrs, title, titleAttrs, contentAttrs }, all optional
     * @returns {String}
     */
    update( model, changes ) {
      let replacements = [];
      let tag = ( name, p, attrs ) => {
        let merged = mergeAttrs( p.attrs, attrs );
        if ( merged ) {
          replacements.push( { start: p.openStart, end: p.openEnd, text: MDShortcodes.buildOpeningTag( name, merged ) } );
        }
      };
      tag( "collapse", model.collapse, changes.attrs );
      tag( "collapse_title", model.title, changes.titleAttrs );
      tag( "collapse_content", model.content, changes.contentAttrs );
      if ( changes.title !== undefined ) {
        let title = cleanTitle( changes.title );
        if ( title !== model.title.text.trim() ) {
          replacements.push( { start: model.title.innerStart, end: model.title.innerEnd, text: title } );
        }
      }
      let out = model.raw;
      replacements.sort( ( a, b ) => b.start - a.start ).forEach( r => {
        out = out.slice( 0, r.start ) + r.text + out.slice( r.end );
      } );
      return out;
    },

    /**
     * A new [collapse] with empty content
     * @param {Object} options - { title, attrs, titleAttrs, contentAttrs }
     * @returns {String}
     */
    create( options ) {
      let tag = ( name, attrs ) => MDShortcodes.buildOpeningTag( name, mergeAttrs( {}, attrs ) || {} );
      return [
        tag( "collapse", options.attrs ),
        tag( "collapse_title", options.titleAttrs ) + cleanTitle( options.title ) + "[/collapse_title]",
        tag( "collapse_content", options.contentAttrs ),
        "[/collapse_content]",
        "[/collapse]",
      ].join( "\n" );
    },
  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDCollapseModel;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDCollapseModel = MDCollapseModel;
  }

} )( typeof window !== "undefined" ? window : this );

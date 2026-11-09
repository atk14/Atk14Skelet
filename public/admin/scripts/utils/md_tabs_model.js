/**
 * Model of a DrInk Markdown [tabs] shortcode for editing its structure without touching the content:
 * names and other attributes of the tabs, their order and number, attributes of [tabs].
 * Everything else is kept exactly: content of the tabs, whitespace between the tags.
 * Used by the inspector of the tab designer (tab_designer.js); the content of tabs is edited as blocks in the block view.
 *
 *   let model = UTILS.MDTabsModel.parse( "[tabs]\n[tab name=\"A\"]\nText\n[/tab]\n[/tabs]" );
 *   model.tabs[ 0 ].attrs.name; // "A"
 *   model = UTILS.MDTabsModel.rename( model, 0, "B" );
 *   model = UTILS.MDTabsModel.add( model, "C" );
 *   UTILS.MDTabsModel.serialize( model );
 *   // "[tabs]\n[tab name=\"B\"]\nText\n[/tab]\n[tab name=\"C\"]\n[/tab]\n[/tabs]"
 *
 * Operations return a new model, the given one is not changed.
 *
 * Dependencies: md_shortcodes.js
 * Works in the browser (window.UTILS.MDTabsModel) and in Node.js (module.exports).
 */
( function( root ) {

  let MDShortcodes = ( typeof module !== "undefined" && module.exports ) ? require( "./md_shortcodes.js" ) : root.UTILS.MDShortcodes;

  let clone = model => Object.assign( {}, model, {
    attrs: Object.assign( {}, model.attrs ),
    tabs: model.tabs.map( tab => Object.assign( {}, tab, { attrs: Object.assign( {}, tab.attrs ) } ) ),
    separators: model.separators.slice(),
  } );

  // DrInk Markdown doesn't allow "]" in shortcode params
  let cleanParam = value => String( value ).replace( /\]/g, ")" );

  let MDTabsModel = {

    /**
     * Parses a [tabs] shortcode; returns null when there's anything but [tab]s and whitespace in it
     * @param {String} raw
     * @returns {Object|null} { before, open, attrs, separators, tabs: [ { attrs, open, inner, close } ], close, after }
     *   separators[ 0 ] is between [tabs] and the first [tab], separators[ i + 1 ] after the i-th [tab]
     */
    parse( raw ) {
      let tree = MDShortcodes.parseTree( raw, [ "tabs", "tab" ] );
      if ( tree.errors.length || tree.children.length !== 1 ) {
        return null;
      }
      let node = tree.children[ 0 ];
      if ( node.name !== "tabs" || raw.slice( 0, node.start ).trim() || raw.slice( node.end ).trim() || !node.children.length ) {
        return null;
      }
      let tabs = [];
      let separators = [];
      let pos = node.innerStart;
      for ( let child of node.children ) {
        let separator = raw.slice( pos, child.start );
        if ( child.name !== "tab" || separator.trim() ) {
          return null;
        }
        separators.push( separator );
        tabs.push( {
          attrs: child.attrs,
          open: raw.slice( child.start, child.innerStart ),
          inner: raw.slice( child.innerStart, child.innerEnd ),
          close: raw.slice( child.innerEnd, child.end ),
        } );
        pos = child.end;
      }
      let last = raw.slice( pos, node.innerEnd );
      if ( last.trim() ) {
        return null;
      }
      separators.push( last );
      return {
        before: raw.slice( 0, node.start ),
        open: raw.slice( node.start, node.innerStart ),
        attrs: node.attrs,
        separators: separators,
        tabs: tabs,
        close: raw.slice( node.innerEnd, node.end ),
        after: raw.slice( node.end ),
      };
    },

    serialize( model ) {
      let out = model.before + model.open + model.separators[ 0 ];
      model.tabs.forEach( ( tab, i ) => {
        out += tab.open + tab.inner + tab.close + model.separators[ i + 1 ];
      } );
      return out + model.close + model.after;
    },

    /**
     * Renames the i-th tab; other attributes of the tab are kept
     */
    rename( model, i, name ) {
      model = clone( model );
      let tab = model.tabs[ i ];
      name = cleanParam( name );
      if ( tab.attrs.name !== name ) {
        tab.attrs.name = name;
        tab.open = MDShortcodes.buildOpeningTag( "tab", tab.attrs );
      }
      return model;
    },

    /**
     * Sets attributes of [tabs] (a key with an empty value is removed), the others are kept
     */
    setAttrs( model, attrs ) {
      model = clone( model );
      let changed = false;
      Object.keys( attrs ).forEach( key => {
        let value = attrs[ key ] ? cleanParam( attrs[ key ] ) : "";
        if ( ( model.attrs[ key ] || "" ) === value ) {
          return;
        }
        changed = true;
        if ( value ) {
          model.attrs[ key ] = value;
        } else {
          delete model.attrs[ key ];
        }
      } );
      if ( changed ) {
        model.open = MDShortcodes.buildOpeningTag( "tabs", model.attrs );
      }
      return model;
    },

    /**
     * Appends an empty tab, separated from the others as they are
     */
    add( model, name ) {
      model = clone( model );
      let attrs = { name: cleanParam( name ) };
      model.tabs.push( { attrs: attrs, open: MDShortcodes.buildOpeningTag( "tab", attrs ), inner: "\n", close: "[/tab]" } );
      let between = model.separators.length > 2 ? model.separators[ 1 ] : "\n";
      model.separators.splice( model.separators.length - 1, 0, between );
      return model;
    },

    /**
     * Removes the i-th tab with its content; the last tab is never removed
     */
    remove( model, i ) {
      if ( model.tabs.length <= 1 ) {
        return model;
      }
      model = clone( model );
      model.tabs.splice( i, 1 );
      // the separator after the tab goes with it; for the last tab the one before it, the closing [/tabs] keeps its own
      model.separators.splice( i === model.tabs.length ? i : i + 1, 1 );
      return model;
    },

    /**
     * Moves the tab from the position to another one; separators stay in their places
     */
    move( model, from, to ) {
      model = clone( model );
      let tab = model.tabs.splice( from, 1 )[ 0 ];
      model.tabs.splice( to, 0, tab );
      return model;
    },
  };

  if ( typeof module !== "undefined" && module.exports ) {
    module.exports = MDTabsModel;
  } else {
    root.UTILS = root.UTILS || { };
    root.UTILS.MDTabsModel = MDTabsModel;
  }

} )( typeof window !== "undefined" ? window : this );

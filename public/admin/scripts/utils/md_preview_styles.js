/**
 * Custom CSS (<style> elements) in previews of Markdown content (the block view, the "Preview" of the Code view).
 * On the web such CSS applies to the whole page; in the administration it would restyle the administration
 * itself, and its media queries would ignore the preview's width (phone, tablet, desktop: the viewport
 * is narrowed, not the window). So the CSS of every <style> in a preview is rewritten:
 *
 * - its rules are scoped to the preview's viewport (@scope), not reaching the editor's own controls in it,
 * - a width @media becomes a container query of the viewport, as the preview's grid does (see _md_preview_grid.scss;
 *   its px widths are lowered by the viewport's padding, so a tablet preview 768px wide matches (min-width: 768px)),
 * - other at-rules (@keyframes, @font-face...) are kept outside of the scope.
 *
 *   UTILS.MDPreviewStyles.apply( previewElement );
 *
 * Uses the browser's CSS parser (a constructed CSSStyleSheet).
 */
window.UTILS = window.UTILS || { };

window.UTILS.MDPreviewStyles = class {

  static scope = ".md-preview__viewport";

  static container = "md-preview__viewport";

  // horizontal padding of the viewport in px (2 × 0.75rem, see _md_preview.scss): a container query measures
  // the viewport's content, a media query the whole width, so the widths of the conditions are lowered by it
  static viewportPadding = 24;

  // controls of the block view inside the viewport (see md_block_editor.js)
  static limits = ".md-block__toolbar, .md-block__placeholder, .md-block__add, .md-block__bar, .md-block__editor, .md-block__source, .md-blocks__append, .md-blocks__menu, .md-blocks__message";

  /**
   * Rewrites the <style> elements in the element (the ones not rewritten yet)
   * @param {Element} element
   */
  static apply( element ) {
    if ( !element ) {
      return;
    }
    element.querySelectorAll( "style:not([data-md-preview])" ).forEach( style => {
      style.textContent = window.UTILS.MDPreviewStyles.transform( style.textContent );
      style.dataset.mdPreview = "1";
    } );
  }

  /**
   * @param {String} css
   * @returns {String}
   */
  static transform( css ) {
    let sheet;
    try {
      sheet = new CSSStyleSheet();
      sheet.replaceSync( css );
    } catch ( e ) {
      return css;
    }
    let self = window.UTILS.MDPreviewStyles;
    let scoped = [];
    let global = [];
    [ ...sheet.cssRules ].forEach( rule => {
      if ( self.isScopable( rule ) ) {
        scoped.push( self.ruleText( rule ) );
      } else {
        global.push( rule.cssText );
      }
    } );
    if ( scoped.length ) {
      global.push( "@scope (" + self.scope + ") to (" + self.limits + ") {\n" + scoped.join( "\n" ) + "\n}" );
    }
    return global.join( "\n" );
  }

  // style rules and conditional rules, the at-rules allowed in @scope
  static isScopable( rule ) {
    return rule instanceof CSSStyleRule || rule instanceof CSSMediaRule || rule instanceof CSSSupportsRule ||
      ( window.CSSContainerRule && rule instanceof window.CSSContainerRule );
  }

  static ruleText( rule ) {
    let self = window.UTILS.MDPreviewStyles;
    let inner = () => [ ...rule.cssRules ].map( r => self.ruleText( r ) ).join( "\n" );
    if ( rule instanceof CSSMediaRule ) {
      let condition = self.widthCondition( rule.media.mediaText );
      return condition ? "@container " + self.container + " " + condition + " {\n" + inner() + "\n}" : "@media " + rule.media.mediaText + " {\n" + inner() + "\n}";
    }
    if ( rule instanceof CSSSupportsRule ) {
      return "@supports " + rule.conditionText + " {\n" + inner() + "\n}";
    }
    return rule.cssText;
  }

  /**
   * The condition of a media query depending on the width only, usable in a container query;
   * "screen and (min-width: 768px)" -> "(min-width: 768px)"; null for other media queries
   * @param {String} mediaText
   * @returns {String|null}
   */
  static widthCondition( mediaText ) {
    let condition = mediaText.trim().replace( /^(?:only\s+)?(?:screen|all)\s+and\s+/i, "" );
    let feature = "\\([^()]*\\bwidth\\b[^()]*\\)";
    if ( /device-/i.test( condition ) || !new RegExp( "^" + feature + "(?:\\s+and\\s+" + feature + ")*$", "i" ).test( condition ) ) {
      return null;
    }
    let padding = window.UTILS.MDPreviewStyles.viewportPadding;
    return condition.replace( /(\d*\.?\d+)px\b/g, ( m, width ) => Math.max( 0, parseFloat( width ) - padding ) + "px" );
  }
};

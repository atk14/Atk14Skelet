/**
 * Checks the Markdown block tokenizer on real content exported from the database by test/js/export_markdown_sources
 *
 *   ./test/js/export_markdown_sources | node test/js/check_tokenizer_roundtrip.js
 *   node test/js/check_tokenizer_roundtrip.js sources.json [--verbose]
 *
 * Reports sources which don't survive tokenize() + join() unchanged (must never happen)
 * and sources with suspicious blocks (unclosed shortcodes) worth a look.
 * With --verbose, prints the blocks of every source.
 */
var fs = require( "fs" );
var MDBlockTokenizer = require( "../../public/admin/scripts/utils/md_block_tokenizer.js" );

var args = process.argv.slice( 2 );
var verbose = args.indexOf( "--verbose" ) >= 0;
var file = args.filter( function( a ) { return a !== "--verbose"; } )[ 0 ];
var data = JSON.parse( fs.readFileSync( file || "/dev/stdin", "utf8" ) );

var failures = 0;
var warnings = 0;
var blockCount = 0;
var containerCount = 0;
var typeCounts = {};

data.sources.forEach( function( item ) {
  var label = item.class_name + "#" + item.id + " " + item.field;
  var doc = MDBlockTokenizer.tokenize( item.source, { blockShortcodes: data.block_shortcodes } );

  if ( MDBlockTokenizer.join( doc ) !== item.source ) {
    failures++;
    console.log( "FAIL " + label + ": join() doesn't give back the original source" );
  }

  var walk = function( blocks, depth ) {
    blocks.forEach( function( block ) {
      blockCount++;
      var type = block.type + ( depth ? " (nested)" : "" );
      typeCounts[ type ] = ( typeCounts[ type ] || 0 ) + 1;
      if ( block.unclosed ) {
        warnings++;
        console.log( "WARN " + label + ": unclosed [" + block.name + "] at line " + ( block.line + 1 ) );
      }
      if ( block.container ) {
        containerCount++;
        block.container.children.forEach( function( child ) {
          walk( child.doc.blocks, depth + 1 );
        } );
      } else if ( block.type === "shortcode" && MDBlockTokenizer.defaultContainers[ block.name ] && !block.unclosed ) {
        // a container shortcode which couldn't be split into its children, it's edited as a whole
        console.log( "INFO " + label + ": [" + block.name + "] at line " + ( block.line + 1 ) + " is not split into children" );
      }
    } );
  };
  walk( doc.blocks, 0 );

  if ( verbose ) {
    console.log( "--- " + label );
    doc.blocks.forEach( function( block ) {
      var preview = block.raw.replace( /\s+/g, " " ).slice( 0, 70 );
      console.log( "  " + ( block.line + 1 ) + "\t" + block.type + ( block.name ? "[" + block.name + "]" : "" ) + "\t" + preview );
    } );
  }
} );

console.log( data.sources.length + " sources, " + blockCount + " blocks (" + containerCount + " containers) " + JSON.stringify( typeCounts ) );
console.log( failures + " failures, " + warnings + " warnings" );
process.exit( failures ? 1 : 0 );

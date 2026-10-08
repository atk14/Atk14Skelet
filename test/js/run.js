/**
 * Minimal test runner for admin JavaScript utilities (no dependencies, Node.js 9+)
 *
 *   node test/js/run.js                 # runs all test/js/tc_*.js
 *   node test/js/run.js tc_md_shortcodes.js
 *
 * A test case file exports an object of test functions:
 *
 *   module.exports = { test_something() { assert.equal( 1, 1 ); } };
 */
var fs = require( "fs" );
var path = require( "path" );

var files = process.argv.slice( 2 );
if ( !files.length ) {
  files = fs.readdirSync( __dirname ).filter( function( f ) { return /^tc_.*\.js$/.test( f ); } ).sort();
}

var passed = 0;
var failed = 0;
files.forEach( function( file ) {
  var testCase = require( path.resolve( __dirname, path.basename( file ) ) );
  Object.keys( testCase ).forEach( function( name ) {
    try {
      testCase[ name ]();
      passed++;
    } catch ( e ) {
      failed++;
      console.log( "FAIL " + file + " " + name );
      console.log( e.stack );
    }
  } );
} );

console.log( passed + " passed, " + failed + " failed" );
process.exit( failed ? 1 : 0 );

var assert = require( "assert" );
var MDRowLayout = require( "../../public/admin/scripts/utils/md_row_layout.js" );

var S = function( xs, sm, md, lg, xl ) {
  return { xs: xs, sm: sm, md: md, lg: lg, xl: xl };
};

module.exports = {

  test_sizes_from_classes() {
    assert.deepEqual( MDRowLayout.sizesFromClasses( [ "col-12", "col-xs-12", "col-sm-6", "col-md-4", "col-lg-2", "col-xl-2" ] ), { sizes: S( 12, 6, 4, 2, 2 ), extra: [] } );
    // inherited from smaller breakpoints, xs is full width when not given
    assert.deepEqual( MDRowLayout.sizesFromClasses( [ "col-md-6", "text-center" ] ), { sizes: S( 12, 12, 6, 6, 6 ), extra: [ "text-center" ] } );
    // hidden on small devices
    assert.deepEqual( MDRowLayout.sizesFromClasses( "d-none d-xs-none d-sm-none col-md-4 d-md-block col-lg-3 col-xl-2".split( " " ) ).sizes, S( 0, 0, 4, 3, 2 ) );
    // hidden on large devices
    assert.deepEqual( MDRowLayout.sizesFromClasses( "col-12 col-md-6 d-lg-none".split( " " ) ).sizes, S( 12, 12, 6, 0, 0 ) );
    // other display classes are not about visibility
    assert.deepEqual( MDRowLayout.sizesFromClasses( [ "col-6", "d-flex" ] ), { sizes: S( 6, 6, 6, 6, 6 ), extra: [ "d-flex" ] } );
    // not representable
    assert.equal( MDRowLayout.sizesFromClasses( [ "col" ] ), null );
    assert.equal( MDRowLayout.sizesFromClasses( [ "col-md-auto" ] ), null );
    assert.equal( MDRowLayout.sizesFromClasses( [ "col-md-6", "col-md-4" ] ), null );
    assert.equal( MDRowLayout.sizesFromClasses( [ "col-13" ] ), null );
  },

  test_classes_from_sizes() {
    assert.equal( MDRowLayout.classesFromSizes( S( 12, 6, 4, 2, 2 ) ), "col-12 col-xs-12 col-sm-6 col-md-4 col-lg-2 col-xl-2" );
    assert.equal( MDRowLayout.classesFromSizes( S( 0, 0, 4, 3, 2 ) ), "d-none d-xs-none d-sm-none col-md-4 d-md-block col-lg-3 col-xl-2" );
    [ S( 12, 6, 4, 2, 2 ), S( 0, 0, 4, 3, 2 ), S( 12, 12, 6, 0, 0 ), S( 6, 0, 6, 0, 3 ) ].forEach( sizes => {
      assert.deepEqual( MDRowLayout.sizesFromClasses( MDRowLayout.classesFromSizes( sizes ).split( " " ) ).sizes, sizes );
    } );
  },

  test_default_classes() {
    var model = MDRowLayout.parse( "[row]\n[col]A[/col]\n[col]B[/col]\n[col]C[/col]\n[/row]" );
    assert.deepEqual( model.cells.map( c => c.sizes ), [ S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ) ] );
    model = MDRowLayout.parse( "[row][col]1[/col][col]2[/col][col]3[/col][col]4[/col][/row]" );
    assert.deepEqual( model.cells[ 0 ].sizes, S( 12, 12, 6, 3, 3 ) );
    // own classes are added to the default ones
    model = MDRowLayout.parse( "[row][col class=\"col-lg-8 highlight\"]1[/col][col]2[/col][/row]" );
    assert.deepEqual( model.cells[ 0 ].sizes, S( 12, 12, 6, 8, 8 ) );
    assert.deepEqual( model.cells[ 0 ].extra, [ "highlight" ] );
  },

  test_parse() {
    var source = [
      "[row class=\"row--borders\"]",
      "  [col class=\"col-12 col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 \" defaultclasses=0]",
      "### Column 1",
      "",
      "[row][col]nested[/col][/row]",
      "[/col]",
      "  [col class=\"d-none d-xs-none d-sm-none col-md-6 d-md-block col-lg-6 col-xl-6 text-right\" defaultclasses=0 order=2]B[/col]",
      "[/row]",
    ].join( "\n" );
    var model = MDRowLayout.parse( source );
    assert.equal( model.head, "[row class=\"row--borders\"]\n  " );
    assert.equal( model.tail, "[/row]" );
    assert.equal( model.cells.length, 2 );
    assert.equal( model.cells[ 0 ].content, "\n### Column 1\n\n[row][col]nested[/col][/row]\n" );
    assert.deepEqual( model.cells[ 0 ].sizes, S( 12, 6, 6, 6, 6 ) );
    assert.deepEqual( model.cells[ 1 ].sizes, S( 0, 0, 6, 6, 6 ) );
    assert.deepEqual( model.cells[ 1 ].extra, [ "text-right" ] );
    assert.deepEqual( model.cells.map( c => c.after ), [ "\n  ", "\n" ] );

    // unchanged widths give back the source
    assert.equal( MDRowLayout.serialize( model, model.cells.map( c => c.sizes ) ), source );
  },

  test_parse_invalid() {
    [
      "[row]\ntext\n[col]A[/col]\n[/row]",
      "[row]\n[col]A[/col]\n[div]B[/div]\n[/row]",
      "[row]\n[col]A\n[/row]",
      "[row]\n[/row]",
      "[row][col class=\"col\"]A[/col][/row]",
      "[row][col]A[/col][/row]\ntext after",
      "[div][col]A[/col][/div]",
    ].forEach( source => {
      assert.equal( MDRowLayout.parse( source ), null, source );
    } );
  },

  test_serialize_changed_widths() {
    var model = MDRowLayout.parse( "[row]\n  [col]A[/col]\n  [col class=\"x\" order=2]B[/col]\n[/row]" );
    var code = MDRowLayout.serialize( model, [ model.cells[ 0 ].sizes, S( 12, 12, 4, 4, 4 ) ] );
    assert.equal( code, "[row]\n  [col]A[/col]\n  [col class=\"col-12 col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 x\" defaultclasses=0 order=\"2\"]B[/col]\n[/row]" );
    // the changed column still has its widths, the unchanged one too
    var again = MDRowLayout.parse( code );
    assert.deepEqual( again.cells.map( c => c.sizes ), [ S( 12, 12, 6, 6, 6 ), S( 12, 12, 4, 4, 4 ) ] );
  },

  test_serialize_added_and_removed_columns() {
    var model = MDRowLayout.parse( "[row]\n  [col]A[/col]\n  [col]B[/col]\n[/row]" );
    // a column added: empty, separated like the others; [col] in a 3-column row is md-4 by default, so A and B are kept
    var code = MDRowLayout.serialize( model, [ S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ) ] );
    assert.equal( code, [
      "[row]",
      "  [col]A[/col]",
      "  [col]B[/col]",
      "  [col class=\"col-12 col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4\" defaultclasses=0][/col]",
      "[/row]",
    ].join( "\n" ) );

    // a column relying on default classes keeps its tag only when the default classes give the same widths
    // with the new number of columns: [col] in a 2-column row is md-6, in a 3-column row md-4
    code = MDRowLayout.serialize( model, [ S( 12, 12, 6, 6, 6 ), S( 12, 12, 6, 6, 6 ), S( 12, 12, 12, 12, 12 ) ] );
    assert.ok( code.indexOf( "[col]A[/col]" ) < 0 );
    assert.deepEqual( MDRowLayout.parse( code ).cells.map( c => c.sizes ), [ S( 12, 12, 6, 6, 6 ), S( 12, 12, 6, 6, 6 ), S( 12, 12, 12, 12, 12 ) ] );
    code = MDRowLayout.serialize( model, [ S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ) ].slice( 0, 2 ).concat( [ S( 12, 12, 4, 4, 4 ) ] ) );
    assert.deepEqual( MDRowLayout.parse( code ).cells.map( c => c.sizes ), [ S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ), S( 12, 12, 4, 4, 4 ) ] );

    // columns removed: their content goes to the last column
    model = MDRowLayout.parse( "[row]\n[col]A[/col]\n[col]\nB\n\nB2\n[/col]\n[col][/col]\n[col]D[/col]\n[/row]" );
    code = MDRowLayout.serialize( model, [ S( 12, 12, 12, 12, 12 ), S( 12, 12, 12, 12, 12 ) ] );
    assert.equal( code, "[row]\n[col class=\"col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12\" defaultclasses=0]A[/col]\n[col class=\"col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12\" defaultclasses=0]\nB\n\nB2\n\nD\n[/col]\n[/row]" );
  },
};

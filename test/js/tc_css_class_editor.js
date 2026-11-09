var assert = require( "assert" );
var CssClassEditor = require( "../../public/admin/scripts/utils/css_class_editor.js" );

module.exports = {

  test_parse() {
    assert.deepEqual( CssClassEditor.parse( "" ), [] );
    assert.deepEqual( CssClassEditor.parse( null ), [] );
    // the order is kept, a class is there once (the first one wins)
    assert.deepEqual( CssClassEditor.parse( "  card p-3\tmx-2\n p-3 card  " ), [ "card", "p-3", "mx-2" ] );
    assert.deepEqual( CssClassEditor.parse( "mx-2 card" ), [ "mx-2", "card" ] );
  },

  test_serialize() {
    assert.equal( CssClassEditor.serialize( [] ), "" );
    assert.equal( CssClassEditor.serialize( [ "card", "p-3", "card", "mx-2" ] ), "card p-3 mx-2" );
    assert.equal( CssClassEditor.serialize( [ "a b", " c " ] ), "a b c" );
  },

  test_same() {
    assert.ok( CssClassEditor.same( "card  p-3 ", "card p-3" ) );
    assert.ok( CssClassEditor.same( "card p-3 card", "card p-3" ) );
    assert.ok( CssClassEditor.same( "", "  " ) );
    // the order matters
    assert.ok( !CssClassEditor.same( "card mx-2", "mx-2 card" ) );
    assert.ok( !CssClassEditor.same( "card", "card p-3" ) );
  },
};

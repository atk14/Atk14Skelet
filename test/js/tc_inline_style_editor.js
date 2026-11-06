var assert = require( "assert" );
var InlineStyleEditor = require( "../../public/admin/scripts/utils/inline_style_editor.js" );

module.exports = {

  test_parse() {
    assert.deepEqual( InlineStyleEditor.parse( "" ), [] );
    assert.deepEqual( InlineStyleEditor.parse( null ), [] );
    assert.deepEqual( InlineStyleEditor.parse( "color: red; margin:0 auto;" ), [
      { property: "color", value: "red" },
      { property: "margin", value: "0 auto" },
    ] );
    // no trailing ";", extra whitespace and empty declarations
    assert.deepEqual( InlineStyleEditor.parse( "  color :  red ;; ;max-width: 30%  " ), [
      { property: "color", value: "red" },
      { property: "max-width", value: "30%" },
    ] );
    // !important and custom properties (case kept) are values/properties as they are
    assert.deepEqual( InlineStyleEditor.parse( "--Accent: #f00; color: var(--Accent) !important" ), [
      { property: "--Accent", value: "#f00" },
      { property: "color", value: "var(--Accent) !important" },
    ] );
    // a declaration without ":" is kept as a property without a value
    assert.deepEqual( InlineStyleEditor.parse( "color red; margin: 0" ), [
      { property: "color red", value: "" },
      { property: "margin", value: "0" },
    ] );
  },

  test_parse_keeps_parentheses_and_quotes() {
    assert.deepEqual( InlineStyleEditor.parse( "background: url(data:image/png;base64,AAA=) no-repeat; color: red" ), [
      { property: "background", value: "url(data:image/png;base64,AAA=) no-repeat" },
      { property: "color", value: "red" },
    ] );
    assert.deepEqual( InlineStyleEditor.parse( "font-family: \"A;B\", 'C\\';D'; content: \"x:y\"" ), [
      { property: "font-family", value: "\"A;B\", 'C\\';D'" },
      { property: "content", value: "\"x:y\"" },
    ] );
    assert.deepEqual( InlineStyleEditor.parse( "width: calc(100% - (2 * 1rem)); height: 1px" ).map( d => d.property ), [ "width", "height" ] );
  },

  test_serialize() {
    assert.equal( InlineStyleEditor.serialize( [] ), "" );
    assert.equal( InlineStyleEditor.serialize( [
      { property: " color ", value: " red " },
      { property: "margin", value: "" },
      { property: "", value: "1px" },
      { property: "padding", value: "1rem" },
    ] ), "color: red; padding: 1rem;" );
    // round trip
    var style = "background: url(data:image/png;base64,AAA=) no-repeat; font-family: \"A;B\"; color: red;";
    assert.equal( InlineStyleEditor.serialize( InlineStyleEditor.parse( style ) ), style );
  },

  test_same() {
    assert.ok( InlineStyleEditor.same( "color:red", "color: red;" ) );
    assert.ok( InlineStyleEditor.same( "", " ; " ) );
    assert.ok( !InlineStyleEditor.same( "color: red; margin: 0", "margin: 0; color: red" ) ); // the order matters
    assert.ok( !InlineStyleEditor.same( "color: red", "color: blue" ) );
  },
};

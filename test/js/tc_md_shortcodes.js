var assert = require( "assert" );
var MDShortcodes = require( "../../public/admin/scripts/utils/md_shortcodes.js" );

module.exports = {

  test_scan_tags() {
    var source = "[tabs]\n[tab name=\"One\"]\nText [collapse]\n[/tab]\n[/tabs]";
    var tags = MDShortcodes.scanTags( source, [ "tabs", "tab" ] );
    assert.deepEqual( tags.map( t => ( t.closing ? "/" : "" ) + t.name ), [ "tabs", "tab", "/tab", "/tabs" ] );
    assert.equal( tags[ 1 ].raw, "[tab name=\"One\"]" );
    assert.equal( source.slice( tags[ 1 ].start, tags[ 1 ].end ), "[tab name=\"One\"]" );
    assert.deepEqual( tags[ 1 ].attrs, { name: "One" } );

    // a name must be followed by a space or "]": [collapse] is not [col]
    assert.deepEqual( MDShortcodes.scanTags( "[collapse][col][/col]", [ "col" ] ).map( t => t.raw ), [ "[col]", "[/col]" ] );
  },

  test_scan_tags_ignores_code() {
    var source = "`[row]` text\n```\n[row]\n```\n[row]";
    var tags = MDShortcodes.scanTags( source, [ "row" ] );
    assert.equal( tags.length, 1 );
    assert.equal( tags[ 0 ].start, source.lastIndexOf( "[row]" ) );

    assert.equal( MDShortcodes.scanTags( source, [ "row" ], { maskCode: false } ).length, 3 );
  },

  test_mask_code_keeps_length() {
    var source = "a `b` c\r\n```js\r\nx\r\n```\r\nd";
    var masked = MDShortcodes.maskCode( source );
    assert.equal( masked.length, source.length );
    assert.equal( masked.split( "\n" ).length, source.split( "\n" ).length );
    assert.equal( masked.indexOf( "x" ), -1 );
    assert.ok( masked.indexOf( "d" ) > 0 );
  },

  test_parse_tree() {
    var source = "[tabs]\n[tab name=\"A\"]\n[row][col]x[/col][/row]\n[/tab]\n[tab name=\"B\"]\ny\n[/tab]\n[/tabs]";
    var tree = MDShortcodes.parseTree( source, [ "tabs", "tab", "row", "col" ] );
    assert.equal( tree.errors.length, 0 );
    assert.equal( tree.children.length, 1 );
    var tabs = tree.children[ 0 ];
    assert.equal( tabs.name, "tabs" );
    assert.equal( tabs.start, 0 );
    assert.equal( tabs.end, source.length );
    assert.deepEqual( tabs.children.map( c => c.attrs.name ), [ "A", "B" ] );
    assert.equal( source.slice( tabs.children[ 1 ].innerStart, tabs.children[ 1 ].innerEnd ), "\ny\n" );
    assert.equal( tabs.children[ 0 ].children[ 0 ].name, "row" );
  },

  test_parse_tree_errors() {
    var tree = MDShortcodes.parseTree( "[/tab][tabs][tab]x[/tabs]", [ "tabs", "tab" ] );
    assert.deepEqual( tree.errors.map( e => e.message ), [ "unexpected closing tag [/tab]", "unclosed tag [tab]" ] );
    assert.ok( tree.children[ 0 ].closeTag );
    assert.equal( tree.children[ 0 ].children[ 0 ].closeTag, null );

    tree = MDShortcodes.parseTree( "[tabs]x", [ "tabs" ] );
    assert.deepEqual( tree.errors.map( e => e.message ), [ "unclosed tag [tabs]" ] );
    assert.equal( tree.children[ 0 ].end, 7 );
  },

  test_parse_attributes() {
    assert.deepEqual( MDShortcodes.parseAttributes( "" ), {} );
    assert.deepEqual(
      MDShortcodes.parseAttributes( " name=\"Tab \\\"1\\\"\" class=highlight id='x y' defaultclasses=0 active" ),
      { name: "Tab \"1\"", class: "highlight", id: "x y", defaultclasses: "0", active: "" }
    );
    assert.deepEqual( MDShortcodes.parseAttributes( "class=\"col-6 col-md-4\"" ), { class: "col-6 col-md-4" } );
  },

  test_build_opening_tag() {
    assert.equal( MDShortcodes.buildOpeningTag( "tabs" ), "[tabs]" );
    assert.equal( MDShortcodes.buildOpeningTag( "tab", { name: "Say \"hi\"", class: "x" } ), "[tab name=\"Say \\\"hi\\\"\" class=\"x\"]" );

    // round trip
    var attrs = { name: "Say \"hi\", 'you'" };
    var tag = MDShortcodes.scanTags( MDShortcodes.buildOpeningTag( "tab", attrs ), [ "tab" ] )[ 0 ];
    assert.deepEqual( tag.attrs, attrs );
  },
};

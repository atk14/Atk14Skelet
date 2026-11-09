var assert = require( "assert" );
var MDTabsModel = require( "../../public/admin/scripts/utils/md_tabs_model.js" );

var SOURCE = [
  "[tabs class=\"x\"]",
  "  [tab name=\"One\" id=\"first\"]",
  "",
  "Text **one**",
  "",
  "[/tab]",
  "  [tab name='Two']Two[/tab]",
  "[/tabs]",
].join( "\n" );

module.exports = {

  test_parse_and_serialize() {
    var model = MDTabsModel.parse( SOURCE );
    assert.deepEqual( model.attrs, { class: "x" } );
    assert.deepEqual( model.tabs.map( t => t.attrs.name ), [ "One", "Two" ] );
    assert.equal( model.tabs[ 0 ].inner, "\n\nText **one**\n\n" );
    assert.equal( model.tabs[ 1 ].open, "[tab name='Two']" );
    assert.deepEqual( model.separators, [ "\n  ", "\n  ", "\n" ] );
    assert.equal( MDTabsModel.serialize( model ), SOURCE );
  },

  test_parse_invalid() {
    [
      "[tabs]\ntext\n[tab name=\"A\"]A[/tab]\n[/tabs]",
      "[tabs]\n[tab name=\"A\"]A[/tab]\ntext\n[/tabs]",
      "[tabs]\n[/tabs]",
      "[tabs]\n[tab name=\"A\"]A\n[/tabs]",
      "[tabs][tab]A[/tab][/tabs]\nafter",
      "[row][col]A[/col][/row]",
    ].forEach( source => {
      assert.equal( MDTabsModel.parse( source ), null, source );
    } );
  },

  test_rename() {
    var model = MDTabsModel.parse( SOURCE );
    var renamed = MDTabsModel.rename( model, 0, "Say \"hi\" [1]" );
    assert.equal( MDTabsModel.serialize( renamed ), SOURCE.replace( "[tab name=\"One\" id=\"first\"]", "[tab name=\"Say \\\"hi\\\" [1)\" id=\"first\"]" ) );
    // the given model is not changed, the same name keeps the tag as it was
    assert.equal( MDTabsModel.serialize( model ), SOURCE );
    assert.equal( MDTabsModel.serialize( MDTabsModel.rename( model, 1, "Two" ) ), SOURCE );
  },

  test_set_attrs() {
    var model = MDTabsModel.parse( SOURCE );
    assert.equal( MDTabsModel.serialize( MDTabsModel.setAttrs( model, { class: "x" } ) ), SOURCE );
    assert.ok( MDTabsModel.serialize( MDTabsModel.setAttrs( model, { class: "x y" } ) ).indexOf( "[tabs class=\"x y\"]\n" ) === 0 );
    assert.ok( MDTabsModel.serialize( MDTabsModel.setAttrs( model, { class: "" } ) ).indexOf( "[tabs]\n" ) === 0 );
  },

  test_add() {
    var code = MDTabsModel.serialize( MDTabsModel.add( MDTabsModel.parse( SOURCE ), "Three" ) );
    assert.equal( code, SOURCE.replace( "[tab name='Two']Two[/tab]\n", "[tab name='Two']Two[/tab]\n  [tab name=\"Three\"]\n[/tab]\n" ) );
    // a single tab: the new one goes on its own line
    code = MDTabsModel.serialize( MDTabsModel.add( MDTabsModel.parse( "[tabs]\n[tab name=\"A\"]A[/tab]\n[/tabs]" ), "B" ) );
    assert.equal( code, "[tabs]\n[tab name=\"A\"]A[/tab]\n[tab name=\"B\"]\n[/tab]\n[/tabs]" );
    assert.deepEqual( MDTabsModel.parse( code ).tabs.map( t => t.attrs.name ), [ "A", "B" ] );
  },

  test_remove() {
    var model = MDTabsModel.parse( SOURCE );
    assert.equal( MDTabsModel.serialize( MDTabsModel.remove( model, 0 ) ), "[tabs class=\"x\"]\n  [tab name='Two']Two[/tab]\n[/tabs]" );
    assert.equal( MDTabsModel.serialize( MDTabsModel.remove( model, 1 ) ), "[tabs class=\"x\"]\n  [tab name=\"One\" id=\"first\"]\n\nText **one**\n\n[/tab]\n[/tabs]" );
    // the last tab stays
    var single = MDTabsModel.remove( model, 1 );
    assert.equal( MDTabsModel.serialize( MDTabsModel.remove( single, 0 ) ), MDTabsModel.serialize( single ) );
  },

  test_move() {
    var model = MDTabsModel.parse( SOURCE );
    var moved = MDTabsModel.move( model, 1, 0 );
    assert.equal( MDTabsModel.serialize( moved ), "[tabs class=\"x\"]\n  [tab name='Two']Two[/tab]\n  [tab name=\"One\" id=\"first\"]\n\nText **one**\n\n[/tab]\n[/tabs]" );
    assert.equal( MDTabsModel.serialize( MDTabsModel.move( moved, 0, 1 ) ), SOURCE );
  },
};

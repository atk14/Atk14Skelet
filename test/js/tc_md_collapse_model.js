var assert = require( "assert" );
var MDCollapseModel = require( "../../public/admin/scripts/utils/md_collapse_model.js" );

var SOURCE = [
  "[collapse class=\"x\"]",
  "  [collapse_title class='btn btn-primary']Read more[/collapse_title]",
  "  [collapse_content]",
  "Hidden **text**",
  "  [/collapse_content]",
  "[/collapse]",
].join( "\n" );

module.exports = {

  test_parse() {
    var model = MDCollapseModel.parse( SOURCE );
    assert.deepEqual( model.attrs, { class: "x" } );
    assert.equal( model.title.text, "Read more" );
    assert.deepEqual( model.title.attrs, { class: "btn btn-primary" } );
    assert.deepEqual( model.content.attrs, {} );
    // the title after the content
    model = MDCollapseModel.parse( "[collapse][collapse_content]A[/collapse_content][collapse_title]T[/collapse_title][/collapse]" );
    assert.equal( model.title.text, "T" );
  },

  test_parse_invalid() {
    [
      "[collapse]\n[collapse_content]A[/collapse_content]\n[/collapse]",
      "[collapse]\n[collapse_title]T[/collapse_title]\ntext\n[collapse_content]A[/collapse_content]\n[/collapse]",
      "[collapse]\n[collapse_title]T[/collapse_title]\n[collapse_title]U[/collapse_title]\n[collapse_content]A[/collapse_content]\n[/collapse]",
      "[collapse]\n[collapse_title]T[/collapse_title]\n[collapse_content]A\n[/collapse]",
      "[collapse][collapse_title]T[/collapse_title][collapse_content]A[/collapse_content][/collapse]\nafter",
      "[tabs][tab]A[/tab][/tabs]",
    ].forEach( source => {
      assert.equal( MDCollapseModel.parse( source ), null, source );
    } );
  },

  test_update() {
    var model = MDCollapseModel.parse( SOURCE );
    // nothing changed: the source is kept as it was
    assert.equal( MDCollapseModel.update( model, { title: "Read more", attrs: { class: "x", style: "" }, titleAttrs: { class: "btn btn-primary" } } ), SOURCE );
    assert.equal( MDCollapseModel.update( model, {} ), SOURCE );

    var code = MDCollapseModel.update( model, {
      title: "More\ninfo [/collapse_title]",
      attrs: { class: "" },
      titleAttrs: { class: "btn btn-link]" },
      contentAttrs: { class: "pt-2", style: "color: red" },
    } );
    assert.equal( code, [
      "[collapse]",
      "  [collapse_title class=\"btn btn-link\"]More info[/collapse_title]",
      "  [collapse_content class=\"pt-2\" style=\"color: red\"]",
      "Hidden **text**",
      "  [/collapse_content]",
      "[/collapse]",
    ].join( "\n" ) );
    assert.ok( MDCollapseModel.parse( code ) );
  },

  test_create() {
    var code = MDCollapseModel.create( { title: "Read more", titleAttrs: { class: "btn btn-primary" }, attrs: { class: "" } } );
    assert.equal( code, "[collapse]\n[collapse_title class=\"btn btn-primary\"]Read more[/collapse_title]\n[collapse_content]\n[/collapse_content]\n[/collapse]" );
    assert.equal( MDCollapseModel.parse( code ).title.text, "Read more" );
  },
};

var assert = require( "assert" );
var MDCssModel = require( "../../public/admin/scripts/utils/md_css_model.js" );

var BREAKPOINTS = [ { name: "sm", min: 576 }, { name: "md", min: 768 }, { name: "lg", min: 992 } ];

var SOURCE = [
  "<style>",
  "/* promo box */",
  ".promo { padding: 1rem; content: \"}\"; }",
  "@media (max-width: 575.98px) { .promo { margin: 0; } }",
  "",
  "@media (min-width: 768px) {",
  "    .promo { padding: 2rem; }",
  "",
  "    .promo h2 { font-size: 2rem; }",
  "}",
  "@media screen and (min-width: 992px) {",
  "  .promo { padding: 3rem; }",
  "}",
  "</style>",
].join( "\n" );

module.exports = {

  test_parse() {
    var model = MDCssModel.parse( SOURCE, BREAKPOINTS );
    assert.equal( model.open, "<style>" );
    assert.deepEqual( model.sections, {
      "": "/* promo box */\n.promo { padding: 1rem; content: \"}\"; }\n@media (max-width: 575.98px) { .promo { margin: 0; } }",
      sm: "",
      md: ".promo { padding: 2rem; }\n\n.promo h2 { font-size: 2rem; }",
      lg: ".promo { padding: 3rem; }",
    } );
    assert.equal( model.headers.lg, "@media screen and (min-width: 992px) {" );

    assert.equal( MDCssModel.parse( "<div>x</div>", BREAKPOINTS ), null );
    assert.equal( MDCssModel.parse( "<style>a{}</style>\n\n<style>b{}</style>", BREAKPOINTS ), null );
    assert.ok( MDCssModel.isCss( "  <style media=\"print\">\n</style>" ) );
  },

  test_join() {
    var model = MDCssModel.parse( SOURCE, BREAKPOINTS );
    var sections = Object.assign( {}, model.sections, { sm: ".promo { color: red; }", lg: "" } );
    var css = MDCssModel.joinSections( sections, BREAKPOINTS, model.headers );
    assert.equal( MDCssModel.build( model, css ), [
      "<style>",
      "/* promo box */",
      ".promo { padding: 1rem; content: \"}\"; }",
      "@media (max-width: 575.98px) { .promo { margin: 0; } }",
      "",
      "@media (min-width: 576px) {",
      "  .promo { color: red; }",
      "}",
      "",
      "@media (min-width: 768px) {",
      "  .promo { padding: 2rem; }",
      "",
      "  .promo h2 { font-size: 2rem; }",
      "}",
      "</style>",
    ].join( "\n" ) );
    // joined sections give the same sections back
    assert.deepEqual( MDCssModel.parse( MDCssModel.build( model, css ), BREAKPOINTS ).sections, Object.assign( {}, sections, { lg: "" } ) );
  },

  test_not_splittable() {
    // a rule after a breakpoint (moving it before would change the cascade)
    assert.equal( MDCssModel.splitSections( "@media (min-width: 768px) { a { color: red } }\na { color: blue }", BREAKPOINTS ), null );
    // descending order, a breakpoint twice
    assert.equal( MDCssModel.splitSections( "@media (min-width: 768px) { a{} }\n@media (min-width: 576px) { b{} }", BREAKPOINTS ), null );
    assert.equal( MDCssModel.splitSections( "@media (min-width: 768px) { a{} }\n@media (min-width: 768px) { b{} }", BREAKPOINTS ), null );
    // a comment between breakpoints
    assert.equal( MDCssModel.splitSections( "@media (min-width: 576px) { a{} }\n/* x */\n@media (min-width: 768px) { b{} }", BREAKPOINTS ), null );
    // an unknown width is a rule for all widths
    assert.deepEqual( MDCssModel.splitSections( "@media (min-width: 700px) { a{} }", BREAKPOINTS ).sections[ "" ], "@media (min-width: 700px) { a{} }" );
    // unfinished CSS
    assert.deepEqual( MDCssModel.splitSections( "a { color: red", BREAKPOINTS ).sections[ "" ], "a { color: red" );
  },

  test_create() {
    assert.equal( MDCssModel.create( "a { color: red }" ), "<style>\na { color: red }\n</style>" );
    assert.equal( MDCssModel.create( "" ), "<style>\n</style>" );
  },
};

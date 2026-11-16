var assert = require( "assert" );
var TypographyEditor = require( "../../public/admin/scripts/utils/typography_editor.js" );

module.exports = {

  test_state() {
    assert.deepEqual( TypographyEditor.state( "", "" ), { align: null, weight: null, italic: false } );
    assert.deepEqual( TypographyEditor.state( "card text-center font-weight-bold font-italic", "" ), { align: "center", weight: "bold", italic: true } );
    // from the style when there's no class
    assert.deepEqual( TypographyEditor.state( "card", "TEXT-ALIGN: Right; font-weight: 700 !important; font-style: italic" ), { align: "right", weight: "bold", italic: true } );
    assert.deepEqual( TypographyEditor.state( "", "font-weight: 600" ), { align: null, weight: null, italic: false } ); // not offered
    // a class wins
    assert.deepEqual( TypographyEditor.state( "text-left", "text-align: center" ).align, "left" );
    // responsive variants are not this panel's
    assert.equal( TypographyEditor.state( "text-md-center", "" ).align, null );
  },

  test_apply_align() {
    assert.deepEqual( TypographyEditor.apply( "card text-left p-3", "color: red;", { align: "center" } ), { class: "card p-3 text-center", style: "color: red;" } );
    // the declaration is removed
    assert.deepEqual( TypographyEditor.apply( "card", "text-align: right; color: red;", { align: "center" } ), { class: "card text-center", style: "color: red;" } );
    // removing
    assert.deepEqual( TypographyEditor.apply( "card text-center", "", { align: null } ), { class: "card", style: "" } );
    // nothing changes: the same strings
    assert.deepEqual( TypographyEditor.apply( "card  text-center", " color:red ", { align: "center" } ), { class: "card  text-center", style: " color:red " } );
  },

  test_apply_weight_and_italic() {
    assert.deepEqual( TypographyEditor.apply( "font-weight-bold", "font-weight: 300", { weight: "light" } ), { class: "font-weight-light", style: "" } );
    assert.deepEqual( TypographyEditor.apply( "font-weight-bold", "", { weight: null } ), { class: "", style: "" } );
    assert.deepEqual( TypographyEditor.apply( "x", "font-style: italic;", { italic: true } ), { class: "x font-italic", style: "" } );
    assert.deepEqual( TypographyEditor.apply( "x font-italic", "", { italic: false } ), { class: "x", style: "" } );
    // other keys are untouched
    assert.deepEqual( TypographyEditor.apply( "text-center font-italic", "", { weight: "bold" } ), { class: "text-center font-italic font-weight-bold", style: "" } );
  },

  test_config() {
    var bs5 = {
      align: { left: "text-start", center: "text-center", right: "text-end" },
      weight: { light: "fw-light", normal: "fw-normal", bold: "fw-bold" },
      italic: "fst-italic",
    };
    assert.deepEqual( TypographyEditor.state( "text-end fw-bold fst-italic", "", bs5 ), { align: "right", weight: "bold", italic: true } );
    assert.deepEqual( TypographyEditor.apply( "text-end", "", { align: "left" }, bs5 ), { class: "text-start", style: "" } );
    // justify has no class: nothing is added
    assert.deepEqual( TypographyEditor.apply( "text-end", "", { align: "justify" }, bs5 ), { class: "", style: "" } );
  },
};

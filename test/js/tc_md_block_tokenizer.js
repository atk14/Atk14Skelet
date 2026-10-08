var assert = require( "assert" );
var MDBlockTokenizer = require( "../../public/admin/scripts/utils/md_block_tokenizer.js" );

var BLOCK_SHORTCODES = [ "row", "col", "div", "tabs", "tab", "collapse", "linklist" ];

var tokenize = function( source ) {
  var doc = MDBlockTokenizer.tokenize( source, { blockShortcodes: BLOCK_SHORTCODES } );
  assert.equal( MDBlockTokenizer.join( doc ), source, "join() must give back the original source" );
  return doc;
};

var types = function( doc ) {
  return doc.blocks.map( b => b.type );
};

var raws = function( doc ) {
  return doc.blocks.map( b => b.raw );
};

module.exports = {

  test_basic_blocks() {
    var doc = tokenize( [
      "## O nás",
      "",
      "Jsme malá firma z Brna.",
      "Děláme weby.",
      "",
      "[#123 Image: Rose]",
      "",
      "- první bod",
      "- druhý bod",
      "",
      "> citace",
      "",
      "| a | b |",
      "|---|---|",
      "| 1 | 2 |",
      "",
      "---",
    ].join( "\n" ) );
    assert.deepEqual( types( doc ), [ "heading", "paragraph", "iobject", "list", "quote", "table", "hr" ] );
    assert.equal( doc.blocks[ 0 ].level, 2 );
    assert.equal( doc.blocks[ 1 ].raw, "Jsme malá firma z Brna.\nDěláme weby." );
    assert.equal( doc.blocks[ 2 ].id, 123 );
    assert.equal( doc.blocks[ 3 ].ordered, false );
    assert.deepEqual( doc.blocks.map( b => b.line ), [ 0, 2, 5, 7, 10, 12, 16 ] );
    assert.equal( doc.blocks[ 5 ].lineCount, 3 );
  },

  test_shortcode_with_blank_lines_is_one_block() {
    var tabs = [
      "[tabs]",
      "[tab name=\"Služby\"]",
      "Vývoj webů.",
      "",
      "A ještě něco.",
      "[/tab]",
      "[tab name=\"Kontakt\"]",
      "info@example.com",
      "[/tab]",
      "[/tabs]",
    ].join( "\n" );
    var doc = tokenize( "Úvod\n\n" + tabs + "\n\nZávěr" );
    assert.deepEqual( types( doc ), [ "paragraph", "shortcode", "paragraph" ] );
    var block = doc.blocks[ 1 ];
    assert.equal( block.raw, tabs );
    assert.equal( block.name, "tabs" );
    assert.equal( block.unclosed, false );
  },

  test_nested_shortcodes_of_the_same_name() {
    var source = "[div class=\"a\"]\n\n[div class=\"b\"]\n\nx\n\n[/div]\n\ny\n\n[/div]\n\nz";
    var doc = tokenize( source );
    assert.deepEqual( types( doc ), [ "shortcode", "paragraph" ] );
    assert.deepEqual( doc.blocks[ 0 ].attrs, { class: "a" } );
  },

  test_shortcode_attributes_and_single_line() {
    var doc = tokenize( "[row]\n[col class=\"col-md-6\" defaultclasses=0]1[/col]\n[col]2[/col]\n[/row]\n\n[div class=\"x\"]text[/div]" );
    assert.deepEqual( types( doc ), [ "shortcode", "shortcode" ] );
    assert.equal( doc.blocks[ 0 ].name, "row" );
    assert.equal( doc.blocks[ 1 ].name, "div" );
    assert.equal( doc.blocks[ 1 ].raw, "[div class=\"x\"]text[/div]" );
  },

  test_shortcode_without_blank_lines_around() {
    var doc = tokenize( "Text above\n[row]\n[col]x[/col]\n[/row]\nText below" );
    assert.deepEqual( raws( doc ), [ "Text above", "[row]\n[col]x[/col]\n[/row]", "Text below" ] );
    assert.deepEqual( doc.blocks.map( b => b.separator ), [ "\n", "\n", "\n\n" ] );
  },

  test_shortcode_starting_mid_line() {
    var doc = tokenize( "Text [div]\n\nInside\n\n[/div]\n\nAfter" );
    assert.deepEqual( raws( doc ), [ "Text [div]\n\nInside\n\n[/div]", "After" ] );
    assert.equal( doc.blocks[ 0 ].type, "paragraph" );
  },

  test_unclosed_shortcode_spans_to_the_end() {
    var doc = tokenize( "Intro\n\n[tabs]\n[tab name=\"A\"]\n\nx\n\nMore" );
    assert.deepEqual( types( doc ), [ "paragraph", "shortcode" ] );
    assert.equal( doc.blocks[ 1 ].unclosed, true );
  },

  test_inline_and_function_shortcodes_are_ignored() {
    var doc = tokenize( "Hello [span class=\"x\"]World[/span] [icon name=\"star\"]\n\nNext" );
    assert.deepEqual( types( doc ), [ "paragraph", "paragraph" ] );
  },

  test_shortcodes_in_code_are_ignored() {
    var source = "```\n[row]\n\n[col]\n```\n\nUse `[row]` like this\n\n[row]\n\n[/row]";
    var doc = tokenize( source );
    assert.deepEqual( types( doc ), [ "code", "paragraph", "shortcode" ] );
    assert.equal( doc.blocks[ 0 ].raw, "```\n[row]\n\n[col]\n```" );
  },

  test_fenced_code() {
    var doc = tokenize( "~~~~php\n<?php\n\n~~~\necho 1;\n~~~~\n\nText\n\n```\nunclosed\n\nfence" );
    assert.deepEqual( types( doc ), [ "code", "paragraph", "code" ] );
    assert.equal( doc.blocks[ 0 ].lineCount, 6 );
    assert.equal( doc.blocks[ 2 ].raw, "```\nunclosed\n\nfence" );
  },

  test_fence_inside_paragraph_and_shortcode() {
    var doc = tokenize( "Example:\n```\na\n\nb\n```\n\n[tab]\n```\n[/tab]\n```\n[/tab]" );
    assert.deepEqual( raws( doc ), [ "Example:\n```\na\n\nb\n```", "[tab]\n```\n[/tab]\n```\n[/tab]" ] );
  },

  test_loose_list_is_one_block() {
    var doc = tokenize( "1. one\n\n2. two\n\n   continued\n\n3. three\n\n- other list\n\nParagraph" );
    assert.deepEqual( types( doc ), [ "list", "list", "paragraph" ] );
    assert.equal( doc.blocks[ 0 ].raw, "1. one\n\n2. two\n\n   continued\n\n3. three" );
    assert.equal( doc.blocks[ 0 ].ordered, true );
    assert.equal( doc.blocks[ 1 ].ordered, false );
  },

  test_heading_splits_a_paragraph() {
    var doc = tokenize( "Text\n## Heading\nMore text" );
    assert.deepEqual( raws( doc ), [ "Text", "## Heading", "More text" ] );
  },

  test_setext_heading() {
    var doc = tokenize( "Title\n=====\n\nSubtitle\n--------\n\nText" );
    assert.deepEqual( types( doc ), [ "heading", "heading", "paragraph" ] );
    assert.deepEqual( doc.blocks.map( b => b.level ), [ 1, 2, undefined ] );
  },

  test_html_block() {
    var doc = tokenize( "<div class=\"a\">\n\n<div>\n\nx\n\n</div>\n\n</div>\n\n<span>inline</span>\n\n<section>\n\nunclosed" );
    assert.deepEqual( types( doc ), [ "html", "paragraph", "html", "paragraph" ] );
    assert.equal( doc.blocks[ 0 ].tag, "div" );
    assert.equal( doc.blocks[ 0 ].lineCount, 9 );
  },

  test_definitions() {
    var doc = tokenize( "See [the docs][1] and [x].\n\n```\n[2]: http://in-code\n```\n\n[1]: http://example.com\n[x]: http://x.com \"X\"" );
    assert.deepEqual( types( doc ), [ "paragraph", "code", "definitions" ] );
    assert.deepEqual( doc.definitions, [ "[1]: http://example.com", "[x]: http://x.com \"X\"" ] );
  },

  test_iobject_must_be_alone() {
    var doc = tokenize( "[#1]\n\n  [#2 Video: Song]  \n\nText [#3]\n\n[#4]\n[#5]" );
    assert.deepEqual( types( doc ), [ "iobject", "iobject", "paragraph", "paragraph" ] );
    assert.deepEqual( doc.blocks.map( b => b.id ), [ 1, 2, undefined, undefined ] );
  },

  test_lossless_whitespace() {
    [
      "",
      "\n\n",
      "   ",
      "Text",
      "\n\n  \nText\n\n\n\n   \nMore  \n\n",
      "A\r\n\r\nB\r\n- x\r\n\r\n- y\r\n",
      "[row]\r\n\r\n[col]\r\nx\r\n[/col]\r\n\r\n[/row]\r\n",
      "\t\n# H\n\t\n",
    ].forEach( source => {
      tokenize( source );
    } );

    var doc = tokenize( "\n\nA\n\n\n\nB\n" );
    assert.equal( doc.leading, "\n\n" );
    assert.equal( doc.blocks[ 0 ].separator, "\n\n\n\n" );
    assert.equal( doc.trailing, "\n" );

    doc = tokenize( "A\r\n\r\nB" );
    assert.deepEqual( raws( doc ), [ "A\r", "B" ] );
    assert.equal( doc.blocks[ 0 ].separator, "\n\r\n" );

    assert.deepEqual( tokenize( "" ).blocks, [] );
    assert.deepEqual( tokenize( " \n " ).blocks, [] );
  },

  test_normalize_after_move() {
    var doc = tokenize( "First\n[row]\n[/row]\nSecond" );
    // moving the row to the end makes "First" and "Second" adjacent
    doc.blocks.push( doc.blocks.splice( 1, 1 )[ 0 ] );
    MDBlockTokenizer.normalize( doc );
    assert.equal( MDBlockTokenizer.join( doc ), "First\n\nSecond\n\n[row]\n[/row]" );
  },

  test_normalize_given_indexes() {
    var doc = tokenize( "# A\nText\n# B\nMore\n# C\nEnd" );
    MDBlockTokenizer.normalize( doc, [ 2, 3 ] );
    assert.equal( MDBlockTokenizer.join( doc ), "# A\nText\n# B\n\nMore\n\n# C\nEnd" );
  },

  test_join_new_blocks() {
    var doc = tokenize( "A\n\nB\n" );
    doc.blocks.splice( 1, 0, { type: "paragraph", raw: "New" } );
    assert.equal( MDBlockTokenizer.join( doc ), "A\n\nNew\n\nB\n" );
  },

  test_default_block_shortcodes() {
    var doc = MDBlockTokenizer.tokenize( "[row]\n\n[col]\n\nx\n\n[/col]\n\n[/row]\n\n[tabs]\n\n[/tabs]" );
    assert.deepEqual( types( doc ), [ "shortcode", "paragraph", "paragraph" ] );
  },
};

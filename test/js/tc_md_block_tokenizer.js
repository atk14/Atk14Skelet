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

  test_reorder() {
    // the heading is followed by its paragraph without a blank line, that is left untouched when not moved
    var doc = tokenize( "# A\nText A\n\n\nB\n\nC\n" );
    MDBlockTokenizer.reorder( doc, [ 0, 1, 3, 2 ] );
    assert.equal( MDBlockTokenizer.join( doc ), "# A\nText A\n\n\nC\n\nB\n" );

    // moving the paragraph away from its heading separates them by a blank line;
    // the former last block gets a separator, the trailing whitespace of the document stays
    doc = tokenize( "# A\nText A\n\nB\n\nC\n" );
    MDBlockTokenizer.reorder( doc, [ 0, 2, 3, 1 ] );
    assert.equal( MDBlockTokenizer.join( doc ), "# A\n\nB\n\nC\n\nText A\n" );

    // moving the first block to the end
    doc = tokenize( "\nFirst\n[row]\n[/row]\nLast" );
    MDBlockTokenizer.reorder( doc, [ 1, 2, 0 ] );
    assert.equal( MDBlockTokenizer.join( doc ), "\n[row]\n[/row]\nLast\n\nFirst" );

    // nothing moved, nothing changed
    var source = "# A\nText\n# B\nMore";
    doc = tokenize( source );
    MDBlockTokenizer.reorder( doc, [ 0, 1, 2, 3 ] );
    assert.equal( MDBlockTokenizer.join( doc ), source );

    assert.throws( function() { MDBlockTokenizer.reorder( tokenize( "A\n\nB" ), [ 0, 0 ] ); }, /every block index/ );
    assert.throws( function() { MDBlockTokenizer.reorder( tokenize( "A\n\nB" ), [ 0 ] ); }, /every block index/ );
  },

  test_container() {
    var row = [
      "[row class=\"row--borders\"]",
      "  [col class=\"col-md-6\" defaultclasses=0]",
      "### Column 1",
      "Text of the first column.",
      "",
      "[#12 Picture]",
      "[/col]",
      "  [col]Two[/col]",
      "[/row]",
    ].join( "\n" );
    var doc = tokenize( "Intro\n\n" + row + "\n\nOutro\n" );
    assert.deepEqual( types( doc ), [ "paragraph", "shortcode", "paragraph" ] );
    var c = doc.blocks[ 1 ].container;
    assert.ok( c );
    assert.equal( c.head, "[row class=\"row--borders\"]\n  " );
    assert.equal( c.tail, "[/row]" );
    assert.deepEqual( c.children.map( ch => ch.open ), [ "[col class=\"col-md-6\" defaultclasses=0]", "[col]" ] );
    assert.deepEqual( c.children.map( ch => ch.close ), [ "[/col]", "[/col]" ] );
    assert.deepEqual( c.children.map( ch => ch.after ), [ "\n  ", "\n" ] );
    assert.deepEqual( c.children[ 0 ].attrs, { class: "col-md-6", defaultclasses: "0" } );

    var col1 = c.children[ 0 ].doc;
    assert.equal( col1.nested, true );
    assert.deepEqual( types( col1 ), [ "heading", "paragraph", "iobject" ] );
    assert.equal( col1.leading, "\n" );
    assert.equal( col1.trailing, "\n" );
    // lines in the whole source
    assert.deepEqual( col1.blocks.map( b => b.line ), [ 4, 5, 7 ] );
    assert.deepEqual( raws( c.children[ 1 ].doc ), [ "Two" ] );
    assert.equal( c.children[ 1 ].doc.blocks[ 0 ].line, 9 );

    // a change inside a column gets into the source
    col1.blocks[ 1 ].raw = "Changed text.";
    assert.equal( MDBlockTokenizer.join( doc ), "Intro\n\n" + row.replace( "Text of the first column.", "Changed text." ) + "\n\nOutro\n" );
    assert.equal( doc.blocks[ 1 ].raw.indexOf( "Changed text." ) > 0, true );

    // reordering blocks inside a column
    MDBlockTokenizer.reorder( col1, [ 2, 0, 1 ] );
    assert.equal( MDBlockTokenizer.join( c.children[ 0 ].doc ), "\n[#12 Picture]\n\n### Column 1\nChanged text.\n" );
  },

  test_container_inline_child_gets_own_lines() {
    var doc = tokenize( "[row]\n[col]One[/col]\n[col][/col]\n[/row]" );
    var c = doc.blocks[ 0 ].container;
    assert.deepEqual( raws( c.children[ 1 ].doc ), [] );

    // two blocks in a one-line column
    var col = c.children[ 0 ].doc;
    col.blocks.push( { type: "paragraph", raw: "Another" } );
    MDBlockTokenizer.normalize( col, [ 0 ] );
    // a block in an empty column stays on the line
    c.children[ 1 ].doc.blocks.push( { type: "paragraph", raw: "Two" } );
    MDBlockTokenizer.normalize( c.children[ 1 ].doc );
    assert.equal( MDBlockTokenizer.join( doc ), "[row]\n[col]\nOne\n\nAnother\n[/col]\n[col]Two[/col]\n[/row]" );
  },

  test_container_lossless() {
    [
      "[row]\r\n  [col]\r\n\r\nA\r\n\r\n  [/col]\r\n[/row]\r\n",
      "[row][col]A[/col][col]B[/col][/row]",
      "  [row]\n[col]\n```\n[/col]\n```\n[/col]\n[/row]",
      "[row]\n[col]\n[row]\n[col]nested[/col]\n[/row]\n[/col]\n[/row]",
    ].forEach( source => {
      var doc = tokenize( source );
      assert.ok( doc.blocks[ 0 ].container, source );
    } );

    // a nested row is a container too
    var doc = tokenize( "[row]\n[col]\n[row]\n[col]nested[/col]\n[/row]\n[/col]\n[/row]" );
    var inner = doc.blocks[ 0 ].container.children[ 0 ].doc.blocks[ 0 ];
    assert.equal( inner.name, "row" );
    assert.deepEqual( raws( inner.container.children[ 0 ].doc ), [ "nested" ] );
    assert.equal( inner.container.children[ 0 ].doc.blocks[ 0 ].line, 3 );
  },

  test_tabs_container() {
    var source = [
      "[tabs]",
      "[tab name=\"One\"]",
      "Text of one.",
      "",
      "[row]",
      "[col]A[/col]",
      "[col]B[/col]",
      "[/row]",
      "[/tab]",
      "[tab name='Two']",
      "[/tab]",
      "[/tabs]",
    ].join( "\n" );
    var doc = tokenize( source );
    var c = doc.blocks[ 0 ].container;
    assert.ok( c );
    assert.deepEqual( c.children.map( ch => ch.attrs.name ), [ "One", "Two" ] );
    assert.deepEqual( types( c.children[ 0 ].doc ), [ "paragraph", "shortcode" ] );
    assert.deepEqual( raws( c.children[ 1 ].doc ), [] );
    // a row in a tab is a container too
    var row = c.children[ 0 ].doc.blocks[ 1 ];
    assert.deepEqual( row.container.children.map( ch => raws( ch.doc ) ), [ [ "A" ], [ "B" ] ] );
    assert.equal( row.container.children[ 1 ].doc.blocks[ 0 ].line, 6 );

    // a change in a column in a tab gets into the source
    row.container.children[ 1 ].doc.blocks[ 0 ].raw = "B2";
    assert.equal( MDBlockTokenizer.join( doc ), source.replace( "[col]B[/col]", "[col]B2[/col]" ) );

    // not a container: a tab without [/tab], text between tabs
    [ "[tabs]\n[tab name=\"A\"]\nx\n[/tabs]", "[tabs]\n[tab]x[/tab]\nbetween\n[tab]y[/tab]\n[/tabs]" ].forEach( s => {
      assert.equal( tokenize( s ).blocks[ 0 ].container, undefined, s );
    } );
  },

  test_div_container() {
    var source = "Intro\n\n[div style=\"max-width:30%; margin: auto;\"]\n[#57 Picture]\n[/div]\n\nOutro";
    var doc = tokenize( source );
    var div = doc.blocks[ 1 ];
    assert.equal( div.name, "div" );
    var c = div.container;
    assert.equal( c.head, "" );
    assert.equal( c.tail, "" );
    assert.equal( c.children.length, 1 );
    assert.equal( c.children[ 0 ].open, "[div style=\"max-width:30%; margin: auto;\"]" );
    assert.equal( c.children[ 0 ].close, "[/div]" );
    assert.deepEqual( c.children[ 0 ].attrs, { style: "max-width:30%; margin: auto;" } );
    assert.deepEqual( types( c.children[ 0 ].doc ), [ "iobject" ] );
    assert.equal( c.children[ 0 ].doc.blocks[ 0 ].line, 3 );

    // the iobject replaced
    c.children[ 0 ].doc.blocks[ 0 ].raw = "[#58 Picture: Rose]";
    assert.equal( MDBlockTokenizer.join( doc ), source.replace( "[#57 Picture]", "[#58 Picture: Rose]" ) );

    // nested divs
    doc = tokenize( "[div class=\"fullwidth\"]\n[div style=\"font-size: 8vw\"]\nBig\n[/div]\n[/div]" );
    var inner = doc.blocks[ 0 ].container.children[ 0 ].doc.blocks[ 0 ];
    assert.equal( inner.name, "div" );
    assert.deepEqual( raws( inner.container.children[ 0 ].doc ), [ "Big" ] );

    // a one-line div, an empty div
    assert.deepEqual( raws( tokenize( "[div class=\"x\"]text[/div]" ).blocks[ 0 ].container.children[ 0 ].doc ), [ "text" ] );
    assert.deepEqual( raws( tokenize( "[div class=\"x\"][/div]" ).blocks[ 0 ].container.children[ 0 ].doc ), [] );

    // not a container: text after the closing tag
    assert.equal( tokenize( "[div]a[/div] b" ).blocks[ 0 ].container, undefined );
  },

  test_nested_content_on_own_lines() {
    // a block added into "[div]\n[/div]" ends on its own line too
    var doc = tokenize( "[div]\n[/div]" );
    var content = doc.blocks[ 0 ].container.children[ 0 ].doc;
    content.blocks.push( { type: "paragraph", raw: "Text" } );
    MDBlockTokenizer.normalize( content, [ 0 ] );
    assert.equal( MDBlockTokenizer.join( doc ), "[div]\nText\n[/div]" );
  },

  test_not_a_container() {
    [
      "[row]\ntext between\n[col]A[/col]\n[/row]", // text outside columns
      "[row]\n[col]A[/col]\nafter\n[/row]",
      "[row]\n[div]A[/div]\n[/row]", // not a column
      "[row]\n[/row]", // no columns
      "[row]\n[col]A\n[/row]", // unpaired
      "[row]\n[col]A[/col]\n[/row] trailing text",
      "[row]\n[col]A[/col]", // unclosed
    ].forEach( source => {
      var doc = tokenize( source );
      assert.equal( doc.blocks[ 0 ].type, "shortcode", source );
      assert.equal( doc.blocks[ 0 ].container, undefined, source );
    } );

    // containers can be switched off
    var doc = MDBlockTokenizer.tokenize( "[row]\n[col]A[/col]\n[/row]", { blockShortcodes: BLOCK_SHORTCODES, containers: {} } );
    assert.equal( doc.blocks[ 0 ].container, undefined );
  },

  test_replacing_a_container_raw() {
    var doc = tokenize( "[row]\n[col]A[/col]\n[/row]" );
    doc.blocks[ 0 ].raw = "[row]\n[col]B[/col]\n[/row]";
    delete doc.blocks[ 0 ].container;
    assert.equal( MDBlockTokenizer.join( doc ), "[row]\n[col]B[/col]\n[/row]" );
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

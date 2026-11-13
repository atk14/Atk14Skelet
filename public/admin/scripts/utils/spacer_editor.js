/**
 * Spacer editor
 * Inserts a DrInk Markdown [spacer height="2rem"] shortcode (a vertical space, rendered
 * by app/helpers/function.drink_shortcode__spacer.php as an empty div.spacer with the height in its style),
 * or edits the height of an existing one: a slider of whole numbers and a unit (px, em, rem).
 * Registered in UTILS.MDEditorInserts, so it's offered in the "Insert" dropdown of MD editors,
 * in the "Add block" menu of the block view and as the editor of [spacer] blocks:
 * in the dialog, or in the inspector of the block view (inspect()) where changes apply as they are typed.
 * The block view knows a [spacer] as a block thanks to data-function_shortcodes of the textarea
 * (see app/fields/markdown_field.php and md_block_tokenizer.js).
 *
 * Usage:
 *
 *   if( document.getElementById( "spacereditor_modal" ) ) {
 *     new UTILS.SpacerEditor();
 *   }
 *
 * Dependencies:
 *
 * md_shortcodes.js, md_editor_inserts.js
 * _spacer_editor_modal.tpl (the dialog and the template of the inspector's fields)
 * dialog.js (a native <dialog>), Bootstrap 4+ (styles)
 */
window.UTILS = window.UTILS || { };

window.UTILS.SpacerEditor = class {
  texts = window.spacereditorTexts; // UI strings (rendered in template)

  static units = [ "px", "em", "rem" ];

  // the height of a new spacer; a [spacer] without a height gets the same on the web
  static defaultHeight = { value: "2", unit: "rem" };

  // the maximum of the slider per unit (a bigger height of an existing spacer moves it up)
  static maxValues = { px: 200, em: 20, rem: 20 };

  // px in an em or rem when the unit is switched (the usual font size)
  static pxPerEm = 16;

  constructor() {
    if ( window.UTILS.SpacerEditor.instance ) {
      window.UTILS.SpacerEditor.instance.attachToolbarButtons();
      return window.UTILS.SpacerEditor.instance;
    }
    window.UTILS.SpacerEditor.instance = this;

    this.modal = document.getElementById( "spacereditor_modal" );
    this.valueInput = this.modal.querySelector( "#spacereditor_value" );
    this.fields = window.UTILS.SpacerEditor.setUpFields( this.modal.querySelector( ".js--height-fields" ) );
    this.saveBtn = this.modal.querySelector( "#spacereditor_save_btn" );
    this.save = null; // callback given by open()
    this.edited = null; // { raw, model } of the edited [spacer]

    this.attachToolbarButtons();

    this.saveBtn.addEventListener( "click", () => this.saveCode() );
    this.valueInput.addEventListener( "keydown", e => {
      if ( e.key === "Enter" ) {
        e.preventDefault();
        this.saveCode();
      }
    } );
  }

  /**
   * Registers the editor in UTILS.MDEditorInserts (adds "Spacer" to the "Insert" dropdown of every MD editor toolbar)
   */
  attachToolbarButtons() {
    window.UTILS.MDEditorInserts.register( {
      name: "spacereditor",
      text: "<span class=\"fa-solid fa-arrows-up-down\" aria-hidden=\"true\"></span> " + ( this.texts ? this.texts.title : "Spacer" ),
      title: this.texts ? this.texts.title : "Spacer",
      modal: "#spacereditor_modal",
      inline: false,
      shortcode: "spacer",
      parse: raw => window.UTILS.SpacerEditor.parse( raw ),
      open: options => this.open( options ),
      inspect: ( container, options ) => this.inspect( container, options ),
    } );
  }

  /**
   * Parses a [spacer]; returns null when it's not a single [spacer] tag or its height isn't a number with px, em or rem
   *   "[spacer height=\"2rem\"]" -> { attrs: { height: "2rem" }, height: { value: "2", unit: "rem" } }
   *   "[spacer]" -> { attrs: {}, height: null }
   * @param {String} raw
   * @returns {Object|null}
   */
  static parse( raw ) {
    let source = String( raw || "" ).trim();
    let tags = window.UTILS.MDShortcodes.scanTags( source, [ "spacer" ] );
    if ( tags.length !== 1 || tags[ 0 ].closing || tags[ 0 ].start !== 0 || tags[ 0 ].end !== source.length ) {
      return null;
    }
    let attrs = tags[ 0 ].attrs;
    let height = null;
    if ( attrs.height !== undefined ) {
      height = window.UTILS.SpacerEditor.parseHeight( attrs.height );
      if ( !height ) {
        return null;
      }
    }
    return { attrs: attrs, height: height };
  }

  /**
   * "2rem" -> { value: "2", unit: "rem" }, "40" -> { value: "40", unit: "px" } (as on the web), "10vh" -> null
   * @param {String} height
   * @returns {Object|null}
   */
  static parseHeight( height ) {
    let m = String( height ).trim().match( /^(\d+(?:\.\d+)?|\.\d+)\s*(px|em|rem)?$/i );
    return m ? { value: m[ 1 ], unit: ( m[ 2 ] || "px" ).toLowerCase() } : null;
  }

  /**
   * Fills the form with the height of the given [spacer], or with the default one
   * @param {Object} options - { raw, save }
   */
  open( options ) {
    this.save = options.save;
    let model = options.raw ? window.UTILS.SpacerEditor.parse( options.raw ) : null;
    this.edited = model ? { raw: options.raw, model: model } : null;
    this.fields.set( window.UTILS.SpacerEditor.shownHeight( model ) );
    this.saveBtn.textContent = model ? this.texts.save : this.texts.insert;
  }

  /**
   * The height shown in the form: the spacer's own one, the default one for a new spacer or a spacer without a height
   * @param {Object|null} model
   * @returns {Object} { value, unit }
   */
  static shownHeight( model ) {
    return model && model.height ? model.height : window.UTILS.SpacerEditor.defaultHeight;
  }

  /**
   * Builds the [spacer] from the form
   */
  saveCode() {
    let height = this.fields.get();
    let code = window.UTILS.SpacerEditor.build( this.edited, height.value, height.unit );
    if ( this.save ) {
      this.save( code );
    }
  }

  /**
   * The fields in the inspector of the block view; every change is passed to change() at once
   * @param {Element} container
   * @param {Object} options - { raw, change }
   * @returns {Boolean} false when the [spacer] can't be edited here
   */
  inspect( container, options ) {
    let model = window.UTILS.SpacerEditor.parse( options.raw );
    let template = document.getElementById( "spacereditor_inspector" );
    if ( !model || !template ) {
      return false;
    }
    let edited = { raw: options.raw, model: model };
    let fields = template.content.cloneNode( true );
    // labels need unique ids, there may be more editors in the page
    let id = "spacereditor_inspector_" + ( ++window.UTILS.SpacerEditor.inspectorCount );
    let valueInput = fields.querySelector( ".js--value" );
    valueInput.id = id + "_value";
    fields.querySelector( "label.js--height" ).htmlFor = valueInput.id;
    let heightFields = window.UTILS.SpacerEditor.setUpFields( fields.querySelector( ".js--height-fields" ), () => {
      let height = heightFields.get();
      options.change( window.UTILS.SpacerEditor.build( edited, height.value, height.unit ) );
    } );
    heightFields.set( window.UTILS.SpacerEditor.shownHeight( model ) );
    container.appendChild( fields );
    return true;
  }

  /**
   * The height fields: a slider of whole numbers (.js--value), the shown value (.js--output) and the unit (.js--unit).
   * Until the slider or the unit is changed, get() returns the height as it was set (e.g. 1.5rem a slider can't show);
   * switching the unit converts the value (1rem = 1em = 16px), rounded to a whole number.
   * @param {Element} root
   * @param {Function} onChange
   * @returns {Object} { set( { value, unit } ), get() }
   */
  static setUpFields( root, onChange ) {
    let self = window.UTILS.SpacerEditor;
    let slider = root.querySelector( ".js--value" );
    let output = root.querySelector( ".js--output" );
    let unitSelect = root.querySelector( ".js--unit" );
    let original = null;
    let touched = false;
    let shownUnit = null; // the select's value before its change
    let show = ( value, unit ) => {
      shownUnit = unit;
      unitSelect.value = unit;
      slider.max = Math.max( self.maxValues[ unit ], Math.ceil( parseFloat( value ) || 0 ) );
      slider.value = value;
      output.textContent = value;
    };
    slider.addEventListener( "input", () => {
      touched = true;
      output.textContent = slider.value;
      if ( onChange ) {
        onChange();
      }
    } );
    unitSelect.addEventListener( "change", () => {
      let current = touched ? slider.value : original.value;
      let value = self.convert( parseFloat( current ) || 0, shownUnit, unitSelect.value );
      touched = true;
      show( String( value ), unitSelect.value );
      if ( onChange ) {
        onChange();
      }
    } );
    let fields = {
      set( height ) {
        original = { value: height.value, unit: height.unit };
        touched = false;
        show( height.value, height.unit );
      },
      get() {
        return touched ? { value: slider.value, unit: unitSelect.value } : original;
      },
    };
    return fields;
  }

  /**
   * A whole number of the other unit: ( 32, "px", "rem" ) -> 2, ( 2, "rem", "px" ) -> 32, ( 2, "em", "rem" ) -> 2
   * @param {Number} value
   * @param {String} from
   * @param {String} to
   * @returns {Number}
   */
  static convert( value, from, to ) {
    let self = window.UTILS.SpacerEditor;
    let px = from === "px" ? value : value * self.pxPerEm;
    return Math.round( to === "px" ? px : px / self.pxPerEm );
  }

  static inspectorCount = 0;

  /**
   * Builds the [spacer]: other attributes are kept; an unchanged height keeps the source exactly as it was,
   * an empty or invalid number removes the height (the default one applies)
   * @param {Object|null} edited - { raw, model } of the edited [spacer], null for a new one
   * @param {String} value - e.g. "2"
   * @param {String} unit - "px", "em" or "rem"
   * @returns {String}
   */
  static build( edited, value, unit ) {
    let self = window.UTILS.SpacerEditor;
    value = String( value || "" ).trim();
    unit = self.units.indexOf( unit ) >= 0 ? unit : "px";
    let height = self.parseHeight( value ) && !/[a-z]/i.test( value ) ? value + unit : "";
    let attrs = edited ? Object.assign( {}, edited.model.attrs ) : {};
    if ( edited ) {
      let shown = self.shownHeight( edited.model );
      if ( value === shown.value && unit === shown.unit ) {
        return edited.raw;
      }
    }
    if ( height ) {
      attrs.height = height;
    } else {
      delete attrs.height;
    }
    return window.UTILS.MDShortcodes.buildOpeningTag( "spacer", attrs );
  }
};

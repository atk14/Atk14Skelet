<!-- Modal -->
 <template id="layout_designer_cell_controls">
  <div class="cellnumber"></div>
  <div class="cell-controls">
    <div class="js-span-minus cellbtn">{!"square-minus"|icon}</div>
    <div class="js-span-display">0</div>
    <div class="js-span-plus cellbtn">{!"square-plus"|icon}</div>
    <div class="js-hide-cell cellbtn ml-1">{!"eye"|icon:regular}</div>
  </div>
 </template>

 <script>
  window.layoutDesignerTexts = {
    titleXL: '{!"display"|icon} {t}XL - Large desktop{/t}',
    titleLG: '{!"laptop"|icon} {t}LG - Laptop{/t}',
    titleMD: '{!"tablet-screen-button"|icon} {t}MD - Tablet{/t}',
    titleSM: '{!"tablet-screen-button"|icon} {t}SM - Small tablet{/t}',
    titleXS: '{!"mobile-screen-button"|icon} {t}XS - Phone{/t}',
    wrongPaste: "{t}Cannot paste columns: Number of pasted columns do not match number of current columns.{/t}",
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
    column: "{t escape=javascript}Column{/t}",
    widths: "{t escape=javascript}Widths{/t}",
    defaultWidths: "{t escape=javascript}default, by the number of columns{/t}",
  }
 </script>

 {* a [row] and a column in the inspector of the block view (see LayoutDesigner.inspect() and inspectChild()) *}
 <template id="layout_designer_inspector">
  <button type="button" class="btn btn-sm btn-outline-primary mb-3 js--layout">{!"table-columns"|icon} {t}Column widths{/t}…</button>
  <div class="form-label js--columns-label">{t}Columns{/t}</div>
  <div class="layout-designer-inspector__columns mb-3 js--columns" role="group"></div>
  <div class="form-label js--class-label">{t}CSS class{/t}</div>
  <div class="mb-2 js--class" role="group"></div>
  <div class="form-label js--style-label">{t}CSS style{/t}</div>
  <div class="js--style" role="group"></div>
 </template>

 <template id="layout_designer_col_inspector">
  <p class="md-inspector__note mb-1 js--widths"></p>
  <button type="button" class="btn btn-sm btn-outline-primary mb-3 js--layout">{!"table-columns"|icon} {t}Column widths{/t}…</button>
  <div class="form-label js--class-label">{t}CSS class{/t}</div>
  <div class="mb-2 js--class" role="group"></div>
  <div class="form-label js--style-label">{t}CSS style{/t}</div>
  <div class="js--style" role="group"></div>
 </template>

 <template id="layout_designer_row">
    <div class="layout-designer__row-editor">
      <div class="layout-designer__row-header mb-3">
        <div class="js--row-title"></div>
        <div>
          <button class="btn btn-sm btn-outline-primary js--btn-copy">{t}Copy widths{/t}</button>
          <button class="btn btn-sm btn-outline-primary js--btn-paste">{t}Paste widths{/t}</button>
        </div>
      </div>

      <div class="row row-edit-area">  
      </div>

      <div class="hidden-cells-container"><span class="js-hidden_cells_title">{t}Hidden cells: {/t}</span></div>

    </div> 
 </template>

 <template id="hidden_cell_avatar">
  <div>
    <div class="cellnumber"></div>
    <div class="js-show-cell cellbtn ml-1">{!"eye-slash"|icon:regular}</div>
  </div>
 </template>


<dialog class="dialog dialog-xl" id="layout_designer_modal" aria-labelledby="layout_modal_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="layout_modal_label">Layout Designer</h1>
    <button type="button" class="btn-close" commandfor="layout_designer_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body layout-designer" id="layout-designer">

    <div class="form-group row mb-3">
      <label for="layout_designer_column_count" class="form-label col-4">{t}Number of columns{/t}</label>
      <div class="col-8">
      <select name="" id="layout_designer_column_count" autofocus class="{if USING_BOOTSTRAP5}form-select{else}form-control{/if}">
        {for $i=1 to 12}
          <option value="{$i}"{if $i==2} selected{/if}>{$i}</option>
        {/for}
      </select>
    </div>
    </div>

    <div class="alert alert-info js--layout-designer-note d-none">
      {t}Content of the removed columns will be moved to the last column.{/t}
    </div>

    <div class="editor-container">
    </div>
    



  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="layout_designer_modal" command="close">{t}Close{/t}</button>
      <button type="button" class="btn btn-secondary" id="reset_btn">{t}Reset{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-link" id="copy_html_btn">{t}Copy HTML code to clipboard{/t}</button>
      <button type="button" class="btn btn-outline-secondary" id="copy_md_btn">{t}Copy Markdown code to clipboard{/t}</button>
      <button type="button" class="btn btn-primary" id="layout_designer_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>




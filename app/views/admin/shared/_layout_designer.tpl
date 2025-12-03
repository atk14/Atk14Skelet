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
  }
 </script>

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


<div class="modal fade" id="layout_designer_modal" tabindex="-1" aria-labelledby="layout_modal_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="layout_modal_label">Layout Designer</h5>
        {if USING_BOOTSTRAP5}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        {else}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        {/if}
      </div>
      <div class="modal-body layout-designer" id="layout-designer">

        <div class="form-group row mb-3">
          <label for="layout_designer_column_count" class="form-label col-4">{t}Number of columns{/t}</label>
          <div class="col-8">
          <select name="" id="layout_designer_column_count" class="{if USING_BOOTSTRAP5}form-select{else}form-control{/if}">
            {for $i=1 to 12}
              <option value="{$i}"{if $i==2} selected{/if}>{$i}</option>
            {/for}
          </select>
        </div>
        </div>

        <div class="editor-container">
        </div>
        



      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">{t}Cancel{/t}</button>
          <button type="button" class="btn btn-secondary" id="reset_btn">{t}Reset{/t}</button>
        </div>
        <div>
          <button type="button" class="btn btn-link" id="copy_html_btn">{t}Copy HTML code to clipboard{/t}</button>
          <button type="button" class="btn btn-primary" id="copy_md_btn">{t}Copy Markdown code to clipboard{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>




<!-- Modal -->
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

        <div class="form-group">
          <label for="layout_designer_column_count" class="">{t}Number of columns{/t}</label>
          <select name="" id="layout_designer_column_count" class="form-control">
            {for $i=1 to 12}
              <option value="{$i}"{if $i==2} selected{/if}>{$i}</option>
            {/for}
          </select>
        </div>

        <div class="layout-designer__columns" id="xxxxxx">
          <div class="row" id="row_xl">

          </div>
        </div>



      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">{t}Copy code to clipboard{/t}</button>
      </div>
    </div>
  </div>
</div>




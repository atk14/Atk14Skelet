{*
 * Modal for inserting a [div] shortcode or editing its class and style, see public/admin/scripts/utils/div_editor.js
 *}
<script>
  window.diveditorTexts = {
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}"
  };
</script>

<div class="modal fade" id="diveditor_modal" tabindex="-1" aria-labelledby="diveditor_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="diveditor_label">{t}Div{/t}</h5>
        {if USING_BOOTSTRAP5}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        {else}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        {/if}
      </div>
      <div class="modal-body">
        <p class="text-muted small">{t}A box around blocks. Its content is edited directly in the block view.{/t}</p>
        <label for="diveditor_class" class="form-label">{t}CSS class{/t}</label>
        <input type="text" class="form-control mb-3" id="diveditor_class" placeholder="{t}e.g.{/t} text-center bg-light p-3">
        <label for="diveditor_style" class="form-label">{t}CSS style{/t}</label>
        <input type="text" class="form-control" id="diveditor_style" placeholder="{t}e.g.{/t} max-width: 30%; margin: auto;">
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">{t}Close{/t}</button>
        </div>
        <div>
          <button type="button" class="btn btn-primary" id="diveditor_save_btn">{t}Insert{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>

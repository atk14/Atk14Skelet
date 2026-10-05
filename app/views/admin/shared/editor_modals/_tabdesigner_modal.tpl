<template id="tabdesigner_tab_fields">
  <div class="tabdesigner__tab border rounded p-2 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label mb-0">{t}Tab name{/t}</label>
      <button type="button" class="btn btn-sm btn-outline-danger js--remove-tab" title="{t}Remove tab{/t}">&times;</button>
    </div>
    <input type="text" class="form-control mb-2 js--tab-name" placeholder="{t}Tab name{/t}">
    <textarea class="form-control js--tab-content" rows="4" placeholder="{t}Tab content{/t}"></textarea>
  </div>
</template>

<script>
  window.tabdesignerTexts = {
    copied: "{t}Copied!{/t}",
  }
</script>

<div class="modal fade" id="tabdesigner_modal" tabindex="-1" aria-labelledby="tabdesigner_label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="tabdesigner_label">{t}Tabs{/t}</h5>
        {if USING_BOOTSTRAP5}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        {else}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        {/if}
      </div>
      <div class="modal-body tabdesigner" id="tabdesigner">

        <div class="tabdesigner__tabs"></div>

        <button type="button" class="btn btn-outline-primary btn-sm js--add-tab">
          {!"plus"|icon} {t}Add tab{/t}
        </button>

      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">{t}Close{/t}</button>
        </div>
        <div>
          <button type="button" class="btn btn-primary" id="tabdesigner_copy_btn">{t}Copy to clipboard{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>

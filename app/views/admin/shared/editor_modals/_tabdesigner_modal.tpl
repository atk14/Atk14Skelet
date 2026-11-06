<template id="tabdesigner_tab_fields">
  <div class="tabdesigner__tab border rounded p-2 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label mb-0">{t}Tab name{/t}</label>
      <button type="button" class="btn btn-sm btn-outline-danger js--remove-tab" title="{t}Remove tab{/t}">&times;</button>
    </div>
    <input type="text" class="form-control mb-2 js--tab-name" autofocus placeholder="{t}Tab name{/t}">
    <textarea class="form-control js--tab-content" rows="4" placeholder="{t}Tab content{/t}"></textarea>
  </div>
</template>

<script>
  window.tabdesignerTexts = {
    copied: "{t escape=javascript}Copied!{/t}",
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
  }
</script>

<dialog class="dialog dialog-lg" id="tabdesigner_modal" aria-labelledby="tabdesigner_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="tabdesigner_label">{t}Tabs{/t}</h1>
    <button type="button" class="btn-close" commandfor="tabdesigner_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body tabdesigner" id="tabdesigner">

    <div class="tabdesigner__tabs"></div>

    <button type="button" class="btn btn-outline-primary btn-sm js--add-tab">
      {!"plus"|icon} {t}Add tab{/t}
    </button>

  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="tabdesigner_modal" command="close">{t}Close{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-outline-secondary" id="tabdesigner_copy_btn">{t}Copy to clipboard{/t}</button>
      <button type="button" class="btn btn-primary" id="tabdesigner_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>

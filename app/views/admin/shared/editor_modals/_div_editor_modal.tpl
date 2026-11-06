{*
 * Modal for inserting a [div] shortcode or editing its class and style, see public/admin/scripts/utils/div_editor.js
 *}
<script>
  window.diveditorTexts = {
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}"
  };
</script>

{* fields of a [div] in the inspector of the block view (see DivEditor.inspect()) *}
<template id="diveditor_inspector">
  <label class="form-label js--class">{t}CSS class{/t}</label>
  <input type="text" class="form-control form-control-sm mb-2 js--class" placeholder="{t}e.g.{/t} text-center bg-light p-3">
  <div class="form-label js--style">{t}CSS style{/t}</div>
  <div class="js--style" role="group"></div>
</template>

<dialog class="dialog" id="diveditor_modal" aria-labelledby="diveditor_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="diveditor_label">{t}Div{/t}</h1>
    <button type="button" class="btn-close" commandfor="diveditor_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body">
    <p class="text-muted small">{t}A box around blocks. Its content is edited directly in the block view.{/t}</p>
    <label for="diveditor_class" class="form-label">{t}CSS class{/t}</label>
    <input type="text" class="form-control mb-3" id="diveditor_class" autofocus placeholder="{t}e.g.{/t} text-center bg-light p-3">
    <div class="form-label" id="diveditor_style_label">{t}CSS style{/t}</div>
    <div id="diveditor_style" role="group" aria-labelledby="diveditor_style_label"></div>
  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="diveditor_modal" command="close">{t}Close{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-primary" id="diveditor_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>

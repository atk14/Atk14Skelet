{*
 * Modal for inserting a [spacer] shortcode or editing its height, see public/admin/scripts/utils/spacer_editor.js
 *}
<script>
  window.spacereditorTexts = {
    title: "{t escape=javascript}Spacer{/t}",
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}"
  };
</script>

{* fields of a [spacer] in the inspector of the block view (see SpacerEditor.inspect()) *}
<template id="spacereditor_inspector">
  <label class="form-label js--height">{t}Height{/t}</label>
  <div class="spacer-height js--height-fields">
    <input type="range" class="custom-range js--value" min="0" step="1">
    <output class="spacer-height__value js--output"></output>
    <select class="custom-select custom-select-sm js--unit" aria-label="{t}Unit{/t}">
      <option value="px">px</option>
      <option value="em">em</option>
      <option value="rem">rem</option>
    </select>
  </div>
</template>

<dialog class="dialog dialog-sm" id="spacereditor_modal" aria-labelledby="spacereditor_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="spacereditor_label">{t}Spacer{/t}</h1>
    <button type="button" class="btn-close" commandfor="spacereditor_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body">
    <p class="text-muted small">{t}A vertical space between blocks.{/t}</p>
    <label class="form-label" for="spacereditor_value">{t}Height{/t}</label>
    <div class="spacer-height js--height-fields">
      <input type="range" class="custom-range js--value" id="spacereditor_value" min="0" step="1" autofocus>
      <output class="spacer-height__value js--output" for="spacereditor_value"></output>
      <select class="custom-select js--unit" id="spacereditor_unit" aria-label="{t}Unit{/t}">
        <option value="px">px</option>
        <option value="em">em</option>
        <option value="rem">rem</option>
      </select>
    </div>
  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="spacereditor_modal" command="close">{t}Close{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-primary" id="spacereditor_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>

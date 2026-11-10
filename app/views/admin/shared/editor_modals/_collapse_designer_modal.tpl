{*
 * Modal for inserting a [collapse] shortcode or editing its settings, see public/admin/scripts/utils/collapse_designer.js
 *}
<script>
  window.collapsedesignerTexts = {
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
    defaultTitle: "{t escape=javascript}Read more{/t}"
  };
</script>

{* fields of a [collapse], in the dialog and in the inspector of the block view (see UTILS.CollapseFields) *}
<template id="collapsedesigner_fields">
  <label class="form-label js--title-label">{t}Title{/t}</label>
  <input type="text" class="form-control mb-2 js--title" autocomplete="off">
  {* appearance of the title: the value are classes set by the option (classes btn and btn-* are replaced by them) *}
  <label class="form-label js--appearance-label">{t}Appearance of the title{/t}</label>
  <select class="form-control mb-2 js--appearance">
    <option value="">{t}Link{/t}</option>
    <option value="btn btn-primary">{t}Button{/t}</option>
    <option value="btn btn-outline-primary">{t}Outline button{/t}</option>
    <option value="btn btn-secondary">{t}Secondary button{/t}</option>
    <option value="btn btn-link">{t}Button styled as a link{/t}</option>
    <option value="custom" hidden>{t}By the classes below{/t}</option>
  </select>
  <div class="form-label js--title-class-label">{t}CSS class of the title{/t}</div>
  <div class="mb-2 js--title-class" role="group"></div>
  <div class="form-label js--content-class-label">{t}CSS class of the content{/t}</div>
  <div class="mb-2 js--content-class" role="group"></div>
  <div class="form-label js--class-label">{t}CSS class{/t}</div>
  <div class="mb-2 js--class" role="group"></div>
  <div class="form-label js--style-label">{t}CSS style{/t}</div>
  <div class="js--style" role="group"></div>
</template>

<dialog class="dialog" id="collapsedesigner_modal" aria-labelledby="collapsedesigner_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="collapsedesigner_label">{t}Collapse{/t}</h1>
    <button type="button" class="btn-close" commandfor="collapsedesigner_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body" id="collapsedesigner">
    <p class="text-muted small">{t}A title showing hidden content on click. The content is edited directly in the block view.{/t}</p>
    {* the fields from #collapsedesigner_fields *}
  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="collapsedesigner_modal" command="close">{t}Close{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-primary" id="collapsedesigner_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>

{*
 * Modal for inserting or editing a Custom CSS block (a <style> element), see public/admin/scripts/utils/css_designer.js
 *
 * The breakpoints come from MARKDOWN_CSS_BREAKPOINTS (config/settings.php), the same as the web's grid has.
 *}
<script>
  window.cssdesignerConfig = {
    breakpoints: [{foreach $smarty.const.MARKDOWN_CSS_BREAKPOINTS as $name => $min} { name: "{$name|escape:"javascript"}", min: {$min+0} }{if !$min@last},{/if}{/foreach} ]
  };
  window.cssdesignerTexts = {
    title: "{t escape=javascript}Custom CSS{/t}",
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
    allWidths: "{t escape=javascript}All widths{/t}",
    allWidthsTitle: "{t escape=javascript}Rules for all widths of the screen{/t}",
    breakpointTitle: "{t escape=javascript}Rules for screens at least %min% px wide{/t}",
    notSplittable: "{t escape=javascript}The CSS can't be split into the tabs: rules for all widths must come first, then @media (min-width: …) of the breakpoints in ascending order, each one once.{/t}"
  };
</script>

{* fields of a Custom CSS block, in the dialog and in the inspector of the block view (see UTILS.CssFields) *}
<template id="cssdesigner_fields">
  <div class="css-designer">
    <div class="css-designer__tabs js--tabs" role="tablist" aria-label="{t}Screen width{/t}"></div>
    <div class="css-designer__editor js--editor"></div>
    <div class="form-check mt-2">
      <input type="checkbox" class="form-check-input js--whole">
      <label class="form-check-label js--whole-label">{t}Whole code{/t}</label>
    </div>
    <p class="form-text text-warning small mb-0 js--note" hidden></p>
  </div>
</template>

<dialog class="dialog dialog-lg" id="cssdesigner_modal" aria-labelledby="cssdesigner_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="cssdesigner_label">{t}Custom CSS{/t}</h1>
    <button type="button" class="btn-close" commandfor="cssdesigner_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body" id="cssdesigner">
    <p class="text-muted small">{t}CSS rules applied to the page where the content is shown. Rules in the tab of a breakpoint apply to screens at least as wide as it. In the editor's preview they apply to the preview only.{/t}</p>
    {* the fields from #cssdesigner_fields *}
  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="cssdesigner_modal" command="close">{t}Close{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-primary" id="cssdesigner_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>

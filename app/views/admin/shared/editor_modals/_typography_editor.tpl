{*
 * Typography of a block in the inspector of the block view (text alignment, font weight, italics),
 * see public/admin/scripts/utils/typography_editor.js
 *
 * The classes written by the panel: a project with other utility classes (e.g. Bootstrap 5:
 * text-start, text-end, fw-light, fw-normal, fw-bold, fst-italic) overrides this template;
 * a choice without a class (left out of a map) isn't offered.
 *}
<script>
  window.typographyEditorConfig = {
    align: { left: "text-left", center: "text-center", right: "text-right", justify: "text-justify" },
    weight: { light: "font-weight-light", normal: "font-weight-normal", bold: "font-weight-bold" },
    italic: "font-italic"
  };
</script>

<template id="typography_editor">
  <div class="md-inspector__section typography-editor">
    <div class="md-inspector__section-title">{t}Typography{/t}</div>
    <div class="form-label js--align-label">{t}Text alignment{/t}</div>
    <div class="btn-group btn-group-sm mb-2 js--align" role="group">
      <button type="button" class="btn btn-outline-secondary" data-align="left" title="{t}Align left{/t}" aria-label="{t}Align left{/t}" aria-pressed="false"><span class="fa-solid fa-align-left" aria-hidden="true"></span></button>
      <button type="button" class="btn btn-outline-secondary" data-align="center" title="{t}Center{/t}" aria-label="{t}Center{/t}" aria-pressed="false"><span class="fa-solid fa-align-center" aria-hidden="true"></span></button>
      <button type="button" class="btn btn-outline-secondary" data-align="right" title="{t}Align right{/t}" aria-label="{t}Align right{/t}" aria-pressed="false"><span class="fa-solid fa-align-right" aria-hidden="true"></span></button>
      <button type="button" class="btn btn-outline-secondary" data-align="justify" title="{t}Justify{/t}" aria-label="{t}Justify{/t}" aria-pressed="false"><span class="fa-solid fa-align-justify" aria-hidden="true"></span></button>
    </div>
    <div class="form-label js--weight-label">{t}Font{/t}</div>
    <div class="typography-editor__font js--weight" role="group">
      <select class="custom-select custom-select-sm js--weight-select" aria-label="{t}Font weight{/t}">
        <option value="">{t}Inherited weight{/t}</option>
        <option value="light">{t}Light{/t}</option>
        <option value="normal">{t}Normal{/t}</option>
        <option value="bold">{t}Bold{/t}</option>
      </select>
      <button type="button" class="btn btn-sm btn-outline-secondary js--italic" title="{t}Italic{/t}" aria-label="{t}Italic{/t}" aria-pressed="false"><span class="fa-solid fa-italic" aria-hidden="true"></span></button>
    </div>
  </div>
</template>

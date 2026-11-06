<template id="fa_iconpicker_thumbnail">
  <div class="card card--iconthumbnail">
    <div class="card-img-top"></div>
    <div class="card-body text-center mt-0 pt-0">
      <div class="icon_name"></div>
      <div class="d-none">
        <span class="icon_html_code"></span>
        {if $DEVELOPMENT}
        <span class="icon_smarty_code"></span>
        {/if}
      </div>
      <div class="d-flex" style="gap: 5px; justify-content: center; flex-direction: column; xflex-wrap: wrap;">
        <button type="button" class="btn btn-primary btn-sm js--copy-html-btn">{t}Copy HTML code{/t}</button>
        {if $DEVELOPMENT}
        <button type="button" class="btn btn-outline-secondary btn-sm js--copy-smarty-btn">{t}Copy Smarty{/t}</button>
        {/if}
      </div>
    </div>
  </div>
</template>

<script>
  window.faIconpickerTexts = {
    copied: "{t}Copied!{/t}",
    icons: "{t}Icons{/t}",
  }
</script>

<dialog class="dialog dialog-lg" id="fa_iconpicker_modal" aria-labelledby="fa_iconpicker_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="fa_iconpicker_label">{t}Icon Picker{/t}</h1>
    <button type="button" class="btn-close" commandfor="fa_iconpicker_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body fa-iconpicker" id="fa-iconpicker">
    
    <div class="form-group row mb-3">
      <label for="fa_iconpicker_search_input" class="form-label col-4">{t}Search icons{/t}</label>
      <div class="col-8">
        <input type="text" class="form-control" id="fa_iconpicker_search_input" autofocus placeholder="{t}Search icons...{/t}">
      </div>
    </div>

    <div class="fa-iconpicker__thumbnails">
    </div>

  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="fa_iconpicker_modal" command="close">{t}Close{/t}</button>
    </div>
  </div>
</dialog>

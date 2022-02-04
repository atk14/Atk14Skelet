<!-- Modal -->
<div class="modal{if $animation} fade{/if}" id="{$id}" tabindex="-1" role="dialog" aria-labelledby="{$id}Label" aria-hidden="true">
  <div class="modal-dialog{if $vertically_centered} modal-dialog-centered{/if}" role="document">
    <div class="modal-content">
			{if $title|strlen || $close_button}
			<div class="modal-header">
				{if $title|strlen}<h5 class="modal-title" id="{$id}Label">{$title}</h5>{/if}
				{if $close_button}
					<button type="button" class="close" data-dismiss="modal" aria-label="{t}zavřít{/t}">
						<span aria-hidden="true">&times;</span>
					</button>
				{/if}
			</div>
			{/if}

      <div class="modal-body">
        {!$content}
      </div>

			{if $footer}
      <div class="modal-footer">
				{!$footer}
      </div>
			{/if}
    </div>
  </div>
</div>

{if $open_on_load}
	{content for=js}
		$( window ).on("load", function() {
			$("#{$id}").modal({
				show: true,
				backdrop: true
			});
		});
	{/content}
{/if}

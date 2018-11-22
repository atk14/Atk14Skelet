<div class="btn-group btn-group-sm{if $pull=="right"} pull-right{/if}{if $pull=="left"} pull-left{/if}{if $class} {$class}{/if}">
	{!$first_line}
	{if $lines}
		<button class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">{t}Show menu{/t}</span>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			{foreach $lines as $line}
				{!$line}
			{/foreach}
		</div>
	{/if}
</div>

{if $clearfix}
<div class="clearfix"></div>
{/if}

{*

<div class="btn-group{if $pull=="right"} pull-right{/if}{if $pull=="left"} pull-left{/if}{if $class} {$class}{/if}">
  {!$first_line}
  <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="sr-only">Toggle Dropdown</span>
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="#">Action</a>
    <a class="dropdown-item" href="#">Another action</a>
    <a class="dropdown-item" href="#">Something else here</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="#">Separated link</a>
  </div>
</div>

*}
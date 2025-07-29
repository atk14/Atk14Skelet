{if $USING_BOOTSTRAP4 || $USING_BOOTSTRAP5}

	{if $link_on_first_line}

		<div class="btn-group btn-group-sm{if $pull=="right"} float-right{/if}{if $pull=="left"} float-left{/if}{if $class} {$class}{/if}">
			{!$first_line}
			{if $lines}
				<button class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">{t}Show menu{/t}</span>
				</button>
				<div class="dropdown-menu dropdown-menu-right dropdown-menu-end">
					{foreach $lines as $line}
						{!$line}
					{/foreach}
				</div>
			{/if}
		</div>

	{else}

		<div class="btn-group btn-group-sm{if $pull=="right"} float-right{/if}{if $pull=="left"} float-left{/if}{if $class} {$class}{/if}">
			<button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				{!$first_line}
			</button>
			{if $lines}
				<div class="dropdown-menu dropdown-menu-right">
					{foreach $lines as $line}
						{!$line}
					{/foreach}
				</div>
			{/if}
		</div>

	{/if}

{else}

	{* BOOTSTRAP3 *}

	{if $link_on_first_line}

		<div class="btn-group btn-group-sm{if $pull=="right"} pull-right{/if}{if $pull=="left"} pull-left{/if}{if $class} {$class}{/if}">
			{!$first_line}
			{if $lines}
				<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
					<span class="caret"></span>
					<span class="sr-only">{t}Show menu{/t}</span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					{foreach $lines as $line}
						<li>{!$line}</li>
					{/foreach}
				</ul>
			{/if}
		</div>

	{else}

		<div class="btn-group{if $pull=="right"} pull-right{/if}{if $pull=="left"} pull-left{/if}{if $class} {$class}{/if}">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				{!$first_line} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				{foreach $lines as $line}
					<li>{!$line}</li>
				{/foreach}
			</ul>
		</div>

	{/if}

{/if}

{if $clearfix}
<div class="clearfix"></div>
{/if}

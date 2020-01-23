{*<div style="position: relative; height: 0px; width: 100%;">
	<div style="position: absolute; width: 100%; top: -35px;">
		<div class="admin-object-menu {$class}">
			{dropdown_menu clearfix=false}
				{render partial=$subtemplate}
			{/dropdown_menu}
		</div>
	</div>
</div>*}
<div class="admin-object-menu">
	<div class="admin-object-menu__wrapper">
		<div class="{$class}">
			{dropdown_menu clearfix=false}
				{render partial=$subtemplate}
			{/dropdown_menu}
		</div>
	</div>
</div>

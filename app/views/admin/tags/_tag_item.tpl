<tr>
	<td>{$tag->getId()}</td>
	<td>{$tag}</td>
	<td>
		<div class="btn-group">
			<button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="glyphicon glyphicon-cog"></i>
			</button>
			<ul class="dropdown-menu pull-right">
				<li>{a action=edit id=$tag}<i class="glyphicon glyphicon-edit"></i> {t}Edit{/t}{/a}</li>
			</ul>
		</div>
	</td>
</tr>

<?= print_error(val($error)) ?>

<? if (empty($items)): ?>
<p>
	No items in list.
</p>
<? else: ?>
<table class="list">
	<thead>
		<tr>
			<td>
				ID
			</td>	
			<td>
				Name
			</td>	
			<td>
				Value
			</td>	
			<td>
				Type
			</td>	
			<td>
				Start date
			</td>	
			<td>
				End date
			</td>	
			<td>
				Action
			</td>	
		</tr>
	</thead>
	<tbody>
		<? foreach ($items as $item): ?>
		<tr class="<?= cycle('odd', 'even') ?>">
			<td>
				<?= $item->id ?>
			</td>	
			<td>
				<a href="/ajax/item/<?=$item->id?>/edit" class="edit-link">
					<?= $item->name ?>
				</a>
			</td>	
			<td>
				<?= $item->value ?>
			</td>	
			<td>
				<?= $item->type ?>
			</td>	
			<td>
				<?= $item->startDate ?>
			</td>	
			<td>
				<?= $item->endDate ?>
			</td>	
			<td>
				<a href="/ajax/item/<?=$item->id?>/remove" class="remove-link">
					Remove
				</a>
			</td>	
		</tr>
		<? endforeach; ?>
	</tbody>
</table>
<? endif ?>
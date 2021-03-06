<div class="toolbox">
	<a href="/">Back to list</a>
</div>
<h1>Item Form</h1>

<p>

</p>
<?= print_error('_form', $errors); ?>

<form method="post">

<table class="form form-normal">
	<tr>
		<td class="form-label">
			Name
		</td>
		<td>
			<input class="input input-medium" type="text" name="name" value="<?=$item->name?>" />
			<?= print_error('name', $errors); ?>
		</td>
	</tr>
	<tr>
		<td class="form-label">
			Value
		</td>
		<td>
			<input class="input input-medium" type="text" name="value" value="<?=$item->value ?>" />
			<?= print_error('value', $errors); ?>
		</td>
	</tr>
	<tr>
		<td class="form-label">
			Type
		</td>
		<td>
			<select class="input input-medium" name="typeId">
				<?= select_options($types, $item->typeId) ?>
			</select>
			<?= print_error('typeId', $errors); ?>
		</td>
	</tr>
	<tr>
		<td class="form-label">
			Start date
		</td>
		<td>
			<input class="input" type="text" name="startDate" value="<?=$item->startDate ?>" />
			<?= print_error('startDate', $errors); ?>
		</td>
	</tr>
	<tr>
		<td class="form-label">
			End date
		</td>
		<td>
			<input class="input" type="text" name="endDate" value="<?=$item->endDate ?>" />
			<?= print_error('endDate', $errors); ?>
		</td>
	</tr>
	<tr class="submit-row">
		<td>
			<?= print_error('id', $errors); ?>
			<input type="hidden" name="id" value="<?=$item->id?>" />
		</td>
		<td>
			<input class="btn-primary" type="submit" value="Save" />
		</td>
	</tr>
</table>
</form>
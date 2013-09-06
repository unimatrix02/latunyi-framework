<h1>Item Form</h1>

<p>
	<a href="/">Back to list</a>
</p>
<?= print_error('_form', $errors); ?>

<form method="post">
<table class="form">
	<tr>
		<td>
			Name
		</td>
		<td>
			<input type="text" name="name" value="<?=$item->name?>" />
			<?= print_error('name', $errors); ?>
		</td>
	</tr>
	<tr>
		<td>
			Value
		</td>
		<td>
			<input type="text" name="value" value="<?=$item->value ?>" />
			<?= print_error('value', $errors); ?>
		</td>
	</tr>
	<tr>
		<td>
			Type
		</td>
		<td>
			<select name="typeId">
				<?= select_options($types, $item->typeId) ?>
			</select>
			<?= print_error('typeId', $errors); ?>
		</td>
	</tr>
	<tr>
		<td>
			Start date
		</td>
		<td>
			<input type="text" name="startDate" value="<?=$item->startDate ?>" />
			<?= print_error('startDate', $errors); ?>
		</td>
	</tr>
	<tr>
		<td>
			End date
		</td>
		<td>
			<input type="text" name="endDate" value="<?=$item->endDate ?>" />
			<?= print_error('endDate', $errors); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= print_error('id', $errors); ?>
			<input type="hidden" name="id" value="<?=$item->id?>" />
		</td>
		<td>
			<input type="submit" value="Save" />
		</td>
	</tr>
</table>
</form>
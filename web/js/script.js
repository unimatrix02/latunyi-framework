/**
 * After init, load list for the first item
 */
$(document).ready(function() 
{
	loadList();
	
	$('.add-item').click(function() 
	{
		$.get('/ajax/item/0/add', function(resp) 
		{
			$('.form-container').html(resp).removeClass('hidden').show();
			handleFormSubmit();
		});
		return false;
	});
});

/**
 * Loads the item list from the server.
 */
function loadList() 
{
	$.get('/list/items', function(resp) 
	{
		$('.items').html(resp);
		
		$('a.edit-link').click(function()
		{
			event.preventDefault();
			var url = $(this).attr('href');
			$.get(url, function(resp) 
			{
				$('.form-container').html(resp).removeClass('hidden').show();
				handleFormSubmit();
			});
		});

		$('a.remove-link').click(function()
				{
			event.preventDefault();
			var url = $(this).attr('href');
			$.get(url, function(resp) 
			{
				if (resp == 'OK')
				{
					loadList();
				}
				else
				{
					alert(resp);
				}
			});
		});
	});
}

function handleFormSubmit() 
{
	// Send data to server for validation
	$('.item-form').submit(function() 
	{
		event.preventDefault();
		// Gather form data
		var formData = $(this).serialize();
		var itemId = $('input[name="id"]').val();
		
		$.post('/ajax/item/' + itemId + '/save', formData, function(resp) 
		{
			if (resp == 'OK') 
			{
				// Close form, reload list
				$('.form-container').hide();
				loadList();
			}
			else
			{
				// Show returned HTML, add event handler
				$('.form-container').html(resp);
				handleFormSubmit();
			}
		});
	});
		
}
function log(s) {
	console.log(s);
}

/**
 * After init, load list for the first item
 */
$(document).ready(function() 
{
	loadList();

	$('.add-item').fancybox({
		type: 'ajax',
		openEffect: 'none',
		closeEffect: 'none',
		openSpeed: 0,
		closeSpeed: 0,
		afterShow: handleFormSubmit,
		closeBtn: false
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

		$('a.edit-link').fancybox({
			type: 'ajax',
			openEffect: 'none',
			closeEffect: 'none',
			openSpeed: 0,
			closeSpeed: 0,
			afterShow: handleFormSubmit,
			closeBtn: false
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
	$('.item-form :input:first:visible').focus();

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
				$.fancybox.close();
				loadList();
			}
			else
			{
				// Show returned HTML, add event handler
				//$('.form-container').html(resp);
				$('.fancybox-inner').html(resp);
				handleFormSubmit();
			}
		});

		return false;
	});
}
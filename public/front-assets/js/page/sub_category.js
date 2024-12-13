/*
Author       : Dreamstechnologies
Template Name: SmartHR - Bootstrap Admin Template
Version      : 4.0
*/

$(document).ready(function() {
	
	$(document).on('click','.save-product-group', function(){
		let category_id = $('#category_id').val().trim();
		let sub_category_name = $('#sub_category_name').val().trim();
		//let createdDate = $('#created_date').val().trim();
		let isValid = true;
		$('.invalid-feedback').hide();
		$('.form-control').removeClass('is-invalid');
		if(category_id === '')
		{
			$('#category_id').addClass('is-invalid');
			$('.category_id_err').show();
			isValid = false;
		}
		
		// Validate product_group
		if (sub_category_name === '') {
			$('#sub_category_name').addClass('is-invalid');
			$('#sub_category_name').next('.invalid-feedback').show();
			isValid = false;
		}
		
		if (isValid) {
			var form = $("#frmproductgroup");
			var URL = $('#frmproductgroup').attr('action');
			//alert(URL);
			$.ajax({
				url: URL,
				type: "POST",
				data: form.serialize() + '&_token=' + csrfToken,
				//dataType: 'json',
				success: function(response) {
					if (!response.success) {
						$('#sub_category_name').addClass('is-invalid');
						$('#sub_category_name').next('.invalid-feedback').text(response.message).show();
					} else {
						$('#success_msg').modal('show');
						setTimeout(() => {
							window.location.reload();
						}, "2000");
					}
				},
			});
		}
	});
	


$(document).on('click','.edit-product-group', function(){
	var id = $(this).data('id');
	var URL = $(this).data('url');
	//alert(URL);
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, _token: csrfToken},
		dataType: 'json',
		success: function(response) {
			$('#id').val(response.id);
			$('#edit_category_id').val(String(response.category_id)).trigger('change');
			$('#edit_sub_category_name').val(response.sub_category_name);
			$('#edit_product_group').modal('show');
			//alert(JSON.stringify(response));
			
		},
	});
}); 

$(document).on('click','.update-product-group-form', function(){
	
	let edit_category_id = $('#edit_category_id').val().trim();
	let edit_sub_category_name = $('#edit_sub_category_name').val().trim();
	//let createdDate = $('#created_date').val().trim();
	let isValid = true;
	$('.invalid-feedback').hide();
	$('.form-control').removeClass('is-invalid');
	if (edit_category_id === '') 
	{
		$('#edit_category_id').addClass('is-invalid');
		$('.edit_category_id_err').show();
		isValid = false;
	}
	// Validate product_group
	if (edit_sub_category_name === '') {
		$('#edit_sub_category_name').addClass('is-invalid');
		$('#edit_sub_category_name').next('.invalid-feedback').show();
		isValid = false;
	}
	
	if (isValid) {
		var form = $("#frmeditproductgroup");
		var URL = $('#frmeditproductgroup').attr('action');
		$.ajax({
			url: URL,
			type: "POST",
			data: form.serialize() + '&_token=' + csrfToken,
			//dataType: 'json',
			success: function(response) {
				if (!response.success) {
					$('#edit_sub_category_name').addClass('is-invalid');
					$('#edit_sub_category_name').next('.invalid-feedback').text(response.message).show();
				}
				else{
					$('#updt_success_msg').modal('show');
					setTimeout(() => {
						window.location.reload();
					}, "2000");
				}
			},
		});
	}
});



$(document).on('click','.delete-product-group', function(){
	var id = $(this).data('id');
	var URL = $(this).data('url');
	//alert(id);alert(URL);
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, _token: csrfToken},
		dataType: 'json',
		success: function(response) {
			//alert(response);
			//var url = "{{ route('deleteContactList') }}";
			$('.data-id-pcode-list').attr('data-id', id);
			$('#list_code_name').html(response);
			$('#delete_product_group').modal('show');
		},
	});
	
});
$(document).on('click','.data-id-pcode-list', function(){
	var id = $(this).data('id');
	var URL = $(this).data('url');
	//alert(URL);
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, _token: csrfToken},
		dataType: 'json',
		success: function(response) {
			if(response.result == 'success'){
				$('#delete-prospect-msg').html('<font color="green">Record Deleted Successfully</font>');
			}else{
				$('#data_already_use').modal('show');
			}
			setTimeout(() => {
				window.location.reload();
			}, "2000");
		},
	});
	
});
$(document).on('click','.update-status', function(){
	var id= $(this).data('id');
	var URL = $(this).data('url');
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, _token: csrfToken},
		dataType: 'json',
		success: function(response) {
			//alert(response);
			setTimeout(() => {
				window.location.reload();
			}, "1000");
		},
	});
});

$(document).on('click','.search-data', function(){
	$('#search-product-group-frm').submit();
	
});
$('.search-sort-by').on('change' ,function (event) {
	//var sort_by = $(this).val();
	$('#search-sortby').submit();
})


/*$(document).on('click','.contact-details', function(){
	var URL = $(this).data('url');
	window.location = URL;
});*/
$(document).on('click', '.dropdown-toggle, .dropdown-menu, .dropdown-item', function(event) {
    event.stopPropagation(); 
});


});

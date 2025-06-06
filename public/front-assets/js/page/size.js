/*
Author       : Dreamstechnologies
Template Name: SmartHR - Bootstrap Admin Template
Version      : 4.0
*/

$(document).ready(function() {
	
	$(document).on('click','.save-size', function(){
		let size = $('#size').val().trim();
		//let createdDate = $('#created_date').val().trim();
		let isValid = true;
		$('.invalid-feedback').hide();
		$('.form-control').removeClass('is-invalid');
		if (size === '')
		{
			$('#size').addClass('is-invalid');
			$('#size').next('.invalid-feedback').show();
			isValid = false;
		}
		if (isValid) {
			var form = $("#frmproductcode");
			var URL = $('#frmproductcode').attr('action');
			//alert(URL);
			$.ajax({
				url: URL,
				type: "POST",
				data: form.serialize() + '&_token=' + csrfToken,
				//dataType: 'json',
				success: function(response) {
					if (!response.success) {
						$('#size').addClass('is-invalid');
						$('#size').next('.invalid-feedback').text(response.message).show();
					} else {
						$('#success_msg').modal('show');
						setTimeout(() => {
							window.location.reload();
						}, "2000");
					}
				},
				error: function (xhr) {
					if (xhr.status === 422) {
						const errors = xhr.responseJSON.errors;
						$('.invalid-feedback').hide();
						$('.form-control').removeClass('is-invalid');

						// Loop through errors and apply them to the respective fields
						$.each(errors, function(key, value) {
							if ($('#'+key).length) { // Which have id
								let field = $('#' + key.replace('.', '\\.'));
								field.addClass('is-invalid');
								field.next('.invalid-feedback').show().text(value[0]);
							}else{ // Which have not any id
								var fieldName = key.split('.')[0]; // Get the base field name (e.g., product_sale_price)
								var index = key.split('.').pop();
								var inputField = $('input[name="' + fieldName + '[]"]').eq(index);
								inputField.addClass('is-invalid');
								inputField.next('.invalid-feedback').show().text(value[0]);
							}	
						});
						
					}else {
						alert('An unexpected error occurred.');
					}
				}
			});
		}
	});
	


$(document).on('click','.edit-size', function(){
	var id = $(this).data('id');
	var URL = $(this).data('url');
	//alert(URL);
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, _token: csrfToken},
		dataType: 'json',
		success: function(response) {
			//alert(response.state);
			$('#id').val(response.id);
			$('#size').val(response.size);
			$('#height').val(response.height);
			$('#width').val(response.width);
			$('#add_size').modal('show');
			//alert(JSON.stringify(response));
			
		},
	});
}); 

$(document).on("click",'.removeImage', function (event) {
	var id = $(this).data('id');
	var imagename = $(this).data('imagename');
	var size_id = $(this).data('size');
	var media_source_id = 1;
	//alert(imagename);
	var URL = $("#del-media-image").val();
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, size_id:size_id, image_name:imagename,media_source_id:media_source_id, _token: csrfToken},
		dataType: 'json',
		success: function(response) {  
			//alert(JSON.stringify(response));var galleriesHtml = '';  size_remain
			var app_url =  response.app_url;
			var galleriesHtml = ''; 
			var medianames = response.medias;
            //alert(vehicle_gallaries);
			$('#text_show').html(response.size_remain);
			
			if (Dropzone.instances.length > 0) {
					// Assuming the first Dropzone instance is the one you want to update
					var dropzoneInstance = Dropzone.instances[0];
					dropzoneInstance.options.maxFiles = response.size_remain;
				} else {
					initializeDropzone(response.size_remain);
				}
			
			
			$.each(medianames, function (index, gallery) {
				
				if (index % 4 === 0) {
					galleriesHtml += '<div class="row mb-3">'; 
				}
				
				galleriesHtml += `
						<div class="col-md-3 position-relative">
							<!-- Cross Icon to delete image -->
							<div class="position-absolute top-0 end-0 m-2">
								<button type="button" 
										data-img="${gallery.image}" 
										data-id="${gallery.id}" data-size="${gallery.media_source_id}" data-imagename="${gallery.image}"  
										class="btn removeImage bg-transparent">
									<i class="fa fa-times-circle custom_gallery_icon" style="font-size:20px;color:red"></i>
								</button>
							</div>

							<!-- Image -->
							<img src="${app_url}/uploads/size/${gallery.media_source_id}/gallery/thumbs/${gallery.image}" 
								 class="img-thumbnail" 
								 alt="Gallery Image">
						</div>
					`;
				if ((index % 4 === 3) || (index === medianames.length - 1)) {
					galleriesHtml += '</div>'; // Close the row
				}
			});
			//alert(galleriesHtml);
			$('#galleries_data').html(galleriesHtml);
			
		},
	});
});

$(document).on('click','.update-product-code-form', function(){
	
	let stageName = $('#edit_name').val().trim();
	//let createdDate = $('#created_date').val().trim();
	let isValid = true;
	$('.invalid-feedback').hide();
	$('.form-control').removeClass('is-invalid');if (stageName === '') 
	{
		$('#edit_name').addClass('is-invalid');
		$('#edit_name').next('.invalid-feedback').show();
		isValid = false;
	}
	if (isValid) {
		var form = $("#frmeditproductcode");
		var URL = $('#frmeditproductcode').attr('action');
		$.ajax({
			url: URL,
			type: "POST",
			data: form.serialize() + '&_token=' + csrfToken,
			//dataType: 'json',
			success: function(response) {
				if (!response.success) {
					$('#edit_name').addClass('is-invalid');
					$('#edit_name').next('.invalid-feedback').text(response.message).show();
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



$(document).on('click','.delete-size', function(){
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
			$('#list_name').html(response);
			$('#delete_product_code').modal('show');
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

$(document).on('click','.add_size', function(){
	$('#name').val('');
	$('#galleries_data').html('');
})

$(document).on('click','.search-data', function(){
	$('#search-product-code-frm').submit();
	
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

$('#exportForm').on('submit', function(e) {
	setTimeout(function() {
		$('#export').modal('hide');
	}, 2000);
});

$('#importForm').on('submit', function(e) {
	setTimeout(function() {
		$('#import').modal('hide');
	}, 2000);
});

$(document).on('click','.downloaddemo', function(){
	setTimeout(function() {
		$('#import').modal('hide');
	}, 1000);
});

});

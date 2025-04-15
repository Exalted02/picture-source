@extends('layouts.app')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
				<h4 class="page-title">{{ __('home_settings') }}</h4>
				<div class="row">
					<div class="col-md-12">
						<form action="{{ route('home-settings-save') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
							@csrf
							<div>
								<h3><span id="text_show">{{9 - count($images)}}</span>  {{ __('image_gallery_only') }},  {{ __('image_gallery_img_box') }}</h3>
							</div>
							
						</form>
					</div>
				</div>
				<div class="row">
						@foreach($images as $val)
						<div class="col-md-2 position-relative mt-1">
							<!-- Cross Icon to delete image -->
							<div class="position-absolute top-0 end-0 m-2">
								<button type="button" data-id="{{$val->id}}" data-imagename="{{$val->name}}" class="btn removeImage bg-transparent">
									<i class="fa fa-times-circle custom_gallery_icon" style="font-size:20px;color:red"></i>
								</button>
							</div>

							<!-- Image -->
							<img src="{{url('uploads/home').'/'.$val->name}}" class="img-thumbnail" alt="Gallery Image">
						</div>
						@endforeach
				</div>
				{{--<div class="submit-section">
					<button class="btn btn-primary save-update" type="button" >{{ __('save_and_update') }}</button>
				</div>--}}
            </div>
        </div>
    </div>
    <!-- /Page Content -->
</div>
	<!-- /Page Content -->
<!-- update Success message -->
<div class="modal custom-modal fade" id="success_msg" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon">
						<i class="la la-trash-alt"></i>
					</div>
					<h3>{{ __('data_deleted_successfully') }}!!!</h3>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection 
@section('scripts')
<script src="{{ url('front-assets/css/dropzone.css') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>

<script>
$(document).on("click",'.removeImage', function (event) {
	var id = $(this).data('id');
	var imagename = $(this).data('imagename');
	var URL = "{{route('delete-home-image')}}";
	$.ajax({
		url: URL,
		type: "POST",
		data: {id:id, image_name:imagename, _token: csrfToken},
		dataType: 'json',
		success: function(response) { 
			$('#success_msg').modal('show');
			setTimeout(function() {
				window.location.reload();
			}, 2000);
		}
	});
});
var remaining_upload = '{{9-count($images)}}';
Dropzone.options.imageUpload = {
	maxFiles: remaining_upload,
	acceptedFiles: ".jpeg,.jpg,.png,.gif",
	init: function() {
		this.on("maxfilesexceeded", function(file) {
			this.removeFile(file);
		});
	}
};
</script>

<script>
//var csrfToken = "{{ csrf_token() }}";
$( document ).ready(function() {
	$(document).on('click','.save-update', function(){
		let isValid = true;
		if (isValid) {
			var form = $("#frmEmailSettings");
			var URL = $('#frmEmailSettings').attr('action');
			$.ajax({
				url: URL,
				type: "POST",
				data: form.serialize() + '&_token=' + csrfToken,
				//dataType: 'json',
				success: function(response) {
					$('#updt_success_msg').modal('show');
					setTimeout(() => {
						window.location.reload();
					}, "2000");
				},
				error: function(xhr) {
					// Handle validation errors
					if (xhr.status === 422) {
						const errors = xhr.responseJSON.errors;
						let errorMessage = '';

						$('.invalid-feedback').hide();
						$('.form-control').removeClass('is-invalid');
						// Loop through errors and format them
						$.each(errors, function(key, value) {
							errorMessage += value[0] + '\n'; // Assuming you want the first error message for each field
							if ($('#'+key).length) { // Which have id
								$('#'+key).addClass('is-invalid');
								$('#'+key).next('.invalid-feedback').show().text(value[0]);
							}else{ // Which have not any id
								var fieldName = key.split('.')[0]; // Get the base field name (e.g., product_sale_price)
								var index = key.split('.').pop();
								var inputField = $('input[name="' + fieldName + '[]"]').eq(index);
								inputField.addClass('is-invalid');
								inputField.next('.invalid-feedback').show().text(value[0]);
							}
						});
						// Display the errors
						//alert('Validation errors:\n' + errorMessage);
					} else if(xhr.status == 403){
						$('#module_permission').modal('show');
						setTimeout(() => {
							window.location.reload();
						}, "2000");
					}else{
						alert('An unexpected error occurred.');
					}
				}
			});
		}
	});
});
</script>
@endsection

<div class="modal custom-modal fade" id="delete_product_group" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon bg-danger" id="delete-prospect-msg">
						<i class="la la-trash-restore"></i>
					</div>
					<h3>{{ __('are_you_sure') }}, {{ __('you_want_delete') }}</h3>
					<p>{{ __('sub_category') }} "<span id="list_code_name"></span>" {{ __('from_your_account') }}</p>
					<div class="col-lg-12 text-center form-wizard-button">
						<a href="#" class="button btn-lights" data-bs-dismiss="modal">{{ __('not_now') }}</a>
						<a href="javascript:void(0);" class="btn btn-primary data-id-pcode-list" data-url="{{ route('deleteSubcategoryList') }}">{{ __('okay') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Add product code -->
<div id="add_subcategory" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('add_new_sub_category') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="frmproductgroup" action="{{ route('user.save-subcategory') }}">
				<input type="hidden" id="id" name="id">
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('category') }}<span class="text-danger">*</span></label>
								<select name="category_id" class="select" id="category_id">
								<option value="">{{ __('please_select') }}</option>
								@foreach($categories as $val)
								<option value="{{ $val->id }}">{{ $val->name}}</option>
								@endforeach
								</select>
								<div class="invalid-feedback category_id_err">{{ __('please_select') }} {{ __('category')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('sub_category') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="sub_category_name" id="sub_category_name">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('sub_category')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							@php
								$unique_number = \Illuminate\Support\Str::random(10);
							@endphp
							<input type="hidden" name="unique_number" value="{{ $unique_number }}">
							<form></form>
							<form action="{{ route('subcategory.dropzone.store') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
								@csrf
								<input type="hidden" name="unique_number" value="{{ $unique_number }}">
								<div>
									<h3><span id="text_show">12</span>  {{ __('image_gallery_only') }},  {{ __('image_gallery_img_box') }}</h3>
								</div>
								
							</form>
						</div>
						
						<div class="col-md-12">
							<div id="galleries_data"></div>
						</div>
						<input type="hidden" id="del-media-image" value="{{ route('delete.subcategory.media')}}">
					</div>
					<div class="submit-section">
						<button class="btn btn-primary submit-btn save-product-group" type="button">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Add product code -->

<!--- edit product code -->
{{--<div id="edit_product_group" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('edit_sub_category') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="frmeditproductgroup" action="{{ route('user.save-subcategory') }}">
				<input type="hidden" id="id" name="id">
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('category') }}<span class="text-danger">*</span></label>
								<select name="category_id" class="select" id="edit_category_id">
								<option value="">{{ __('please_select') }}</option>
								@foreach($categories as $val)
								<option value="{{ $val->id }}">{{ $val->name}}</option>
								@endforeach
								</select>
								<div class="invalid-feedback edit_category_id_err">{{ __('please_select') }} {{ __('category')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('sub_category') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="sub_category_name" id="edit_sub_category_name">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('sub_category')}}.</div>
							</div>
						</div>
					</div>
					<div class="submit-section">
						<button class="btn btn-primary submit-btn update-product-group-form" type="button">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>--}}
<!-- /Edit Contact -->

<!-- Success Contact -->
<div class="modal custom-modal fade" id="success_msg" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon">
						<i class="la la-plus"></i>
					</div>
					<h3>{{ __('data_created_successfully') }}!!!</h3>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Success Contact -->
<div class="modal custom-modal fade" id="data_already_use" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon bg-danger">
						<i class="la la-times-circle"></i>
					</div>
					<h3>{{ __('data_already_use') }}!!!</h3>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Success Contact -->
<!-- update Success message -->
<div class="modal custom-modal fade" id="updt_success_msg" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon">
						<i class="la la-user-shield"></i>
					</div>
					<h3>{{ __('data_updated_successfully') }}!!!</h3>
				</div>
			</div>
		</div>
	</div>
</div>

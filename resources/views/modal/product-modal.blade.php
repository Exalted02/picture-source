<div class="modal custom-modal fade" id="delete_product_code" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon bg-danger" id="delete-prospect-msg">
						<i class="la la-trash-restore"></i>
					</div>
					<h3>{{ __('are_you_sure') }}, {{ __('you_want_delete') }}</h3>
					<p>{{ __('product') }} "<span id="list_name"></span>" {{ __('from_your_account') }}</p>
					<div class="col-lg-12 text-center form-wizard-button">
						<a href="#" class="button btn-lights" data-bs-dismiss="modal">{{ __('not_now') }}</a>
						<a href="javascript:void(0);" class="btn btn-primary data-id-pcode-list" data-url="{{ route('deleteArtistList') }}">{{ __('okay') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Add product code -->
<div id="add_product" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('add_new_product') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="frmproductcode" action="{{ route('user.save-product') }}">
				<input type="hidden" id="id" name="id">
				<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('category') }}<span class="text-danger">*</span></label>
								<select class="select form-control" name="category" id="category" data-url="{{ route('get-subcategory')}}" required>
									<option value="">{{ __('please_select') }}</option>
									@foreach($categories as $val)
										<option value="{{ $val->id }}">{{ $val->name ?? ''}}</option>
									@endforeach
								</select>
								<div class="invalid-feedback">{{ __('please_select') }} {{ __('category')}}.</div>
							</div>
						</div>
					
					{{--<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('sub_category') }}</label>
								<select class="select form-control" name="subcategory" id="subcategory">
									<option value="">{{ __('please_select') }}</option>
									
								</select>
							</div>
						</div>--}}
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('artist') }}<span class="text-danger">*</span></label>
								<select class="select form-control" name="artist_id" id="artist_id" required>
									<option value="">{{ __('please_select') }}</option>
									@foreach($artists as $val)
										<option value="{{ $val->id }}">{{ $val->name ?? ''}}</option>
									@endforeach
								</select>
								<div class="invalid-feedback">{{ __('please_select') }} {{ __('artist')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						{{--<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('size') }}<span class="text-danger">*</span></label>
								<select class="select form-control" name="size" id="size" data-url="{{ route('get-subcategory')}}" required>
									<option value="">{{ __('please_select') }}</option>
									@foreach($sizes as $val)
										<option value="{{ $val->id }}">{{ $val->size ?? ''}}</option>
									@endforeach
								</select>
								<div class="invalid-feedback">{{ __('please_select') }} {{ __('size')}}.</div>
							</div>
						</div>--}}
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('orientation') }}<span class="text-danger">*</span></label>
								<select class="select form-control" name="orientation" id="orientation" required>
									<option value="">{{ __('please_select') }}</option>
									@foreach(get_product_orientation() as $o_id=>$val)
										<option value="{{ $o_id }}">{{ $val ?? ''}}</option>
									@endforeach
								</select>
								<div class="invalid-feedback">{{ __('please_select') }} {{ __('orientation')}}.</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">Length (inch)<span class="text-danger">*</span></label>
								<input class="form-control number-only" type="text" name="length" id="length" required>
								<div class="invalid-feedback">{{ __('please_enter') }} Length.</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">Width (inch)<span class="text-danger">*</span></label>
								<input class="form-control number-only" type="text" name="width" id="width" required>
								<div class="invalid-feedback">{{ __('please_enter') }} Width.</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">Depth (inch)<span class="text-danger">*</span></label>
								<input class="form-control number-only" type="text" name="depth" id="depth" required>
								<div class="invalid-feedback">{{ __('please_enter') }} Depth.</div>
							</div>
						</div>
					
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('color') }}<span class="text-danger">*</span></label>
								<select class="select form-control" name="color" id="color" required>
									<option value="">{{ __('please_select') }}</option>
									@foreach($colors as $val)
										<option value="{{ $val->id }}">{{ $val->color ?? ''}}</option>
									@endforeach
								</select>
								<div class="invalid-feedback">{{ __('please_select') }} {{ __('color')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('product_code') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="product_code" id="product_code" required>
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('product')}}.</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('product_name') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="name" id="name" required>
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('name')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('price') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="price" id="price" required>
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('price')}}.</div>
							</div>
						</div>
					</div>
					
					
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('product_desc') }}</label>
								<input type="text" class="form-control" name="description" id="description">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('description')}}.</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('product_moulding_description') }}<span class="text-danger">*</span></label>
								<textarea class="form-control summernote" type="text" name="moulding_description" id="moulding_description" cols="150" required></textarea>
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('moulding')}}.</div>
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
							<form action="{{ route('product.dropzone.store') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
								@csrf
								<input type="hidden" name="unique_number" value="{{ $unique_number }}">
								<div>
									<h3><span id="text_show">12</span>  {{ __('image_gallery_only') }},  {{ __('image_gallery_img_box') }}</h3>
								</div>
								
							</form>
						</div>
					</div>
					<div class="col-md-12">
							<div id="galleries_data"></div>
					</div>
					<input type="hidden" id="del-media-image" value="{{ route('delete.product.media')}}">

					{{--<div class="row">
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('product_dimension') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="dimensions" id="dimensions">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('dimensions')}}.</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('product_image') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="file" name="image" id="image">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('image')}}.</div>
							</div>
						</div>
					</div>--}}
					<div class="submit-section">
						<button class="btn btn-primary submit-btn save-product" type="button">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Add product code -->

<!--- edit product code -->
{{--<div id="edit_product_code" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('edit_new_product') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="frmeditproductcode" action="{{ route('user.save-product') }}">
				<input type="hidden" id="id" name="id">
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('name') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="name" id="edit_name">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('product')}}.</div>
							</div>
						</div>
					</div>
					<div class="submit-section">
						<button class="btn btn-primary submit-btn update-product-code-form" type="button">Submit</button>
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
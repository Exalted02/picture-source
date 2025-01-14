<div class="modal custom-modal fade" id="delete_product_code" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<div class="success-message text-center">
					<div class="success-popup-icon bg-danger" id="delete-prospect-msg">
						<i class="la la-trash-restore"></i>
					</div>
					<h3>{{ __('are_you_sure') }}, {{ __('you_want_delete') }}</h3>
					<p>{{ __('size') }} "<span id="list_name"></span>" {{ __('from_your_account') }}</p>
					<div class="col-lg-12 text-center form-wizard-button">
						<a href="#" class="button btn-lights" data-bs-dismiss="modal">{{ __('not_now') }}</a>
						<a href="javascript:void(0);" class="btn btn-primary data-id-pcode-list" data-url="{{ route('deleteSizeList') }}">{{ __('okay') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Add product code -->
<div id="add_size" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('add_new_size') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="frmproductcode" action="{{ route('user.save-size') }}">
				<input type="hidden" id="id" name="id">
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('size') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="size" id="size">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('size')}}.</div>
							</div>
						</div>
					</div>
					
					<div class="submit-section">
						<button class="btn btn-primary submit-btn save-size" type="button">Submit</button>
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
				<h5 class="modal-title">{{ __('edit_new_size') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="frmeditproductcode" action="{{ route('user.save-size') }}">
				<input type="hidden" id="id" name="id">
					<div class="row">
						<div class="col-sm-12">
							<div class="input-block mb-3">
								<label class="col-form-label">{{ __('name') }}<span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="name" id="edit_name">
								<div class="invalid-feedback">{{ __('please_enter') }} {{ __('size')}}.</div>
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
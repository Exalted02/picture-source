@extends('layouts.app')
@section('content')
@php 
//echo "<pre>";print_r($orders);die;
@endphp
<!-- Page Wrapper -->
<div class="page-wrapper">
	<!-- Page Content -->
	<div class="content container-fluid">
	
		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-md-4">
					<h3 class="page-title">{{ __('order') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard') }}</a></li>
						<li class="breadcrumb-item active">{{ __('order') }}</li>
					</ul>
				</div>
				<div class="col-md-8 float-end ms-auto">
					<div class="d-flex title-head">
						<div class="view-icons">
							<a href="#" class="grid-view btn btn-link"><i class="las la-redo-alt"></i></a>
							<a href="#" class="list-view btn btn-link" id="collapse-header"><i class="las la-expand-arrows-alt"></i></a>
							<a href="javascript:void(0);" class="list-view btn btn-link" id="filter_search"><i class="las la-filter"></i></a>
						</div>
						{{--<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_edit_model"><i class="la la-plus-circle"></i> {{ __('add_customer') }}</a>--}}
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Header -->
		
		<!-- Search Filter -->
		<div class="filter-filelds" id="filter_inputs">
		<form name="search-frm" method="post" action="{{ route('order')}}" id="search-order-frm">
		@csrf
			<div class="row filter-row">
				<div class="col-xl-3">  
					 <div class="input-block">
						<input type="text" class="form-control date-range" name="order_search_daterange" id="order_search_daterange" placeholder="{{ __('from_to_date')}}" value="{{ old('order_search_daterange', request('order_search_daterange')) }}">
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						 <select class="select" name="search_status">
							<option value="">{{ __('please_select') }}</option>
							<option value="1" {{ old('search_status', request('search_status')) == "1" ? 'selected' : '' }}>{{ __('pending') }}</option>
							<option value="2" {{ old('search_status', request('search_status')) == "2" ? 'selected' : '' }}>{{ __('shipped') }}</option>
							<option value="3" {{ old('search_status', request('search_status')) == "3" ? 'selected' : '' }}>{{ __('cancel') }}</option>
							<option value="4" {{ old('search_status', request('search_status')) == "4" ? 'selected' : '' }}>{{ __('deliver') }}</option>
						</select>
					 </div>
				</div>
				<div class="col-xl-3">  
				<a href="javascript:void(0);" class="btn btn-success w-100 search-data"><i class="fa-solid fa-magnifying-glass"></i> {{ __('search') }} </a> 
				</div>
			</div>
			</form>
		</div>
		 <hr>
		 <!-- /Search Filter -->
		 <div class="row">
		 @if($orders->count() > 0)
		 <div class="col-lg-6 mb-2">
				<div class="btn-group">
					<button type="button" class="btn action-btn add-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" onclick="change_multi_status('1','User','{{url('change-multi-status')}}')">Active</a></li>
						<li><a class="dropdown-item" onclick="change_multi_status('0','User','{{url('change-multi-status')}}')">Inactive</a></li>
						<li><a class="dropdown-item" onclick="delete_multi_data('User','{{url('delete-multi-data')}}')">Delete</a></li>
					</ul>
				</div>
			</div>
		@endif
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped custom-table datatable">
						<thead>
							<tr>
							@if($orders->count() > 0)
							<th>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" id="checkAll">
									</label>
								</th>
							@endif
								<th>{{ __('order_id') }}</th>
								<th>{{ __('customer') }}</th>
								{{--<th>{{ __('discount_amount') }}</th>
								<th>{{ __('coupon_discount') }}</th>
								<th>{{ __('shipping_charge') }}</th>--}}
								<th>{{ __('final_amount') }}</th>
								<th>{{ __('order_date') }}</th>
								<th>{{ __('status') }}</th>
								<th class="text-end">Action</th>
							</tr>
						</thead>
						<tbody>
						@foreach($orders as $val)
						
						

							<tr>
								@if($orders->count() > 0)
								<td>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" name="chk_id" data-emp-id="{{$val->id}}">
									</label>
								</td>
								@endif
								<td>{{ $val->id ?? 'N/A'}}</td>
								<td>{{ $val->user_details->name ?? 'N/A'}}</td>
								{{--<td>{{ $val->discount_amount ?? 'N/A'}}</td>
								<td>{{ $val->coupon_discount ?? 'N/A'}}</td>
								<td>{{ $val->shipping_charge ?? 'N/A'}}</td>--}}
								<td>{{ $val->final_amount ?? 'N/A'}}</td>
								<td>{{ date('d/m/Y', strtotime($val->created_at)) ?? ''}}</td>
								<td>
								@if($val->status ==1)
									<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-warning dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-warning"></i> {{ __('pending') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="1" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-warning"></i> {{ __('pending') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="2" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('shipped') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="3" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('cancel') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="4" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('deliver') }}</a>
										</div>
									</div>
								@elseif($val->status ==2)
								<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-info dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-info"></i> {{ __('shipped') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="1" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-warning"></i> {{ __('pending') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="2" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('shipped') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="3" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('cancel') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="4" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('deliver') }}</a>
										</div>
									</div>
								@elseif($val->status ==3)
								<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-success dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-success"></i> {{ __('cancel') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="1" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-warning"></i> {{ __('pending') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="2" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('shipped') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="3" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('cancel') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="4" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('deliver') }}</a>
										</div>
									</div>
								@elseif($val->status ==4)
								<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-success dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-success"></i> {{ __('deliver') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="1" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-warning"></i> {{ __('pending') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="2" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('shipped') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="3" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('cancel') }}</a>
											<a class="dropdown-item update-order-status" href="javascript:void(0);" data-status="4" data-id="{{ $val->id }}" data-url="{{ route('change.order.status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('deliver') }}</a>
										</div>
									</div>
								@endif
								</td>
									
								
								<td class="text-end">
									<div class="dropdown dropdown-action">
										<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
										<div class="dropdown-menu dropdown-menu-right">
										
										{{--<a class="dropdown-item" href="{{ route('view-dealer', ['id'=>$val->id]) }}"><i class="fa-regular fa-eye m-r-5"></i> {{ __('view') }}</a>--}}
											{{--<a class="dropdown-item edit-customer" href="javascript:void(0);" data-id="{{ $val->id ??''}}" data-url=""><i class="fa-solid fa-pencil m-r-5"></i> {{ __('edit') }}</a>--}}
											<a class="dropdown-item" href="{{ route('view-order', $val->id) }}"><i class="fa-regular fa-eye m-r-5"></i> {{ __('view') }}</a>
												{{--<a class="dropdown-item delete-customer" href="javascript:void(0);" data-id="{{ $val->id ?? '' }}" data-url="{{ route('getDeleteCustomer') }}"><i class="fa-regular fa-trash-can m-r-5"></i> {{ __('delete') }}</a>--}}
										</div>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- /Page Content -->
@include('modal.customer-modal')
@include('modal.common')
@endsection 
@section('scripts')
@include('_includes.footer')
<script src="{{ url('front-assets/js/page/order.js') }}"></script>
<script src="{{ url('front-assets/js/filter-search-calender.js') }}"></script>
<script>
$(document).ready(function() {
	
	const has_search = @json($has_search);
	if(has_search==1)
	{
		$('#filter_search').click();
	}

	$(document).on('click', '.update-order-status', function () {
		var status = $(this).data('status');
		var id = $(this).data('id');
		var URL = $(this).data('url');
			$.ajax({
				url: URL,
				type: "POST",
				data: {id:id,status:status,'_token':csrfToken},
				//dataType: 'json',
				success: function(response) {
					setTimeout(() => {
							window.location.reload();
						}, "1000");
				},
			});
	});
});
</script>
@endsection

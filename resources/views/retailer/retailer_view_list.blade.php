@extends('layouts.app')
@section('content')
@php
//$retailer_name = App\Models\User::where('id', $retailer_id)->first()->name;
$customer_details = App\Models\User::where('id', $retailer_id)->first();

$country = '';
$state = '';
$city = '';
if(!empty($customer_details->country))
{
	$country = App\Models\Countries::where('id',$customer_details->country)->first()->name;
}

if(!empty($customer_details->state))
{
	$state = App\Models\States::where('id',$customer_details->state)->first()->name;
}

if(!empty($customer_details->city))
{
	$city = App\Models\Cities::where('id',$customer_details->city)->first()->name;
}
@endphp
<!-- Page Wrapper -->
<div class="page-wrapper">
	<!-- Page Content -->
	<div class="content container-fluid">
	
		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-md-4">
					<h3 class="page-title">{{ __('retailer_view') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard') }}</a></li>
						<li class="breadcrumb-item active">{{ __('retailer') }}</li>
					</ul>
				</div>
				{{--<div class="col-md-8 float-end ms-auto">
					<div class="d-flex title-head">
						<div class="view-icons">
							<a href="#" class="grid-view btn btn-link"><i class="las la-redo-alt"></i></a>
							<a href="#" class="list-view btn btn-link" id="collapse-header"><i class="las la-expand-arrows-alt"></i></a>
							<a href="javascript:void(0);" class="list-view btn btn-link" id="filter_search"><i class="las la-filter"></i></a>
						</div>
						<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_edit_model"><i class="la la-plus-circle"></i> {{ __('add_customer') }}</a>
					</div>
				</div>--}}
			</div>
		</div>
		<!-- /Page Header -->
		
		<!-- Search Filter -->
		{{--<div class="filter-filelds" id="filter_inputs">
		<form name="search-frm" method="post" action="{{ route('user.category')}}" id="search-product-code-frm">
		@csrf
			<div class="row filter-row">
				<div class="col-xl-3">  
					 <div class="input-block">
						 <input type="text" class="form-control floating" name="search_name" placeholder="{{ __('category')}}">
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						<input type="text" class="form-control  date-range bookingrange" name="date_range_phone" placeholder="{{ __('from_to_date')}}">
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						 <select class="select" name="search_status">
							<option value="">{{ __('please_select') }}</option>
							<option value="1">{{ __('active') }}</option>
							<option value="0">{{ __('inactive') }}</option>
						</select>
					 </div>
				</div>
				<div class="col-xl-3">  
				<a href="javascript:void(0);" class="btn btn-success w-100 search-data"><i class="fa-solid fa-magnifying-glass"></i> {{ __('search') }} </a> 
				</div>
			</div>
			</form>
		</div>--}}
		 <hr>
		 <!-- /Search Filter -->
		<div class="project-task">
			<ul class="nav nav-tabs nav-tabs-top nav-justified mb-0">
				<li class="nav-item"><a class="nav-link active" href="#all_details" data-bs-toggle="tab" aria-expanded="true">{{ __('details') }}</a></li>
				<li class="nav-item"><a class="nav-link" href="#all_orders" data-bs-toggle="tab" aria-expanded="true">{{ __('orders') }}</a></li>
					{{--<li class="nav-item"><a class="nav-link active" href="#all_reviews" data-bs-toggle="tab" aria-expanded="true">{{ __('reviews') }}</a></li>--}}
						{{--<li class="nav-item"><a class="nav-link" href="#delivery_address" data-bs-toggle="tab" aria-expanded="false">{{ __('delivery_address') }}</a></li>--}}
					{{--<li class="nav-item"><a class="nav-link" href="#wistlist" data-bs-toggle="tab" aria-expanded="false">{{ __('wistlist') }}</a></li>--}}
			</ul>
			<div class="tab-content">
				<div class="tab-pane show active" id="all_details">
					@if(!empty($customer_details))
						<div class="rowline"></div>
						<div class="row col-md-12 mt-4"><h4><strong>{{ __('customer_details') }}</strong></h4></div>
						<div class="row col-md-12">
							<div class="col-md-3 mt-3">
								<strong>{{ __('customer_name') }}</strong>
								<div>{{ $customer_details->name ?? 'N/A' }}</div>
							</div>
							
							<div class="col-md-3 mt-3">
								<strong>{{ __('email') }}</strong>
								<div>{{ $customer_details->email ?? 'N/A' }}</div>
							</div>
							
							<div class="col-md-3 mt-3">
								  <strong>{{ __('phone_number') }}</strong>
								  <div>{{ $customer_details->phone_number ?? 'N/A' }}</div>
							</div>
							<div class="col-md-3 mt-3">
								  <strong>{{ __('address') }}</strong>
								  <div>{{ $customer_details->address ?? 'N/A' }}</div>
							</div>
							@if(!empty($country))
							<div class="col-md-3 mt-3">
								  <strong>{{ __('country') }}</strong>
								  <div>{{ $country ?? 'N/A' }}</div>
							</div>
							@endif
							
							@if(!empty($state))
							<div class="col-md-3 mt-3">
								  <strong>{{ __('state') }}</strong>
								  <div>{{ $state ?? 'N/A' }}</div>
							</div>
							@endif
							
							@if(!empty($city))
							<div class="col-md-3 mt-3">
								  <strong>{{ __('city') }}</strong>
								  <div>{{ $city ?? 'N/A' }}</div>
							</div>
							@endif
							
							@if(!empty($customer_details->zipcode))
							<div class="col-md-3 mt-3">
								  <strong>{{ __('pincode') }}</strong>
								  <div>{{ $customer_details->zipcode ?? 'N/A' }}</div>
							</div>
							@endif
						</div>
					@endif
				</div>
				<div class="tab-pane show" id="all_orders">
					<div class="contact-tab-wrap">
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-striped custom-table datatable">
										<thead>
											<tr>
											
												{{--<th>{{ __('sl_no') }}</th>--}}
												<th>{{ __('order_id') }}</th>
												<th>{{ __('order_amount') }}</th>
												<th>{{ __('final_amount') }}</th>
												<th>{{ __('status') }}</th>
												<th>{{ __('created_date') }}</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
										@foreach($order_dtls as $val)
										@php 
											$order_status = $val->status==1 ? 'pending' :($val->status==2 ? 'shipped' : ($val->status==3 ? 'cancel' : 'deliver' ));
										@endphp
											<tr>
												
												<td>{{ $val->id ?? ''}}</td>
												<td>{{ $val->order_total ?? ''}}</td>
												<td>{{ $val->final_amount ?? ''}}</td>
												<td>{{ $order_status ?? ''}}</td>
												
												<td>{{ date('d/m/Y', strtotime($val->created_at)) ?? ''}}</td>
												
												<td>
													<div class="dropdown dropdown-action">
														<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
														<div class="dropdown-menu dropdown-menu-right">
														
															<a class="dropdown-item" href="{{ route('view-retailer', $val->id) }}"><i class="fa-regular fa-eye m-r-5"></i> {{ __('view') }}</a>
															
														</div>
													</div>
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>
									<form name="frmtax" id="frmtax" action="{{ route('retailer-tax-download') }}">
									 <input type="hidden" id="retailer_id" name="retailer_id">
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- /Page Content -->
@include('modal.retailer-modal')
@include('modal.common')
@endsection 
@section('scripts')
@include('_includes.footer')
<script src="{{ url('front-assets/js/page/retailers.js') }}"></script>
@endsection

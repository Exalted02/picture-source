@extends('layouts.app')
@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<!-- Page Wrapper -->
<div class="page-wrapper">
	<!-- Page Content -->
	<div class="content container-fluid">
	
		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-md-4">
					<h3 class="page-title">{{ __('account_remove') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard') }}</a></li>
						<li class="breadcrumb-item active">{{ __('account_remove') }}</li>
					</ul>
				</div>
				{{--<div class="col-md-8 float-end ms-auto">
					<div class="d-flex title-head">
						<div class="view-icons">
							<a href="#" class="grid-view btn btn-link"><i class="las la-redo-alt"></i></a>
							<a href="#" class="list-view btn btn-link" id="collapse-header"><i class="las la-expand-arrows-alt"></i></a>
							<a href="javascript:void(0);" class="list-view btn btn-link" id="filter_search"><i class="las la-filter"></i></a>
						</div>
						<a href="#" class="btn add-btn add_size" data-bs-toggle="modal" data-bs-target="#add_size"><i class="la la-plus-circle"></i> {{ __('add_size') }}</a>
					</div>
				</div>--}}
			</div>
		</div>
		<!-- /Page Header -->
		
		<!-- Search Filter -->
		{{--<div class="filter-filelds" id="filter_inputs">
		<form name="search-frm" method="post" action="{{ route('user.size')}}" id="search-product-code-frm">
		@csrf
			<div class="row filter-row">
				<div class="col-xl-3">  
					 <div class="input-block">
						 <input type="text" class="form-control floating" name="search_name" placeholder="{{ __('size')}}">
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
		 {{--<div class="row">
		 @if($sizes->count() > 0)
			<div class="col-lg-6 mb-2">
				<div class="btn-group">
					<button type="button" class="btn action-btn add-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" onclick="change_multi_status('1','Size','{{url('change-multi-status')}}')">Active</a></li>
						<li><a class="dropdown-item" onclick="change_multi_status('0','Size','{{url('change-multi-status')}}')">Inactive</a></li>
						<li><a class="dropdown-item" onclick="delete_multi_data('Size','{{url('delete-multi-data')}}')">Delete</a></li>
					</ul>
				</div>
			</div>
		@endif
		</div>--}}

		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped custom-table datatable">
						<thead>
							<tr>
								<th>{{ __('email') }}</th>
								<th>{{ __('region') }}</th>
								<th>{{ __('status') }}</th>
							</tr>
						</thead>
						<tbody>
						@foreach($lists as $list)
						
							<tr>
								<td>{{ $list->email ?? ''}}</td>
								<td class="show-region" data-region="{{ $list->region }}" style="cursor:pointer">{{ Str::words($list->region ?? '', 8, '...') }}</td>
								<td>
								@if($list->status ==1)
									<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-success dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-success"></i> {{ __('approve') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-update-status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('approve') }}</a>
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-update-status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('pending') }}</a>
											<a class="dropdown-item delete-remove-account" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-delete') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('delete') }}</a>
										</div>
									</div>
								 @elseif($list->status ==2)
									<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-info dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-info"></i> {{ __('pending') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-update-status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('approve') }}</a>
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-update-status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('pending') }}</a>
											<a class="dropdown-item delete-remove-account" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-delete') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('delete') }}</a>
										</div>
									</div>
								@else
									<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-danger dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-danger"></i> {{ __('delete') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-update-status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('approve') }}</a>
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-update-status') }}"><i class="fa-regular fa-circle-dot text-info"></i> {{ __('pending') }}</a>
											<a class="dropdown-item delete-remove-account" href="javascript:void(0);" data-id="{{ $list->id }}" data-url="{{ route('remove-account-delete') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('delete') }}</a>
										</div>
									</div>
								@endif
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
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Account Region Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
         <span id="region_data"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
      </div>
    </div>
  </div>
</div>

@include('modal.common')
@endsection 
@section('scripts')
@include('_includes.footer')

<script>
$(document).ready(function() {
   $(document).on('click','.update-status', function(){
	   var id = $(this).data('id');
	   var URL = $(this).data('url');
	    //alert(id); alert(URL);
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
   
    $(document).on('click','.delete-remove-account', function(){
	   var id = $(this).data('id');
	   var URL = $(this).data('url');
	    //alert(id); alert(URL);
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
   
   $(document).on('click','.show-region', function(){
	   var region = $(this).data('region');
	    $('#region_data').html(region);
		$('#exampleModal').modal('show');
		
   });
   
   
});
	
	
</script>
@endsection

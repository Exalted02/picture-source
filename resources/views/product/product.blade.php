@extends('layouts.app')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
	<!-- Page Content -->
	<div class="content container-fluid">
	
		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-md-4">
					<h3 class="page-title">{{ __('product') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard') }}</a></li>
						<li class="breadcrumb-item active">{{ __('product') }}</li>
					</ul>
				</div>
				<div class="col-md-8 float-end ms-auto">
					<div class="d-flex title-head">
						<div class="view-icons">
							<a href="#" class="grid-view btn btn-link"><i class="las la-redo-alt"></i></a>
							<a href="#" class="list-view btn btn-link" id="collapse-header"><i class="las la-expand-arrows-alt"></i></a>
							<a href="javascript:void(0);" class="list-view btn btn-link" id="filter_search"><i class="las la-filter"></i></a>
						</div>
						<a href="#" class="btn add-btn add_product" data-bs-toggle="modal" data-bs-target="#add_product"><i class="la la-plus-circle"></i> {{ __('add_product') }}</a>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Header -->
		
		<!-- Search Filter -->
		<div class="filter-filelds" id="filter_inputs">
		<form name="search-frm" method="post" action="{{ route('user.product')}}" id="search-product-code-frm">
		@csrf
			<div class="row filter-row">
				<div class="col-xl-3">  
					 <div class="input-block">
						 <input type="text" class="form-control floating" name="search_name" value="{{ old('search_name', request('search_name'))}}" placeholder="{{ __('product')}}">
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						<input type="text" class="form-control  date-range bookingrange" name="date_range_phone" placeholder="{{ __('from_to_date')}}">
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						<select class="select form-control" name="search_artist">
						<option value="">{{ __('please_select') }} {{ __('artist') }}</option>
						@foreach($artists as $artist)
							<option value="{{ $artist->id }}" {{ old('search_artist', request('search_artist')) == (string) $artist->id ? 'selected' : '' }}>{{ $artist->name }}</option>
						@endforeach
						</select>
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						<select class="select form-control" name="search_category">
						<option value="">{{ __('please_select') }} {{ __('category') }}</option>
						@foreach($categories as $category)
							<option value="{{ $category->id }}" {{ old('search_category', request('search_category')) == (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
						@endforeach
						</select>
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						<select class="select form-control" name="search_size">
						<option value="">{{ __('please_select') }} {{ __('size') }}</option>
						@foreach($sizes as $size)
							<option value="{{ $size->id }}" {{ old('search_size', request('search_size')) == (string) $size->id ? 'selected' : '' }}>{{ $size->size }}</option>
						@endforeach
						</select>
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						<select class="select form-control" name="search_color">
						<option value="">{{ __('please_select') }} {{ __('color') }}</option>
						@foreach($colors as $color)
							<option value="{{ $color->id }}" {{ old('search_color', request('search_color')) == (string) $color->id ? 'selected' : '' }}>{{ $color->color }}</option>
						@endforeach
						</select>
					 </div>
				</div>
				<div class="col-xl-3">  
					 <div class="input-block">
						 <select class="select" name="search_status">
							<option value="">{{ __('please_select') }} {{ __('status') }}</option>
							<option value="1" {{ old('search_status', request('search_status')) == "1" ? 'selected' : '' }}>{{ __('active') }}</option>
							<option value="0" {{ old('search_status', request('search_status')) == "0" ? 'selected' : '' }}>{{ __('inactive') }}</option>
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
		 @if($products->count() > 0)
			<div class="col-lg-6 mb-2">
				<div class="btn-group">
					<button type="button" class="btn action-btn add-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" onclick="change_multi_status('1','Artists','{{url('change-multi-status')}}')">Active</a></li>
						<li><a class="dropdown-item" onclick="change_multi_status('0','Artists','{{url('change-multi-status')}}')">Inactive</a></li>
						<li><a class="dropdown-item" onclick="delete_multi_data('Artists','{{url('delete-multi-data')}}')">Delete</a></li>
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
							@if($products->count() > 0)
								<th>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" id="checkAll">
									</label>
								</th>
							@endif
								{{--<th>{{ __('sl_no') }}</th>--}}
								<th>{{ __('product_code') }}</th>
								<th>{{ __('name') }}</th>
								<th>{{ __('price') }}</th>
								<th>{{ __('created_date') }}</th>
								<th>{{ __('status') }}</th>
								<th class="text-end">Action</th>
							</tr>
						</thead>
						<tbody>
						@foreach($products as $val)
						
						

							<tr>
								@if($products->count() > 0)
								<td>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" name="chk_id" data-emp-id="{{$val->id}}">
									</label>
								</td>
								@endif
								<td>{{ $val->product_code ?? ''}}</td>
								<td>{{ $val->name ?? ''}}</td>
								<td>Rs. {{ $val->price ?? ''}}</td>
								<td>{{ date('d/m/Y', strtotime($val->created_at)) ?? ''}}</td>
								<td>
								@if($val->status ==1)
									<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-success dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-success"></i> {{ __('active') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $val->id }}" data-url="{{ route('product-update-status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('active') }}</a>
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $val->id }}" data-url="{{ route('product-update-status') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('inactive') }}</a>
										</div>
									</div>
								 @else
									<div class="dropdown action-label">
										<a class="btn btn-white btn-sm badge-outline-danger dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fa-regular fa-circle-dot text-danger"></i> {{ __('inactive') }}
										</a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $val->id }}" data-url="{{ route('product-update-status') }}"><i class="fa-regular fa-circle-dot text-success"></i> {{ __('active') }}</a>
											<a class="dropdown-item update-status" href="javascript:void(0);" data-id="{{ $val->id }}" data-url="{{ route('product-update-status') }}"><i class="fa-regular fa-circle-dot text-danger"></i> {{ __('inactive') }}</a>
										</div>
									</div> 
								 
								 @endif
								</td>
									
								
								<td class="text-end">
									<div class="dropdown dropdown-action">
										<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item edit-product" href="javascript:void(0);" data-id="{{ $val->id ??''}}" data-url="{{ route('edit-product') }}"><i class="fa-solid fa-pencil m-r-5"></i> {{ __('edit') }}</a>
											<a class="dropdown-item" href="{{ route('view-product', $val->id) }}"><i class="fa-regular fa-eye m-r-5"></i> {{ __('view') }}</a>
											<a class="dropdown-item delete-product-code" href="javascript:void(0);" data-id="{{ $val->id ?? '' }}" data-url="{{ route('getDeleteArtist') }}"><i class="fa-regular fa-trash-can m-r-5"></i> {{ __('delete') }}</a>
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
@include('modal.product-modal')
@include('modal.common')
@endsection 
@section('scripts')
@include('_includes.footer')
<script src="{{ url('front-assets/css/dropzone.css') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
<script src="{{ url('front-assets/js/page/product.js') }}"></script>
<link href="{{ url('front-assets/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<script src="{{ url('front-assets/summernote/summernote-lite.min.js') }}"></script>

<script>

$(document).ready(function() {
		const has_search = @json($has_search);
		if(has_search==1)
		{
			$('#filter_search').click();
		}
	});

</script>
@endsection

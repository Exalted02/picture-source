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
					<h3 class="page-title">{{ __('order_wistlist_view') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard') }}</a></li>
						<li class="breadcrumb-item active">{{ __('view') }}</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Page Header -->
		{{--<div class="card">
			<div class="card-body">
				<ul class="nav nav-tabs nav-tabs-solid nav-justified">
					<li class="nav-item"><a class="nav-link active" href="user-dashboard.html">Reviews</a></li>
					<li class="nav-item"><a class="nav-link" href="user-all-jobs.html">All </a></li>
					<li class="nav-item"><a class="nav-link" href="saved-jobs.html">Saved</a></li>
					<li class="nav-item"><a class="nav-link" href="applied-jobs.html">Applied</a></li>
					<li class="nav-item"><a class="nav-link" href="interviewing.html">Interviewing</a></li>
					<li class="nav-item"><a class="nav-link" href="offered-jobs.html">Offered</a></li>
					<li class="nav-item"><a class="nav-link" href="visited-jobs.html">Visitied </a></li>
					<li class="nav-item"><a class="nav-link" href="archived-jobs.html">Archived </a></li>
				</ul>
			</div>
		</div>--}}
		<div class="project-task">
		{{--<ul class="nav nav-tabs nav-tabs-top nav-justified mb-0">
					<li class="nav-item"><a class="nav-link active" href="#all_reviews" data-bs-toggle="tab" aria-expanded="true">{{ __('reviews') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#pending_tasks" data-bs-toggle="tab" aria-expanded="false">Pending Tasks</a></li>
					<li class="nav-item"><a class="nav-link" href="#completed_tasks" data-bs-toggle="tab" aria-expanded="false">Completed Tasks</a></li>
				</ul>--}}
				<div class="tab-content">
					<div class="tab-pane show active" id="all_reviews">
					
						<div class="contact-tab-wrap">
						@if($order_dtls->count() > 0)
							@foreach($order_dtls as $details)
							<div class="multiadd d-flex flex-wrap">
								<div class="col-md-9 mt-3">
									  <strong>{{ __('final_amount') }}</strong>
									  <div>{{$details->final_amount ?? '' }}</div>
								</div>
								<div class="col-md-3 mt-3">
									 <strong>{{ __('order_status') }}</strong>
										<select class="select form-control order-status" id="order_status" style="height:40px;" data-id="{{ $details->id }}" data-url="{{ route('change.order.status')}}">
											<option value="">{{ __('please_select') }}</option>
											<option value="1" {{ $details->status ==1 ? 'selected' : ''; }}>{{ __('pending') }}</option>
											<option value="2" {{ $details->status ==2 ? 'selected' : ''; }}>{{ __('shipped') }}</option>
											<option value="3" {{ $details->status ==3 ? 'selected' : ''; }}>{{ __('cancel') }}</option>
											<option value="4" {{ $details->status ==4 ? 'selected' : ''; }}>{{ __('deliver') }}</option>
										</select>
									 
								</div>
								<div class="rowline"></div>
								@foreach($details->order_details as $key=>$val)
								@php 
								$color = '';
								$size = '';
								$existscolor = 	App\Models\Color::where('id',$val->color_id)->exists();
								if($existscolor)
								{
									$color = 	App\Models\Color::where('id',$val->color_id)->first()->color;
								}
								
								$existssize =  App\Models\Size::where('id',$val->size_id)->exists();
								if($existssize)
								{
								$size = 	App\Models\Size::where('id',$val->size_id)->first()->size;
								}
								
								@endphp
								<div class="row col-md-12">
									<div class="col-md-3 mt-3">
										<strong>{{ __('product_name') }}</strong>
										<div>{{ $val->product_name ?? 'N/A' }}</div>
									</div>
									
									<div class="col-md-3 mt-3">
										  <strong>{{ __('product_code') }}</strong>
										  <div>{{ $val->product_code ?? 'N/A' }}</div>
									</div>
									<div class="col-md-3 mt-3">
										  <strong>{{ __('quantity') }}</strong>
										  <div>{{ $val->quantity ?? 'N/A' }}</div>
									</div>
									<div class="col-md-3 mt-3">
										  <strong>{{ __('price') }}</strong>
										  <div>{{ $val->price ?? 'N/A' }}</div>
									</div>
									<div class="col-md-3 mt-3">
										  <strong>{{ __('color') }}</strong>
										  <div>{{ $color ?? 'N/A' }}</div>
									</div>
									<div class="col-md-3 mt-3">
										  <strong>{{ __('size') }}</strong>
										  <div>{{ $size ?? 'N/A' }}</div>
									</div>
								</div>
								@if($key < count($details->order_details)-1)
								<div class="rowline"></div>
								@endif
								@endforeach
								
								@if($wistlists->count() > 0)
								<div class="rowline"></div>
								<div class="row col-md-12 mt-4"><h4><strong>Wistlist</strong></h4></div>
								@foreach($wistlists as $key=>$val)
								<div class="row col-md-12">
									<div class="col-md-3 mt-3">
										<strong>{{ __('email_address') }}</strong>
										<div>{{ $val->email_address ?? '' }}</div>
									</div>
									
									<div class="col-md-3 mt-3">
										  <strong>{{ __('relationship') }}</strong>
										  <div>{{ $val->relationship ?? '' }}</div>
									</div>
									<div class="col-md-3 mt-3">
										  <strong>{{ __('birthdate') }}</strong>
										  <div>{{ date('d/m/Y', strtotime($val->birthdate)) }}</div>
									</div>
									<div class="col-md-3 mt-3">
										  <strong>{{ __('aniversary') }}</strong>
										  <div>{{ date('d/m/Y', strtotime($val->aniversary)) }}</div>
									</div>
								</div>
								@if($key < count($wistlists)-1)
								<div class="rowline"></div>
								@endif
								@endforeach
								@endif
								
							</div>
							@endforeach
						@endif
						</div>
					</div>
					<div class="tab-pane" id="pending_tasks"></div>
					<div class="tab-pane" id="completed_tasks"></div>
				</div>
		</div>
		 

		
	</div>
</div>
	<!-- /Page Content -->

@include('modal.common')
@endsection 
@section('scripts')
@include('_includes.footer')


<script>
$(window).on('load', function() {
    $('.select').select2();
});
$(document).ready(function() {
	//alert('ok');
	$('.select').select2();
	$(document).on('change','.order-status', function(){
		//$(this).select2();
		var status =$(this).val();
		var form = $("#frmproductcode");
		var id = $(this).data('id');
		var URL = $(this).data('url');
		//alert(status);alert(id);alert(URL);
			$.ajax({
				url: URL,
				type: "POST",
				data: {id:id,status:status,'_token':csrfToken},
				//dataType: 'json',
				success: function(response) {
					
				},
			});
	});
});




</script>
@endsection

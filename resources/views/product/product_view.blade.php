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
					<h3 class="page-title">{{ __('product_view') }}</h3>
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
				<ul class="nav nav-tabs nav-tabs-top nav-justified mb-0">
					<li class="nav-item"><a class="nav-link active" href="#all_reviews" data-bs-toggle="tab" aria-expanded="true">{{ __('reviews') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#pending_tasks" data-bs-toggle="tab" aria-expanded="false">Pending Tasks</a></li>
					<li class="nav-item"><a class="nav-link" href="#completed_tasks" data-bs-toggle="tab" aria-expanded="false">Completed Tasks</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane show active" id="all_reviews">
					
						<div class="contact-tab-wrap">
						@if($reviews->count() > 0)
							@foreach($reviews as $review)
							@if(!empty($review->rating) || !empty($review->comment))
							<div class="multiadd d-flex flex-wrap">
								<div class="col-md-4 mt-3">
									  <strong>{{ __('sender_name') }}</strong>
									  <div>{{$review->get_reviwer->name ?? '' }}</div>
								</div>
								@if(!empty($review->rating))
								<div class="col-md-4 mt-3">
									<strong>{{ __('rating') }}</strong>
									<div>
									@php
										$rating = $review->rating; // Example dynamic rating
										$wholeStars = floor($rating); // Whole stars
										$halfStar = ($rating - $wholeStars) >= 0.5; // Check for half star
										$emptyStars = 5 - $wholeStars - ($halfStar ? 1 : 0); // Remaining empty stars
									@endphp

										<div class="rating">
											<!-- Full stars -->
											@for ($i = 0; $i < $wholeStars; $i++)
												<i class="fa fa-star" style="color: gold;"></i>
											@endfor

											<!-- Half star -->
											@if ($halfStar)
												<i class="fa fa-star-half-o" style="color: gold;"></i>
											@endif

											<!-- Empty stars -->
											@for ($i = 0; $i < $emptyStars; $i++)
												<i class="fa fa-star-o" style="color: gold;"></i>
											@endfor
										</div>
									</div>
								</div>
								@endif
								@if(!empty($review->comment))
								<div class="col-md-12 mt-3">
									  <strong>{{ __('reviews') }}</strong>
									  <div>{{ $review->comment ?? '' }}</div>
								</div>
								@endif
							</div>
							@endif
							@endforeach
						@endif
							{{--<div class="multiadd d-flex flex-wrap">
								<div class="col-md-4 mt-3">
									  <strong>{{ __('sender') }}</strong>
									  <div>Rajesh</div>
								</div>
								<div class="col-md-12 mt-3">
									  <strong>{{ __('reviews') }}</strong>
									  <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</div>
								</div>
							</div>--}}
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
<script src="{{ url('front-assets/css/dropzone.css') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
<script src="{{ url('front-assets/js/page/product.js') }}"></script>
<link href="{{ url('front-assets/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<script src="{{ url('front-assets/summernote/summernote-lite.min.js') }}"></script>

<script>



</script>
@endsection

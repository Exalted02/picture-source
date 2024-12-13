@extends('layouts.app')
@section('content')
@php 
//echo "<pre>";print_r($contacts);die;
@endphp
<!-- Page Wrapper -->
<div class="page-wrapper">
	<!-- Page Content -->
	<div class="content container-fluid">
	
		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-md-4">
					<h3 class="page-title">Contact</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
						<li class="breadcrumb-item active">Contact</li>
					</ul>
				</div>
				<div class="col-md-8 float-end ms-auto">
					<div class="d-flex title-head">
						<div class="view-icons">
							<a href="#" class="grid-view btn btn-link"><i class="las la-redo-alt"></i></a>
							<a href="#" class="list-view btn btn-link" id="collapse-header"><i class="las la-expand-arrows-alt"></i></a>
							<a href="javascript:void(0);" class="list-view btn btn-link" id="filter_search"><i class="las la-filter"></i></a>
						</div>
						<div class="form-sort">
							<a href="javascript:void(0);" class="list-view btn btn-link" data-bs-toggle="modal" data-bs-target="#export"><i class="las la-file-export"></i>Export</a>
						</div>
						<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_contact"><i class="la la-plus-circle"></i> Add Contact</a>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Header -->
		
		<!-- Search Filter -->
		<div class="filter-filelds" id="filter_inputs">
			<div class="row filter-row">
				<div class="col-xl-2">  
					 <div class="input-block mb-3 form-focus">
						 <input type="text" class="form-control floating">
						 <label class="focus-label">Contact Name</label>
					 </div>
				</div>
				<div class="col-xl-2">  
					<div class="input-block mb-3 form-focus">
						<input type="text" class="form-control floating">
						<label class="focus-label">Email</label>
					</div>
			   </div>
			   <div class="col-xl-2">  
					<div class="input-block mb-3 form-focus">
						<input type="text" class="form-control floating">
						<label class="focus-label">Phone Number</label>
					</div>
				</div>
				<div class="col-xl-2">  
					 <div class="input-block mb-3 form-focus focused">
						<input type="text" class="form-control  date-range bookingrange">
						 <label class="focus-label">From - To Date</label>
					 </div>
				 </div>
				<div class="col-xl-2"> 
					<div class="input-block mb-3 form-focus select-focus">
						<select class="select floating"> 
							<option>--Select--</option>
							<option>Germany</option>
							<option>USA</option>
							<option>Canada</option>
							<option>India</option>
							<option>China</option>
						</select>
						<label class="focus-label">Location</label>
					</div>
				</div>
				<div class="col-xl-2">  
					 <a href="#" class="btn btn-success w-100"> Search </a>  
				</div>     
			 </div>
		</div>
		 <hr>
		 <!-- /Search Filter -->
		<div class="filter-section">
			<ul>
				<li>
					<div class="view-icons">
						<a href="{{ route('user.contact')}}" class="list-view btn btn-link {{ request()->routeIs('user.contact') ? 'active' : '' }}"><i class="las la-list"></i></a>
						<a href="{{ route('user.contact-grid')}}" class="grid-view btn btn-link {{ request()->routeIs('user.contact-grid') ? 'active' : '' }}"><i class="las la-th"></i></a>
					</div>
				</li>
				<li>
					<div class="form-sort">
						<i class="las la-sort-alpha-up-alt"></i>
						<select class="select">
							<option>Sort By Alphabet</option>
							<option>Ascending</option>
							<option>Descending</option>
							<option>Recently Viewed</option>
							<option>Recently Added</option>
						</select>
					</div>
				</li>
				<li>
					<div class="form-sorts dropdown">
						<a href="javascript:void(0);" class="dropdown-toggle" id="table-filter"><i class="las la-filter me-2"></i>Filter</a>
						<div class="filter-dropdown-menu">
							<div class="filter-set-view">
								<div class="filter-set-head">
									<h4>Filter</h4>
								</div>
								<div class="accordion" id="accordionExample">
									<div class="filter-set-content">
										<div class="filter-set-content-head">
											<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Rating<i class="las la-angle-right"></i></a>
										</div>
										<div class="filter-set-contents accordion-collapse collapse show" id="collapseOne" data-bs-parent="#accordionExample">
											<ul>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox" checked>
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="rating">
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<span>5.0</span>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="rating">
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star"></i>
														<span>4.0</span>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="rating">
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<span>3.0</span>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="rating">
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<span>2.0</span>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="rating">
														<i class="fa fa-star filled"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<span>1.0</span>
													</div>
												</li>
											</ul>
										</div>
									</div>
									<div class="filter-set-content">
										<div class="filter-set-content-head">
											<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Owner<i class="las la-angle-right"></i></a>
										</div>
										<div class="filter-set-contents accordion-collapse collapse" id="collapseTwo" data-bs-parent="#accordionExample">
											<ul>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox" checked>
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Hendry</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Guillory</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Jami</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Theresa</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Espinosa</h5>
													</div>
												</li>
											</ul>
										</div>
									</div>
									<div class="filter-set-content">
										<div class="filter-set-content-head">
											<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Tags<i class="las la-angle-right"></i></a>
										</div>
										<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
											<ul>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox" checked>
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Promotion</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Rated</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Rejected</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Collab</h5>
													</div>
												</li>
												<li>
													<div class="filter-checks">
														<label class="checkboxs">
															<input type="checkbox">
															<span class="checkmarks"></span>
														</label>
													</div>
													<div class="collapse-inside-text">
														<h5>Calls</h5>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								
								<div class="filter-reset-btns">
									<a href="#" class="btn btn-light">Reset</a>
									<a href="#" class="btn btn-primary">Filter</a>
								</div>
							</div>
						</div>
					</div>
				</li>
				<li>
					<div class="search-set">
						<div class="search-input">
							<a href="#" class="btn btn-searchset"><i class="las la-search"></i></a>
							<div class="dataTables_filter">
								<label> <input type="search" class="form-control form-control-sm" placeholder="Search"></label>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<!--  List section start -->
		<div class="row mt-4">
					@foreach($contacts as $val)
					@php
						$add_type = ($val->address_status == 1) ? 'Adresse' :(($val->address_status == 2) ? 'Interessent' :(($val->address_status == 3) ? 'Kunde' :(($val->address_status == 4) ? 'Lieferant' : '')));
					@endphp
						<div class="col-xxl-3 col-xl-4 col-md-6">
							<div class="contact-grid">
								<div class="grid-head">
									<div class="users-profile">
										<h5 class="name-user">
											<a href="javascript:void(0);">{{ $val->name ?? ''}}</a>
											<span>{{ $add_type ?? ''}}</span>
										</h5>
									</div>
									<div class="dropdown">
										<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item edit-contact" href="javascript:void(0);"  data-id="{{ $val->id ??''}}" data-url="{{ route('edit-contact') }}"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
											<a class="dropdown-item delete-contact" href="javascript:void(0);"  data-id="{{ $val->id ?? '' }}" data-url="{{ route('getDeleteContactName') }}"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
											<a class="dropdown-item" href="contact-details.html"><i class="fa-regular fa-eye"></i> Preview</a>
										</div>
									</div>
								</div>
								<div class="grid-body">
									<div class="address-info">
										<span><i class="la la-envelope-open"></i>{{ $val->email ?? ''}}</span>
										<span><i class="la la-phone-volume"></i>{{ $val->phone}}</span>
										<span><i class="la la-map-marker"></i>{{ $val->get_state->state_name ?? '' }} , {{ $val->get_country->country_name ?? '' }}</span>
										<span><i class="a la-building"></i>{{ $val->company ?? '' }}</span>
										<span><i class="la la-road"></i>{{ $val->street ?? '' }}</span>
										<span><i class="la la-map-marker"></i>{{ $val->nr ?? '' }}</span>
										<span><i class="la la-map-pin"></i>{{ $val->zip ?? '' }}</span>
										<span><i class="a la-building"></i>{{ $val->city ?? '' }}</span>
									</div>
								</div>
							</div>
						</div>
					@endforeach
						
						<div class="col-lg-12">
							<div class="load-more-btn text-center">
								<a href="javascript:void(0);" class="btn btn-primary load-more-contact" data-url="{{ route('load-more-contact') }}">Load More Contacts<i class="spinner-border_s"></i></a>
							</div>
						</div>
						<input type="text" value="" id="moreload">
		</div>
		
		<!-- List section end    --->
	</div>
</div>
	<!-- /Page Content -->
<!-- /Page Wrapper -->



<!-- Edit Contact -->

<!-- /Edit Contact -->




<!-- Export -->
<div class="modal custom-modal fade modal-padding" id="export" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header header-border justify-content-between p-0">
				<h5 class="modal-title">Export</h5>
				<button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body p-0">
				<form action="contact-list.html">
					<div class="row">
						<div class="col-md-12">
							<div class="input-block mb-3">
								<h5 class="mb-3">Export</h5>
								<div class="status-radio-btns d-flex">
									<div class="people-status-radio">
										<input type="radio" class="status-radio" id="pdf" name="export-type" checked>
										<label for="pdf">Person</label>
									</div>
									<div class="people-status-radio">
										<input type="radio" class="status-radio" id="excel" name="export-type">
										<label for="excel">Organization</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<h4 class="mb-3">Filters</h4>
							<div class="input-block mb-3">
								<label class="col-form-label">Fields <span class="text-danger">*</span></label>
								<select class="select">
									<option>All Fields</option>
									<option>contact</option>
									<option>Company</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-block mb-3">
								<label class="col-form-label">From Date <span class="text-danger">*</span></label>
								<div class="cal-icon">									
									<input class="form-control floating datetimepicker" type="text">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-block mb-3">
								<label class="col-form-label">To Date <span class="text-danger">*</span></label>
								<div class="cal-icon">
									<input class="form-control floating datetimepicker" type="text">
								</div>
							</div>
						</div>
						<div class="col-lg-12 text-end form-wizard-button">
							<button class="button btn-lights reset-btn" type="reset" data-bs-dismiss="modal">Reset</button>
							<button class="btn btn-primary" type="submit">Export Now</button>
						</div>
					</div>
				</form>
				
			</div>
		</div>
	</div>
</div>
@include('modal.contact-modal')
<!-- /Export -->

@endsection 
@section('scripts')
<script>
var csrfToken = "{{ csrf_token() }}";
</script>
@endsection

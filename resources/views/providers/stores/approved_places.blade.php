@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
	<script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>
	<link rel="stylesheet" href="/site/css/jquery.rateyo.min.css">

	<!-- /theme JS files -->

@stop
@section('content')


	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  عرض الأماكن الموافق عليها</h4>
				</div>

				<!-- 						<div class="heading-elements"> -->
				<!-- 							<div class="heading-btn-group"> -->
				<!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a> -->
				<!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a> -->
				<!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a> -->
				<!-- 							</div> -->
				<!-- 						</div> -->
			</div>

			<div class="breadcrumb-line">
				<ul class="breadcrumb">
					<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
					<li class="active"><a href="">عرض الأماكن الموافق عليها</a></li>
				</ul>

				<!-- 						<ul class="breadcrumb-elements"> -->
				<!-- 							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li> -->
				<!-- 							<li class="dropdown"> -->
				<!-- 								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
				<!-- 									<i class="icon-gear position-left"></i> -->
				<!-- 									Settings -->
				<!-- 									<span class="caret"></span> -->
				<!-- 								</a> -->

				<!-- 								<ul class="dropdown-menu dropdown-menu-right"> -->
				<!-- 									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li> -->
				<!-- 									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li> -->
				<!-- 									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li> -->
				<!-- 									<li class="divider"></li> -->
				<!-- 									<li><a href="#"><i class="icon-gear"></i> All settings</a></li> -->
				<!-- 								</ul> -->
				<!-- 							</li> -->
				<!-- 						</ul> -->
			</div>
		</div>
		<!-- /page header -->


		<!-- Content area -->
		<div class="content">
		@include('admin.message')
		<!-- Basic example -->
			<div class="panel panel-flat">
				<div class="panel-heading">
					<h5 class="panel-title">عرض الأماكن الموافق عليها</h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<table class="table datatable-colvis-basic">
					<thead>
					<tr>
						<th>الرقم التعريفي</th>
						<th>عنوان المكان</th>
						<th>القسم الرئيسي</th>
						<th>القسم الفرعي</th>
						<th>المدينة</th>
						<th>صاحب المكان</th>
						<th>متوسط تقييم المكان</th>
						<th>التعليقات</th>
						<th>عدد الاعجابات</th>
						<th>الاجراء المتخذ</th>
					</tr>
					</thead>
					<tbody>
					@php $i=1 @endphp
					@foreach($objects as $object)
						@php
							$rate_maners = \App\Models\OfferRating::where('offer_id',$object->id)->avg('rate');
                            $rate_maners = $rate_maners ? round($rate_maners) : 0;
						@endphp
						<tr parent_id="{{ $object->id }}">

							<td>{{ $i }}</td>
							<td>{{ $object->title }}</td>
							<td>{{ @$object->getCategory->name }}</td>
							<td>{{ @$object->getSubCategory->name }}</td>
							<td>{{ @$object->getCity->name }}</td>
							<td><a href="/provider-panel/users/{{ $object->user_id  }}/edit" target="_blank">{{ $object->getUser ? $object->getUser->username : '_________' }}</a></td>
							<td>
								<div user_id="{{ $object->id }}" id="rateYo{{ $object->id }}" style="margin: auto;"></div>
								<script>
									window.addEventListener('load',function () {
										$('#rateYo{{ $object->id }}').rateYo({
											rating: {{ $rate_maners }},
											starWidth: '20px',
											fullStar: true,
											readOnly: true,
											rtl:true,
										});

									});
								</script>
							</td>
							<td>
								<a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
								   href="/project-details/{{ $object -> id }}"> {{ \App\Models\Comments::where('offer_id',$object->id)->count() }} تعليق
								</a>

								<div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
									 aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel">عرض التعليقات</h4>
											</div>
											<div class="modal-body">
												@foreach(\App\Models\Comments::where('offer_id',$object->id)->get() as $comment)
													<hr>
													<div class="row">
														<label class="col-sm-4">
															<strong>{{ @$comment->getUser->username }}</strong><br>
															<span>{{ @$comment->created_at->diffForHumans() }}</span>
														</label>
														<div class="col-sm-8">{{ @$comment->comment }}</div>
													</div>
												@endforeach

											</div>
											{{--http://www.google.com/maps/place/lat,lng--}}
											{{--http://maps.google.com/maps?q=24.197611,120.780512--}}
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
												</button>
											</div>
										</div>
									</div>
								</div>

							</td>
							<td>{{ $object->getLikes->count() }}</td>
							<td align="center" class="center"> <ul class="icons-list">
									<li class="text-primary-600"><a href="/provider-panel/places/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li>
									<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/provider-panel/places/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
									{{--<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>--}}
								</ul>
							</td>
						</tr>
						@php $i++ @endphp
					@endforeach

					</tbody>
				</table>
			</div>
			<!-- /basic example -->


			<!-- Restore column visibility -->


			<!-- State saving -->

			<!-- Column groups -->



			<!-- Footer -->
		@include('admin.footer')
		<!-- /footer -->

		</div>
		<!-- /content area -->

	</div>
	<script src="/site/js/jquery.rateyo.min.js"></script>
	<!-- /main content -->
	<script type="text/javascript">
		// Warning alert

	</script>
	</body>
	</html>

@stop

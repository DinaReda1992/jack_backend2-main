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

	<!-- /theme JS files -->

@stop
@section('content')


	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -
						عرض الطلبات المرفوضة</h4>
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
					<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
					<li class="active"><a href="">عرض الطلبات المرفوضة</a></li>
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
					<h5 class="panel-title">عرض الطلبات المرفوضة</h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>


				<table class="table table-bordered">
					<thead>
					<tr>
						<th>رقم الطلب</th>
						<th>تاريخ الطلب</th>
						<th>صاحب الطلب</th>
						<th>الاسم الاول </th>
						<th>الاسم الاخير </th>
						<th>سبب الرفض</th>
						<th>عرض الطلب</th>

						<th>الاجراء المتخذ</th>
					</tr>
					</thead>
					<tbody>
					@php
						$i=1;
					@endphp
					@foreach($objects as $object)
						<tr parent_id="{{ $object->id }}">

							<td>{{ $i }}</td>
							<td>{{ $object->created_at->diffForHumans() }}</td>
							<td><a target="_blank" href="/admin-panel/all-users/{{ $object->user_id }}/edit">{{ @$object->getUser->username }}</a></td>
							<td>{{ @$object->first_name }} </td>
							<td>{{ @$object->last_name }} </td>
							<td>{{ @$object->reason_of_cancel }} </td>
							<td>
								<a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
								   href="/project-details/{{ $object -> id }}"> عرض الطلب
								</a>

								<div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
									 aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
											</div>
											<div class="modal-body">
												<hr>
												<div class="row">
													<label class="col-sm-4"><strong> الاسم الأول </strong></label>
													<div class="col-sm-8">{{ @$object->first_name }}</div>
												</div>


												<hr>
												<div class="row">
													<label class="col-sm-4"><strong> الاسم الأخير</strong></label>
													<div class="col-sm-8">{{ @$object->last_name }}</div>
												</div>
												<hr>
												<div class="row">
													<label class="col-sm-4"><strong> الخدمات التي يستطيع تقديمها </strong></label>
													<div class="col-sm-8">
														<ul>
															@foreach($object->getServices as $service)
																<li>{{ $service->getService->name }}</li>
															@endforeach
														</ul>
													</div>
												</div>
												<hr>

												@if($object->brand)
													<div class="row">
														<label class="col-sm-4"><strong> ماركة السيارة</strong></label>
														<div class="col-sm-8">{{ @$object->brand }}</div>
													</div>
													<hr>
												@endif
												@if($object->model)
													<div class="row">
														<label class="col-sm-4"><strong> موديل السيارة السيارة</strong></label>
														<div class="col-sm-8">{{ @$object->model }}</div>
													</div>
													<hr>
												@endif
												@if($object->photo)
													<div class="row">
														<label class="col-sm-4"><strong> صورته الشخصيه</strong></label>
														<div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->photo }}"></div>
													</div>
													<hr>
												@endif
												@if($object->liscense)
													<div class="row">
														<label class="col-sm-4"><strong> صورة الرخصة</strong></label>
														<div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->liscense }}"></div>
													</div>
													<hr>
												@endif
												@if($object->national_photo)
													<div class="row">
														<label class="col-sm-4"><strong> صورة الرقم القومي</strong></label>
														<div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->national_photo }}"></div>
													</div>
													<hr>
												@endif
												@if($object->front_car)
													<div class="row">
														<label class="col-sm-4"><strong> الصورة الامامية للسيارة</strong></label>
														<div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->front_car }}"></div>
													</div>
													<hr>
												@endif
												@if($object->back_car)
													<div class="row">
														<label class="col-sm-4"><strong> الصورة الخلفية للسيارة</strong></label>
														<div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->back_car }}"></div>
													</div>
													<hr>
												@endif
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
							<td align="center" class="center"> <ul class="icons-list">
							{{--<li class="text-primary-600"><a href="/edit-project/{{ $object->id }}"><i class="icon-pencil7"></i></a></li>--}}
							<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/representatives/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
							{{--<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>--}}
							</ul>
							</td>
						</tr>

						@php
							$i++;
						@endphp
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
	<!-- /main content -->
	<script type="text/javascript">
		// Warning alert

	</script>
	</body>
	</html>

@stop

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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  عرض طلبات الفحص المكتملة </h4>
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
							<li class="active"><a href="">عرض طلبات الفحص المكتملة </a></li>
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
							<h5 class="panel-title">عرض طلبات الفحص المكتملة </h5>
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
									<th>نوع خدمة الفحص</th>
									<th>تاريخ اضافة الطلب</th>
									<th>صاحب الطلب</th>
									<th>المدينة</th>
									<th>عرض التقرير</th>
									<th>عرض الطلب </th>
									{{--<th>الاجراء المتخذ</th>--}}
								</tr>
							</thead>
							<tbody>
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">

									<td>{{ $object->id }}</td>
									<td>{{ @$object->getService->name }}</td>
									<td>{{ $object->created_at->diffForHumans() }}</td>
									<td><a href="/users/{{ $object->user_id  }}/edit" target="_blank">{{ $object->getUser ? $object->getUser->full_name()  : '_________' }}</a></td>
									<td><a href="/states/{{ $object->state_id  }}/edit" target="_blank">{{ $object->getState ? $object->getState->name  : 'لم يحدد مدينة' }}</a></td>
									<td>
										<a href="/provider-panel/create_report/{{ $object ->id }}">
											<button type="button" name="button" class="btn btn-success"> عرض التقرير</button>
										</a>
									</td>



									<td><a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;" href="/project-details/{{ $object -> id }}"> عرض </a></td>
									{{--<td align="center" class="center"> <ul class="icons-list">--}}
												 {{--<li class="text-primary-600"><a href="/edit-project/{{ $object->id }}"><i class="icon-pencil7"></i></a></li>--}}
												{{--<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/provider-panel/projects/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>--}}
 										{{--<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>--}}
											{{--</ul>--}}
									{{--</td>--}}
								</tr>
								<div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
											</div>
											<div class="modal-body">
												<div class="row">
													<div  style="margin-top: 25px;" class="col-xs-12">
														<div class="orderInfo">
															<div class="row">
																<div class="col-xs-12">
																	<p style="font-weight: bold">1- معلومات صاحب الطلب :</p>
																	<hr style="margin-top: 0">
																</div>
																<div class="col-xs-6">
																	<label>الاسم الاول بالعربي</label>
																	<input class="form-control" type="text" readonly value="{{ @$object->getUser->first_name }}">
																</div>
																<div class="col-xs-6">
																	<label>الاسم الأخير بالعربي</label>
																	<input class="form-control" type="text" readonly value="{{ @$object->getUser->last_name }}">
																</div>
																<div class="col-xs-6">
																	<label style="margin-top: 50px;">رقم الجوال</label>
																	<input class="form-control" type="tel" readonly value="+{{ @$object->getUser->phonecode }}{{ @$object->getUser->phone }}">
																</div>
																<div class="col-xs-6">
																	<label style="margin-top: 50px;">البريد الالكتروني</label>
																	<input class="form-control" type="email" readonly value="{{ @$object->getUser->email }}">
																</div>
															</div>
														</div>
													</div>


													<div style="margin-top: 25px;" class="col-xs-12">
														<div class="orderInfo">
															<div class="row">
																<div   class="col-xs-12">
																	<p style="font-weight: bold">2- معلومات السيارة :</p>
																	<hr style="margin-top: 0">
																</div>
																<div class="col-xs-6">
																	<label>سنة الصنع</label>
																	<input class="form-control" type="text" readonly value="{{ @$object->getYear->name }}">
																</div>
																<div class="col-xs-6">
																	<label>الماركة</label>
																	<input class="form-control" type="text" readonly value="{{ @$object->getBrand->name }}">
																</div>
																<div class="col-xs-6">
																	<label style="margin-top: 50px;">الموديل</label>
																	<input class="form-control" type="text" readonly value="{{ @$object->getModel->name }}">
																</div>
																<div class="col-xs-6">
																	<label style="margin-top: 50px;">رقم اللوحة (اختياري)</label>
																	<input class="form-control" type="text" readonly value="{{ $object->car_plate }}">
																</div>
																<div class="col-xs-6">
																	<label style="margin-top: 50px;">VIN (اختياري)</label>
																	<input class="form-control" type="text" readonly value="{{ $object->vin }}">
																</div>
															</div>
														</div>
													</div>
													<br>
													<div class="col-xs-12">
														<div class="orderInfo">
															<div class="row">
																<div  style="margin-top: 25px;" class="col-xs-12">
																	<p style="font-weight: bold">3- معلومات صاحب السيارة :</p>
																	<hr style="margin-top: 0">
																</div>
																<div class="col-xs-6">
																	<label>الاسم</label>
																	<input class="form-control" type="text" readonly value="{{ $object->username }}">
																</div>
																<div class="col-xs-6">
																	<label>العنوان</label>
																	<input class="form-control" type="text" readonly value="{{ $object->address }}">
																</div>
																<div class="col-xs-6">
																	<label style="margin-top: 50px;">رقم الجوال</label>
																	<input class="form-control" type="tel" readonly value="{{ $object->phone }}">
																</div>
																<div class="col-xs-12">
																	<label style="margin-top: 50px;">السيارة من معرض</label>
																	<input  type="checkbox" readonly {{ $object->auto_show == 1? 'checked' :'' }}>
																</div>
															</div>
														</div>
													</div>



												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
											</div>
										</div>
									</div>
								</div>

                                @php
                                $notification = \App\Models\Notification::where('type',0)->where('status',0)->where('order_id',$object->id)->first();
								if($notification){
                                $notification->status=1;
                                $notification->save();
								}
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

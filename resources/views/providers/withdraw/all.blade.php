@extends('providers.layout')
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
						عرض طلبات سحب الرصيد</h4>
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
					<li class="active"><a href="">عرض طلبات سحب الرصيد</a></li>
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
		@include('providers.message')
		<!-- Basic example -->
			<div class="panel panel-flat">
				<div class="panel-heading">
					<h5 class="panel-title">عرض طلبات سحب الرصيد</h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>

				<table class="table datatable-colvis-basic" data-order='[[ 0, "desc" ]]' data-page-length='25'>

					<thead>
					<tr>
						<th>رقم الطلب</th>
						<th>تاريخ الطلب</th>
						<th>الرصيد المطلوب تحويله</th>
						<th>حالة الطلب</th>
						<th>الاجراء المتخذ</th>
					</tr>
					</thead>
					<tbody>
					@php
						$i=1;
					@endphp
					@foreach($objects as $object)
						@php
								$status='جارى تنفيذ الطلب';
    $color='orange';

if($object->status==1){
    $status='تم تحويل المبلغ';
    $color='green';
}
if($object->status==2){
    $status='تم الغاء الطلب';
        $color='red';

}
								@endphp
						<tr parent_id="{{ $object->id }}">

							<td>{{ $object->id }}</td>
							<td>{{ $object->created_at->diffForHumans() }}</td>

							<td>{{ $object->price }}</td>
							<td>
								<span style="color: {{$color}}">{{$status}}</span>
							</td>
							<td align="center" class="center">
									@if($object->status==0)
										<a onclick="return false;" data-toggle="modal"
										   data-target="#myModal{{ $object->id }}555" href="#">
											<button type="button" name="button" class="btn btn-danger"> الغاء الطلب</button>
										</a>
										<div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1" role="dialog"
											 aria-labelledby="myModalLabel">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal"
																aria-label="Close"><span aria-hidden="true">&times;</span>
														</button>
														<h4 class="modal-title" id="myModalLabel">الغاء الطلب</h4>
													</div>
													<form method="get" action="/provider-panel/cancel_request_money/{{ $object ->id }}">
														<div class="modal-body">

															<p>
																هل انت متأكد من الغاء طلب سحب الرصيد

															</p>


														</div>
														<div class="modal-footer text-center">
															<button type="submit" class="btn btn-danger">نعم</button>

															<a type="button" class="btn btn-default"
															   data-dismiss="modal">لا</a>
														</div>
													</form>
												</div>
											</div>
										</div>

							@elseif($object->status==1)
									<a onclick="return false;" data-toggle="modal"
									   data-target="#withdrawDetails{{ $object->id }}555" href="#" class="btn btn-success">عرض تفاصيل التحويل</a>
									<div class="modal fade" id="withdrawDetails{{ $object->id }}555" tabindex="-1" role="dialog"
										 aria-labelledby="myModalLabel">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">×</span></button>
													<h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
												</div>
												<div class="modal-body">
													<div class="row">
														<label class="col-sm-4"><strong> وقت التحويل</strong></label>
														<div class="col-sm-8">{{@$object->withdraw->created_at->diffForHumans()}}</div>
													</div>
													<hr>

													<div class="row">
														<label class="col-sm-4"><strong> البنك</strong></label>
														<div class="col-sm-8">{{@$object->withdraw->bank->bank_name}}</div>
													</div>
													<hr>
													<div class="row">
														<label class="col-sm-4"><strong>رقم الحساب</strong></label>
														<div class="col-sm-8">{{@$object->withdraw->bank->account_number}}</div>
													</div>
													<hr>
													<div class="row">
														<label class="col-sm-4"><strong> مبلغ التحويل</strong></label>
														<div class="col-sm-8">{{@$object->withdraw->price}}</div>
													</div>
													<hr>

													<div class="row">
														<label class="col-sm-4"><strong> صورة التحويل</strong></label>
														<div class="col-sm-8">
															@if(@$object->withdraw->photo)
																<img src="/uploads/{{@$object->withdraw->photo}}" width="200" height="200">
															@else
																<h3>لا يوجد</h3>
															@endif
														</div>
													</div>
												</div>


												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
													</button>
												</div>
											</div>
										</div>
									</div>
								@elseif($object->status==2)
										<span class="text-red">تم الالغاء</span>
								@endif
							</td>
						</tr>

						@php
							$i++;
						@endphp
					@endforeach

					@if(count($objects)==0)
						<tr>
							<td colspan="8" align="center">لا يوجد طلبات بعد</td>
						</tr>
					@endif

					</tbody>
				</table>
			</div>
			<!-- /basic example -->


			<!-- Restore column visibility -->


			<!-- State saving -->

			<!-- Column groups -->


			<!-- Footer -->
		@include('providers.footer')
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

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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  طلبات تسعير </h4>
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
					<li class="active"><a href="">عرض  -  طلبات تسعير </a></li>
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
					<h5 class="panel-title">عرض  -  طلبات تسعير </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
					<div style=" text-align: center;" >
						<ul class="nav nav-tabs" role="tablist">
							<li  class="{{!app('request')->input('status') || app('request')->input('status')=='all' ?"active":""}}" ><a  href="/provider-panel/pricing-orders">الكل</a></li>
							<li class="{{ app('request')->input('status')=='new' ?"active":""}}" ><a  href="/provider-panel/pricing-orders?status=new">طلبات تسعير الجديدة</a></li>
							<li class="{{ app('request')->input('status')=='closed' ?"active":""}}" ><a  href="/provider-panel/pricing-orders?status=closed">مغلقة</a></li>

						</ul>
					</div>

				</div>



				<table class="table datatable-colvis-basic">
					<thead>
					<tr>
						<th style="display: none;">#</th>

						<th>#</th>
						<th>تاريخ الطلب</th>
						<th>المستخدم</th>
						<th>عدد القطع</th>

						<th>الوصف</th>
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
								$status='مفتوح';
$color='green';
if($object->status==1) {
    $status='مغلق';
    $color='red';
}
if($object->status==4) {
    $status='منتهى';
    $color='gray';
}


								@endphp
						<tr parent_id="{{ $object->id }}">
							<td style="display: none;">{{ $i }}</td>
							<td>{{ $object->id }}</td>
							<td>{{ @$object->created_at->diffForHumans() }}</td>
							<td>{{ @$object->user->username }}</td>
							<td>{{ @$object->parts->count() }}</td>

							<td>{{  str_limit($object->description, $limit = 100, $end = '...') }}
								 </td>

							<td><span style="color: {{$color}}">{{$status}}</span></td>

							<td align="center" class="center">
								<ul class="icons-list">
{{--									<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/provider-panel/pricing-orders/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>--}}
									<li class="text-teal-600"><a  href="/provider-panel/pricing-orders/{{$object->id}}" class="btn btn-primary" style="color: #fff;">فتح الطلب</a></li>

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
		@include('providers.footer')
		<!-- /footer -->

		</div>
		<!-- /content area -->

	</div>
	<!-- /main content -->
	<script type="text/javascript">
        // Warning alert

	</script>

@stop

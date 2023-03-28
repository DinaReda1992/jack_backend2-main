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
						عرض جميع الطلبات</h4>
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
					<li class="active"><a href="">عرض جميع الطلبات</a></li>
				</ul>
				<div style=" text-align: center;">
					<ul class="nav nav-tabs" role="tablist">
						<li class="{{ !app('request')->input('status')||app('request')->input('status')=='all' ?"active":""}}">
							<a href="/provider-panel/orders"> الكل ({{$shop->all_orders}})</a></li>
						<li class="{{ app('request')->input('status')&&app('request')->input('status')=='new' ?"active":""}}">
							<a href="/provider-panel/orders?status=new">  الجديد ({{$shop->new_orders}})
							</a></li>
						<li class="{{ app('request')->input('status')&&app('request')->input('status')=='preparing' ?"active":""}}">
							<a href="/provider-panel/orders?status=preparing">تم التجهيز ({{$shop->preparing_orders}})
							</a></li>

						<li class="{{ app('request')->input('status')&&app('request')->input('status')=='shipping' ?"active":""}}">
							<a href="/provider-panel/orders?status=shipping">طلبات بالشحن ({{$shop->shipping_orders}})
							</a></li>
						<li class="{{ app('request')->input('status')&&app('request')->input('status')=='completed' ?"active":""}}">
							<a href="/provider-panel/orders?status=completed">مكتملة ({{$shop->completed_orders}})</a></li>

						<li class="{{ app('request')->input('status')&&app('request')->input('status')=='canceled' ?"active":""}}">
							<a href="/provider-panel/orders?status=canceled">ملغية ({{$shop->canceled_orders}})</a>
						</li>


					</ul>
				</div>
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
					<h5 class="panel-title">عرض جميع الطلبات</h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>

				<div class="clearfix"></div>
				<br>
				<form method="get" action="/provider-panel/filter-all">
					<div class="row">
						<div align="center" class="col-md-12">
							<div class="col-md-5" align="center">
								<input name="order_id" type="text" class="form-control" value="{{ @$order_id }}" placeholder="بحث برقم الطلب">
							</div>
							<div class="col-md-2" align="center">
								<button class="btn btn-success">بحث</button>
							</div>
						</div>
					</div>
				</form>
				<div class="clearfix"></div>
				<br>



				@include('providers.orders.orders_details',['type'=>0])

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

@stop

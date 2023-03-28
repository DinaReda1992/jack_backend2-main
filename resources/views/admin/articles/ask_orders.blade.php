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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  طلبات تمييز الموضوع</h4>
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
							<li class="active"><a href="">طلبات تمييز الموضوع</a></li>
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
							<h5 class="panel-title">طلبات تمييز الموضوع</h5>
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
									<th>رقم الموضوع</th>
									<th>رابط الموضوع</th>
									<th>صاحب الموضوع</th>
									<th>رسالة المعلن</th>
									<th>
										تمييز الموضوعات
									</th>

									<th>حذف الطلب</th>
								</tr>
							</thead>
							<tbody>
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">

									<td>{{ $object->id }}</td>
									<td>{{ $object->ads_id }}</td>
									<td><a target="_blank" href="/ads-details/{{ $object -> ads_id }}">عرض الموضوع</a></td>
									<td>{{ $object->username }}</td>
									<td>{{ $object->message }}</td>
                  <td>
                    <a href="/admin-panel/users/adv_ads/{{ $object->ads_id }}">
                      <button type="button" name="button" class="btn {{ $object->getads && $object->getads->adv == 0 ? 'btn-success' : 'btn-warning' }}"> {{ $object->getads && $object->getads->adv == 1 ?  'ازالة تمييز الموضوع' : 'تمييز الموضوع' }}</button>
                    </a>
                  </td>
									<td align="center" class="center"> <ul class="icons-list">
												<!-- <li class="text-primary-600"><a href="/admin-panel/users/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li> -->
												<li class="text-danger-600"><a href="/admin-panel/delete_order/{{ $object->id }}"><i class="icon-trash"></i></a></li>
 										<!--		<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
											</ul>
									</td>
								</tr>
                @php  $object->status = 1 ;
                $object->save();
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

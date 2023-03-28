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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  عرض الاعلانات العادية</h4>
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
							<li class="active"><a href="">عرض الاعلانات العادية</a></li>
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
							<h5 class="panel-title">عرض الاعلانات العادية</h5>
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
									<th>عنوان الاعلان</th>
									<th>تاريخ اضافة الاعلان</th>
									<th>تثبيت في القسم</th>
									<th>صاحب الاعلان</th>
									<th>
										تثبيت في الرئيسية
									</th>
									<th>عرض الاعلان </th>
									<th>الاجراء المتخذ</th>
								</tr>
							</thead>
							<tbody>
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">

									<td>{{ $object->id }}</td>
									<td>{{ $object->title }}</td>
									<td>{{ $object->created_at->diffForHumans() }}</td>
									<td>
										<a href="/admin-panel/users/adv_slider/{{ $object ->id }}">
											<button type="button" name="button" class="btn {{ $object->adv_slider == 0 ? 'btn-success' : 'btn-warning' }}"> {{ $object->adv_slider == 1 ?  'ازالة التثبيت' : 'تثبيت في القسم' }}</button>
										</a>
									</td>									<td><a href="/profile/{{ $object->user_id  }}" target="_blank">{{ $object->user ? $object->user->full_name()  : '_________' }}</a></td>
									<td>
										<a href="/admin-panel/users/adv_ads/{{ $object ->id }}">
											<button type="button" name="button" class="btn {{ $object->adv == 0 ? 'btn-success' : 'btn-warning' }}"> {{ $object->adv == 1 ?  'ازالة التثبيت' : 'تثبيت في الرئيسية' }}</button>
										</a>
									</td>
									<td><a target="_blank" href="/ads-details/{{ $object -> id }}"> عرض </a></td>
									<td align="center" class="center"> <ul class="icons-list">
												<li class="text-primary-600"><a href="/edit-ads/{{ $object->id }}"><i class="icon-pencil7"></i></a></li>
												<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/ads/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>											</ul>
									</td>
								</tr>
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

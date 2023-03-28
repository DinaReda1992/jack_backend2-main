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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض موديلات السيارات {{isset($year)?'ل'.$year->make->name.'-'.$year->year:''}} {{app('request')->input('type')=='deleted'?' المحذوفة':''}}</h4>
						</div>
						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="/admin-panel/models/create{{isset($year)?'?year='.$year->id:''}}"><button type="button" class="btn btn-success" name="button">  اضافة جديد</button></a>
							</div>
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
							<li class="active"><a href=""> - عرض موديلات السيارات {{isset($year)?'ل'.$year->make->name.'-'.$year->year:''}} </a></li>
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

					<!-- Basic example -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">عرض الموديلات</h5>
							<div class="heading-elements">
								<ul class="icons-list">
									<li>
										@if(app('request')->input('type')=='deleted')
											<a style="color: blue" href="/admin-panel/models?year={{app('request')->input('year')}}" > المفعلة <i class="fa fa-gift"></i></a>

										@else
											<a style="color: red" href="/admin-panel/models?year={{app('request')->input('year')}}&type=deleted"> المحذوف <i class="icon-trash"></i></a>
										@endif
									</li>
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<form method="get" action="/admin-panel/search-model">
							<div class="row">
								<div align="center" class="col-md-12">
									<div class="col-md-5" align="center">
										<input name="keyword" type="text" class="form-control" value="{{ @$order_id }}" placeholder="بحث عن الموديل">
									</div>
									<div class="col-md-2" align="center">
										<button class="btn btn-success">بحث</button>
									</div>
								</div>
							</div>
						</form>
						<div class="clearfix"></div>
						<br>


						<table class="table table-bordered">
							<thead>
								<tr>
									
									<th>الرقم التعريفي</th>
									<th>اسم الموديل بالعربى</th>
									<th>اسم الموديل بالانجليزى</th>
									<th>نوع السيارة</th>
									<th>سنة الصنع</th>
									<th>الاجراء المتخذ</th>
								</tr>
							</thead>
							<tbody>
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">
									
									<td>{{ $object->id }}</td>
									<td>{{ $object->name }}</td>
									<td>{{ $object->name_en }}</td>

									<td>{{ @$object->year->make->name }}</td>
									<td>{{ @$object->year->year }}</td>

									<td align="center" class="center">
									        <ul class="icons-list">
												@if($object->is_archived==1)
													<li class="text-teal-600"><a onclick="return false;"
																				 object_id="{{ $object->id }}"
																				 method="get"
																				 delete_url="/admin-panel/model_archived_restore/{{ $object->id }}"
																				 class="sweet_warning" method="get" href="#" message="هل انت متأكد من استعادة النوع"><i
																	class="fa  fa-refresh"></i> استعادة</a></li>

												@else
													<li class="text-primary-600"><a href="/admin-panel/models/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li>
													<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/models/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>

												@endif
<!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
											</ul>  
									</td>
								</tr>
							@endforeach
							@if(count($objects)==0)
								<tr>
									<td colspan="8" align="center">لا يوجد كلمات </td>
								</tr>
							@endif

							</tbody>
						</table>
						<div class="clearfix"></div>
						<br>
						<hr>
						<div align="center">
							{{ $objects->appends(Request::except('page'))->links() }}

						</div>

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
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض الانواع {{app('request')->input('type')=='deleted'?' المحذوفة':''}}</h4>
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
							<li class="active"><a href="">عرض الانواع</a></li>
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

						

						<table class="table datatable-colvis-basic">
							<thead>
								<tr>
									
									<th>الرقم التعريفي</th>
									<th>الموديل بالعربية</th>
									<th>الموديل بالانجليزية</th>
									<th>الماركة التابع له</th>
									{{--<th>الترتيب</th>--}}
									<th>الاجراء المتخذ</th>
								</tr>
							</thead>
							<tbody>
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">
									
									<td>{{ $object->id }}</td>
									<td>{{ $object->name }}</td>
									<td>{{ $object->name_en }}</td>
									<td>{{ @$object->getCarsCategory->name }}</td>
{{--									<td>{{ @$object->getCategory->name }}</td>--}}
{{--									<td>{{ $object->created_at->format("jS \of F, Y g:i:s a") }}</td>--}}
									{{--<td><input value="{{ $object->orders }}" type="text" class="type_order" type_id="{{ $object->id }}" style="width: 50px;padding: 5px;text-align: center"></td>--}}

										<td align="center" class="center"> <ul class="icons-list">
												@if($object->is_archived==1)
													<li class="text-teal-600"><a onclick="return false;"
																				 object_id="{{ $object->id }}"
																				 method="get"
																				 delete_url="/admin-panel/model_archived_restore/{{ $object->id }}"
																				 class="sweet_warning" method="get" href="#" message="هل انت متأكد من استعادة النوع"><i
																	class="fa  fa-refresh"></i> استعادة</a></li>

												@else
													<li class="text-primary-600"><a href="/admin-panel/carsmodels/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li>
													<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/carsmodels/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>

												@endif

<!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
											</ul>  
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
                $(document).ready(function () {
                    $(document).on('change','.type_order',function () {
                        var type_id = $(this).attr('type_id');
                        var order = $(this).val();
                        $.get('/admin-panel/save_order_type/'+ type_id + "/" +order,function (data) {

                        })
                    })
                })
			</script>
</body>
</html>

@stop
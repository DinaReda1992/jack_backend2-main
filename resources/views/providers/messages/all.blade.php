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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض الرسائل</h4>
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
							<li class="active"><a href="">عرض الرسائل</a></li>
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
							<h5 class="panel-title">عرض الرسائل</h5>
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
									<th>القاعة</th>
									<th>آخر رسالة</th>
									<th>مراسلة مع</th>
									<th>عرض المحادثة</th>
									<th>حذف المحادثة</th>
								</tr>
							</thead>
							<tbody>
							@php
							$i=1;
							@endphp
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">
									
									<td>{{ $i }}</td>
									<td>{{ @$object->hall->title }}</td>
									<td>{{ @$object->message }}</td>
									<td>{{ $object->reciever_id !=1 ? @$object->getRecieverUser->username . " "   : @$object->getSenderUser->username . " "  }}</td>
									<td align="center" class="center">
												<a href="/provider-panel/message/{{ $object->hall_id }}/{{ $object->sender_id }}">عرض المحادثة</a>
									</td>
									<td align="center" class="center"> <ul class="icons-list">
											<li class="text-danger-600"><a  href="/provider-panel/delete-reservation/{{ $object->reservation_id  }}"><i class="icon-trash"></i></a></li>
											<!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
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
                $(document).ready(function () {
                    $(document).on('change','.car_order',function () {
                        var car_id = $(this).attr('car_id');
                        var order = $(this).val();
                        $.get('/provider-panel/save_order_car/'+ car_id + "/" +order,function (data) {

                        })
                    })
                })
			</script>
</body>
</html>

@stop
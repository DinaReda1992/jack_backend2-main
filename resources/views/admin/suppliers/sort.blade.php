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
	<script type="text/javascript" src="/assets/js/notify.js"></script>

	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>


	<!-- /theme JS files -->
	<script>
		function setSwitchery(switchElement, checkedBool) {
			if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
				switchElement.setPosition(true);
				switchElement.handleOnchange(true);
			}
		}
		$(document).on( "click", ".switchery", function(e) {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			})
			var id  = $(this).parent().find('input').attr('object_id');
			var d_url =$(this).parent().find('input').attr('delete_url');

			$.ajax({
				url:d_url,
				type:'post',
				data:'',
				success:function(){
					notify.initialization("تم تغيير حالة القسم بنجاح ","success");

				},
				error:function () {
					notify.initialization("حدث خطأ غير متوقع . ","failed");

				}
			})
		});

	</script>

	<!-- /theme JS files -->

@stop
@section('content')


	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض ترتيب الموردين </h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/admin-panel/suppliers/create"><button type="button" class="btn btn-success" name="button">  اضافة جديد</button></a>
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
					<li class="active"><a href="">عرض ترتيب الموردين </a></li>
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
					<h5 class="panel-title">عرض ترتيب الموردين </h5>
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

						<th>الرقم التعريفي</th>
						<th>رقم الجوال</th>
						<th>اسم المورد</th>
						<th>الترتيب </th>

{{--						<th>صورة النوع</th>--}}
					</tr>
					</thead>
					<tbody class="row_position">
					@foreach($objects as $object)
						<tr parent_id="{{ $object->id }}">
							<td>{{ $object->id }}</td>
							<td>0{{ $object->phone }}</td>
							<td>
								<a href="/admin-panel/suppliers/{{$object->id}}/edit">{{@$object->supplier->supplier_name }}</a>
							</td>
							<td><div parent_id="{{ $object->id }}"><input class="input-sort" value="{{@$object->supplier->sort}}"  /></div></td>

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
	<link rel="stylesheet" href="/assets/js/plugins/jquery-ui-1.12.1/jquery-ui.css">

	{{--			<script src="https://code.jquery.com/jquery-1.12.4.js"></script>--}}
	<script src="/assets/js/plugins/jquery-ui-1.12.1/jquery-ui.js"></script>


	<script type="text/javascript">
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		$( ".row_position" ).sortable({
			delay: 150,
			stop: function() {
				var selectedData = new Array();
				$('.row_position>tr').each(function() {
					selectedData.push($(this).attr("parent_id"));
				});
				updateOrder(selectedData);
			}
		});

		$(".input-sort").on('focusout',function(){
			$.ajax({
				url:"/admin-panel/change-sort-suppliers",
				type:'post',
				data:{id:$(this).parent().attr('parent_id'),sort:$(this).val(),single:true},
				success:function(){
				}
			})
		});

		function updateOrder(data) {
			console.log(data);
			$.ajax({
				url:"/admin-panel/change-sort-suppliers",
				type:'post',
				data:{position:data},
				success:function(){
				}
			})
		}
	</script>


@stop
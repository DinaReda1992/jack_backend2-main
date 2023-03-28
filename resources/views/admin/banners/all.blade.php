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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض البنرات </h4>
				</div>
			</div>

			<div class="breadcrumb-line">
				<ul class="breadcrumb">
					<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
					<li class="active"><a href="">عرض البنرات </a></li>
				</ul>

			</div>
		</div>
		<!-- /page header -->


		<!-- Content area -->
		<div class="content">

			<!-- Basic example -->
			<div class="panel panel-flat">
				<div class="panel-heading">
					<h5 class="panel-title">عرض البنرات </h5>
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
						<th>عنوان البنر </th>
						<th>صورة البنر</th>
						<th>رابط البنر</th>
						<th>الاجراء المتخذ</th>
					</tr>
					</thead>
					<tbody class="row_position">
					@foreach($objects as $object)
						<tr parent_id="{{ $object->id }}">
							<td>{{ $object->id }}</td>
							<td>{{ $object->title }}</td>
							<td><img alt="" width="50" height="50" src="/uploads/{{ $object -> photo }}"></td>
							<td>
								@if($object->url)
									<a href="{{ $object->url }}">{{ $object->url }}</a>
								@else
									______
								@endif
							</td>
							<td align="center" class="center"> <ul class="icons-list">
									<li class="text-primary-600"><a href="/admin-panel/banners/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li>
									{{--@if($object->id>2)--}}
									<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/banners/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
									{{--@endif--}}
 												{{--<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> --}}
								</ul>
							</td>
						</tr>
					@endforeach
						@if(count($objects) == 0)
						<tr>
							<td colspan="5"><h6 align="center">لا يوجد بنرات اعلانية بعد</h6></td>
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
		@include('admin.footer')
		<!-- /footer -->

		</div>
		<!-- /content area -->

	</div>
	<!-- /main content -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
{{--	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>--}}

	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


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


		function updateOrder(data) {
			console.log(data);
			$.ajax({
				url:"/admin-panel/change-sort-banners",
				type:'post',
				data:{position:data},
				success:function(){
				}
			})
		}
	</script>

	</body>
	</html>

@stop
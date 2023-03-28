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
					<div class="col-12 col-lg-6 ">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض القاعات</h4>
					</div>
					<div class="col-12 col-lg-6 form-group">

						<select name="provider" id="provider_id" class= 'form-control js-example-placeholder-single select2 '>
							<option value="">اختر صاحب قاعات</option>
							@foreach($providers as $provider)
								<option value="{{$provider->id}}" {{request('provider_id')&&request('provider_id')==$provider->id?'selected':''}}>{{$provider->username}}</option>
							@endforeach
						</select>
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
					<li class="active"><a href="/admin-panel/halls">عرض القاعات</a></li>
				</ul>
				<div style=" text-align: center;" >
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="{{!app('request')->input('status') || app('request')->input('status')=='all' ?"active":""}}"  ><a href="/admin-panel/halls?status=all&&provider_id={{app('request')->input('provider_id')?:''}}">الكل</a></li>
						<li role="presentation" class="{{app('request')->input('status')=='new' ?"active":""}}"  ><a href="/admin-panel/halls?status=new&&provider_id={{app('request')->input('provider_id')?:''}}">جديدة</a></li>
						<li role="presentation"  class="{{app('request')->input('status')=='accepted'?"active":""}}" ><a href="/admin-panel/halls?status=accepted&&provider_id={{app('request')->input('provider_id')?:''}}">مقبولة</a></li>
						<li role="presentation"  class="{{app('request')->input('status')=='stopped'?"active":""}}" ><a href="/admin-panel/halls?status=stopped&&provider_id={{app('request')->input('provider_id')?:''}}">معطلة</a></li>
						<li role="presentation"  class="{{app('request')->input('status')=='refused'?"active":""}}" ><a href="/admin-panel/halls?status=refused&&provider_id={{app('request')->input('provider_id')?:''}}">مرفوضة</a></li>

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

			<!-- Basic example -->
			<div class="panel panel-flat">
				<div class="panel-heading">
					<h5 class="panel-title">عرض القاعات</h5>
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
						<th>صاحب القاعة</th>

						<th>العنوان</th>
						<th> العنوان بالانجليزى</th>

						<th>عدد الكراسى</th>
						<th>الحالة</th>

						<th>الاجراء المتخذ</th>
					</tr>
					</thead>
					<tbody>
					@foreach($objects as $object)
						<tr parent_id="{{ $object->id }}">

							<td>{{ $object->id }}</td>
							<td>{{ $object->provider->username }}</td>
							<td>{{ $object->title }}</td>
							<td>{{ $object->title_en }}</td>
							<td>{{ $object->chairs }}</td>
							<?php
							$status='';
							$color='';
							if($object->status==1){
								$status='مفعلة';
								$color='green';
							}
							elseif ($object->status=='0'){
								$status='قيد المراجعة';
								$color='#ff9800';
							}
							elseif ($object->status=='2'){
								$status='معطلة';
								$color='#3f51b5';
							}
							elseif ($object->status=='3'){
								$status='مرفوضة';
								$color='red';
							}

							?>
							<td style="color:{{$color}}">{{$status}}</td>


							<td align="center" class="center"> <ul class="icons-list">
									<li class="text-primary-600"><a href="/admin-panel/halls/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li>
									@if($object->status==0)
										<li class="text-teal-600 btn-arrow"><a class="btn btn btn-success" style="color:#fff"  href="/admin-panel/accept_hall/{{$object->id}}">قبول</a></li>

										<li class="text-teal-600 "><a class="btn-danger btn" style="color:#fff" href="/admin-panel/refuse_hall/{{$object->id}}">رفض</a></li>



									@elseif($object->status !=0 && $object->status!=3)
									<li class="text-teal-600"><a href="/admin-panel/stop_open_hall/{{$object->id}}"><i class="fa fa-power-off" style="font-size: 17px;
    margin-right: 26px; color: {{$object->status==1?'red':'green'}}"></i></a></li>
										@elseif($object->status==3)
										<li class="text-teal-600 btn-arrow"><a class="btn btn btn-success" style="color:#fff"  href="/admin-panel/accept_hall/{{$object->id}}">قبول</a></li>

								@endif

{{--									<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/halls/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>--}}
									<!--	<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
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
			$(".js-example-placeholder-single").select2({
				placeholder: "اختر صاحب قاعة",
			});
			$(document).on('change', '#provider_id', function (e) {
				var provider_id = $(this).val();
				window.location.href = "/admin-panel/halls?" + "provider_id=" + provider_id+"&&status={{app('request')->input('status')?:''}}"
			});

		});

	</script>
@stop
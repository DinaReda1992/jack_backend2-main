@extends('admin.layout')
@section('js_files')
	<script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/media/fancybox.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
	<script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>


	<script type="text/javascript" src="/assets/js/pages/gallery.js"></script>


@stop
@section('content')


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  عرض المتاجر</h4>
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
							<li class="active"><a href="">عرض المتاجر</a></li>
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
							<h5 class="panel-title">عرض المتاجر</h5>
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
									<th>صاحب المتجر</th>
									<th>اسم المتجر</th>
									<th>عنوان المتجر</th>
									<th>مدينة المتجر المتجر</th>
									<th>رقم السجل</th>
									<th>صورة السجل</th>
									<th>موافقة على المتجر</th>
									{{--<th>القسم الفرعي</th>--}}
									{{--<th>المدينة</th>--}}
									{{--<th>صاحب المكان</th>--}}
									{{--<th>متوسط تقييم المكان</th>--}}
									{{--<th>التعليقات</th>--}}
									{{--<th>عدد الاعجابات</th>--}}
									<th>الاجراء المتخذ</th>
								</tr>
							</thead>
							<tbody>
							@php $i=1 @endphp
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">

									<td>{{ $i }}</td>
									<td>{{ @$object->getUser->username }}</td>
									<td>{{ $object->name }}</td>
									<td>{{ @$object->address }}</td>
									<td>{{ @$object->getState->name }}</td>
									<td>{{ @$object->commercial_registration_no }}</td>
									<td>
										@if($object->photo)

											<div class="thumbnail">
												<div class="thumb">
													<img style="width: 50px;height: 50px;" src="/uploads/{{$object -> photo}}" alt="">
													<div class="caption-overflow">
														<span>
															<a href="/uploads/{{$object -> photo}}" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-plus3"></i></a>
														</span>
													</div>
												</div>
											</div>

										@else
											___________

										@endif
									</td>
									<td>
									@if(@$object->approved==0)
										<a class="btn btn-success" href="/provider-panel/approve_store/{{ $object->id }}">موافقة على المتجر</a>
									@else
										تم الموافقة مسبقا
									@endif
									</td>

									<td align="center" class="center"> <ul class="icons-list">
												<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/provider-panel/stores/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
 										{{--<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>--}}
											</ul>
									</td>
								</tr>
								@php $i++ @endphp
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
			<script src="/site/js/jquery.rateyo.min.js"></script>
			<!-- /main content -->
			<script type="text/javascript">
			 // Warning alert

			</script>
</body>
</html>

@stop

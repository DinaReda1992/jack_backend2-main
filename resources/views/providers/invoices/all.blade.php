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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -  عرض الفواتير</h4>
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
							<li class="active"><a href="">عرض الفواتير</a></li>
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
							<h5 class="panel-title">عرض الفواتير</h5>
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
									<th>صاحب الفاتورة</th>
									<th> البائع</th>
									<th>عدد المنتجات</th>
									<th>حالة الفاتورة</th>
									<th>تفاصيل الفاتورة</th>
								</tr>
							</thead>
							<tbody>
                            @php $i=1 @endphp
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">
									<td>{{ $i }}</td>
									<td>{{ $object->getUser->username }}</td>
                                    <td><a href="/trader/{{ $object->seller_id }}">{{ $object->getSeller->username }}</a></td>
									<td>{{ $object->product_count($object->id) }}</td>
                                    <td>{{ $object->status==1 ? "جاري العمل على الطلب" :  "تم الانتهاء من الطلب" }}</td>
                                    <td><a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;" href="#">عرض تفاصيل الفاتورة</a>
                                        <div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                {!! csrf_field() !!}
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                    aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">تفاصيل الفاتورة </h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>
                                                                        اسم المنتج
                                                                    </th>
                                                                    <th>
                                                                        الكمية
                                                                    </th>
                                                                    <th>
                                                                        السعر
                                                                    </th>
                                                                    <th>
                                                                        السعر بعد الخصم
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php $sum = 0 @endphp
                                                                @foreach($object->getDetails as $detail)
                                                                    @php $sum = $sum+(@$detail->price_discount * @$detail->quantity) @endphp
                                                                    <tr>
                                                                        <td>
                                                                            {{ @$detail->getProduct->title }}
                                                                        </td>
                                                                        <td>
                                                                            {{ @$detail->quantity }}
                                                                        </td>
                                                                        <td>
                                                                            {{ @$detail->price * @$detail->quantity }}
                                                                        </td>
                                                                        <td>
                                                                            {{ @$detail->price_discount * @$detail->quantity }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr>
                                                                    <td colspan="3">
                                                                        الاجمالي :
                                                                    </td>
                                                                    <td>
                                                                        {{ @$sum }}
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

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

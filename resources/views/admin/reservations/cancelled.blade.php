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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -
                        عرض الحجوزات الملغية</h4>
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
                    <li class="active"><a href="">عرض الحجوزات الملغية</a></li>
                </ul>
                <div style=" text-align: center;" >
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="{{app('request')->input('type')=='new' ?"active":""}}"  ><a href="/admin-panel/reservations?type=new">جديدة</a></li>
                        <li role="presentation"  class="{{app('request')->input('type')=='approved'?"active":""}}" ><a href="/admin-panel/reservations?type=approved">المعتمدة</a></li>
                        <li role="presentation"  class="{{app('request')->input('type')=='cancelled'?"active":""}}" ><a href="/admin-panel/reservations?type=cancelled">الملغية</a></li>
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
        @include('admin.message')
        <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">عرض الحجوزات الملغية</h5>
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

                        <th>رقم الحجز</th>
                        <th>صاحب القاعة </th>

                        <th>اسم القاعة</th>
                        <th>صاحب الحجز</th>
                        <th>تاريخ الحجز</th>
                        <th>مبلغ الحجز</th>
                        <th>طريقة الدفع</th>
                        <th>تفاصيل الحجز</th>
{{--                        <th>إعتماد الحجز</th>--}}
                        <th>سبب الإلغاء</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($objects as $object)
                        <tr parent_id="{{ $object->id }}">

                            <td>{{ $object->id }}</td>
                            <td>{{ @$object->hall->provider->username }}</td>
                            <td>{{ @$object->hall->title }}</td>
                            <td>{{ @$object->user->username }}</td>
                            <td>
                                {{ $object->date }}<br>
                                <span>من :  {{ $object->from_time }}</span><br>
                                <span>الى :  {{ $object->to_time }}</span>

                            </td>
                            <td>{{ $object->final_price }} ريال </td>
                            <td>
                                @if($object->payment_method==1)
                                    <a data-toggle="modal" data-target="#bank-transfer{{ $object->id }}"
                                       onclick="return false;"> حوالة بنكية
                                    </a>

                                    <div class="modal fade" id="bank-transfer{{ $object->id }}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">بيانات التحويل</h4>
                                                </div>
                                                <div class="modal-body">
                                                    @if($object->bankTransfer)
                                                        <hr>
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> البنك المحول
                                                                    منه</strong></label>
                                                            <div class="col-sm-8">{{ @$object->bankTransfer->bank_account }}</div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> المبلغ
                                                                    المحول</strong></label>
                                                            <div class="col-sm-8">{{ @$object->bankTransfer->money_transfered }}</div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> الحساب المحول منه</strong></label>
                                                            <div class="col-sm-8">{{ @$object->bankTransfer->account_name }}</div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> رقم الحساب المحول
                                                                    منه</strong></label>
                                                            <div class="col-sm-8">{{ @$object->bankTransfer->account_number }}</div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> صورة
                                                                    التحويل</strong></label>
                                                            <div class="col-sm-8">
                                                                <img src="/uploads/{{ @$object->bankTransfer->photo }}"
                                                                     width="300">

                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                                        إغلاق
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a data-toggle="modal" data-target="#reservation{{ $object->id }}"
                                   onclick="return false;"> تفاصيل الحجز
                                </a>

                                <div class="modal fade" id="reservation{{ $object->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">تفاصيل الحجز</h4>
                                            </div>
                                            <div class="modal-body">
                                                <hr>
                                                <div class="row">
                                                    <label class="col-sm-4"><strong> يوم الحجز</strong></label>
                                                    <div class="col-sm-8">{{ @$object->date }}</div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <label class="col-sm-4"><strong> التوقيت</strong></label>
                                                    <div class="col-sm-8">من {{ $object->from_time }}
                                                        الى {{ $object->to_time }}</div>
                                                </div>
												<hr>
												<div class="row">
													<label class="col-sm-4"><strong> عدد الحضور</strong></label>
													<div class="col-sm-8">{{ $object->number }} شخص</div>
												</div>
                                                <hr>
                                                <div class="row" align="center">
                                                    <label class="col-sm-12"><strong> خصائص مطلوبة</strong></label>
                                                </div>
                                                <div class="row" align="center">
                                                    <label class="col-sm-4"><strong> الخاصية</strong></label>
                                                    <label class="col-sm-4"><strong> العدد </strong></label>
                                                    <label class="col-sm-4"><strong> السعر</strong></label>
                                                </div>
                                                @foreach($object->reservationFeatures as $feature)
                                                    <div class="row" ALIGN="center">
                                                        <label class="col-sm-4"> {{ @$feature->getFeature->name }}</label>
                                                        <label class="col-sm-4">{{ $feature->number }}</label>
                                                        <label class="col-sm-4">{{ $feature->price * $feature->number  }} ريال </label>
                                                    </div>
                                                @endforeach
												<div class="row" align="center">
													<label class="col-sm-8"><strong>إجمالي المبلغ</strong></label>
													<label class="col-sm-4"><strong>{{ $object->final_price }} ريال </strong></label>
												</div>
				<hr>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        <td>{{ $object->reason_of_cancel }}</td>
						</tr>

					@endforeach
					@if(count($objects) == 0)
						<tr>
							<td colspan="8">
								<h5 align="center">
									لا يوجد حجوزات
								</h5>
							</td>
						</tr>
					@endif

                    </tbody>
                </table>
            </div>
			<div>
				{!! $objects->render() !!}
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
                window.location.href = "/admin-panel/reservations?type=cancelled&&" + "provider_id=" + provider_id
            });

        });

    </script>


@stop

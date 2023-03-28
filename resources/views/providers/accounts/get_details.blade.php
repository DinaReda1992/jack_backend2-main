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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span>عرض الحسابات </h4>
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
                    <li class="active"><a href="">عرض الحسابات </a></li>
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
        @include('providers.message')
        <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">عرض الحسابات</h5>
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
                        <th>وقت العملية</th>
                        <th> القاعة</th>
                        <th>رصيدكم من العملية</th>
                        <th>نوع العملية</th>
                        <th>صاحب الحجز</th>
                        <th>تكلفة الحجز</th>
                        {{--<th>حالة الارباح</th>--}}
                        <th>ارباح Meetings</th>
                        <th>تفاصيل الحجز</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=1 @endphp
                    @foreach($objects as $object)
                        <tr parent_id="{{ $object->id }}">
                            <td>{{ $i }}</td>
                            <td>{{ $object->created_at->diffForHumans() }}</td>
                            <td>{{ @$object->reservation->hall->title }}</td>
                            <td>
                                @php
                                    if($object->price < 0){
                                         echo "<span style='color:red;direction:ltr'>".$object->price." ريال</span>";
                                     }else{
                                         echo "<span style='color:green;direction:ltr'>".$object->price." ريال</span>";
                                     }
                                @endphp
                            </td>
                            <td>{{ $object->getType->name }}</td>
                            <td>{{ $object->reservation ? $object->reservation->user->username : "____" }}</td>
                            <td>
                                {{ $object->reservation->final_price }} ريال
                            </td>
                            {{--<td>{{ $object->payment==0 ? 'لم يتم تحصيلها' : 'تم تحصيلها' }}</td>--}}
                            <td>
                            {{ $object->site_profits  }} ريال 
                            <td>
                                @if($object->reservation)
                                    <a data-toggle="modal" data-target="#reservation{{ $object->reservation->id }}"
                                       onclick="return false;"> تفاصيل الحجز
                                    </a>

                                    <div class="modal fade" id="reservation{{ $object->reservation->id }}" tabindex="-1" role="dialog"
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
                                                        <div class="col-sm-8">{{ @$object->reservation->date }}</div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> التوقيت</strong></label>
                                                        <div class="col-sm-8">من {{ $object->reservation->from_time }}
                                                            الى {{ $object->reservation->to_time }}</div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> عدد الحضور</strong></label>
                                                        <div class="col-sm-8">{{ $object->reservation->number }} شخص</div>
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
                                                    @foreach($object->reservation->reservationFeatures as $feature)
                                                        <div class="row" ALIGN="center">
                                                            <label class="col-sm-4"> {{ @$feature->getFeature->name }}</label>
                                                            <label class="col-sm-4">{{ $feature->number }}</label>
                                                            <label class="col-sm-4">{{ $feature->price * $feature->number  }} ريال </label>
                                                        </div>
                                                    @endforeach
                                                    <div class="row" align="center">
                                                        <label class="col-sm-8"><strong>إجمالي المبلغ</strong></label>
                                                        <label class="col-sm-4"><strong>{{ $object->reservation->final_price }} ريال </strong></label>
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

                                @else
                                    _________
                                @endif
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
        @include('providers.footer')
        <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('change','.drag_name',function () {
                var vals = $(this).val();
                var user_id = $(this).attr('user_id');
                $.get('/admin-panel/change_drag_name/'+user_id+'/'+vals,function (data) {

                })
            })
        });
    </script>
    </body>
    </html>

@stop

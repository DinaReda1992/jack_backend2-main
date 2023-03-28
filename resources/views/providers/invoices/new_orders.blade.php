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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -
                        عرض الطلبات الجديدة</h4>
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
                    <li class="active"><a href="">عرض الطلبات الجديدة</a></li>
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
                    <h5 class="panel-title">عرض الطلبات الجديدة</h5>
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
                        <th>نوع الطلب</th>
                        <th>تاريخ الطلب</th>
                        <th>صاحب الطلب</th>
                        <th>الفرع</th>
                        <th>موافقة على الطلب</th>
                        <th>الغاء الطلب</th>
                        <th>عرض الطلب</th>
                        {{--<th>الاجراء المتخذ</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $i=1;
                    @endphp
                    @foreach($objects as $object)
                        <tr parent_id="{{ $object->id }}">

                            <td>{{ $i }}</td>
                            <td>{{ @$object->order_type == 1 ?  "طلب محلي" : "طلب سريع"  }}</td>
                            <td>{{ $object->created_at->diffForHumans() }}</td>
                            <td>{{ @$object->getUser->username }}</td>
                            <td>{{ @$object->getBranch->name }}</td>
                            <td>
                                <a href="/provider-panel/approve_order/{{ $object ->id }}">
                                    <button type="button" name="button" class="btn btn-success"> موافقة على الطلب
                                    </button>
                                </a>
                            </td>
                            <td>
                                <a onclick="return false;" data-toggle="modal"
                                   data-target="#myModal{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-danger"> الغاء الطلب</button>
                                </a>

                                <div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">رسالة إلغاء الطلب</h4>
                                            </div>
                                            <form method="get" action="/provider-panel/cancel_order/{{ $object ->id }}">
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        <textarea name="reason_of_cancel"
                                                                  class="form-control"></textarea>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                       data-dismiss="modal">اغلاق</a>
                                                    <button type="submit" class="btn btn-primary">ارسال</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                            <td><a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
                                   href="/project-details/{{ $object -> id }}"> عرض </a>

                                <div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table style="" class="table">
                                                        <tr>
                                                            <th>اسم المنتج</th>
                                                            <th>السعر</th>
                                                            <th>الكمية</th>
                                                            <th>الحجم</th>
                                                            <th>ملاحظات</th>
                                                        </tr>
                                                        @foreach($object->getDetails as $detail)
                                                            <tr>
                                                                <td>
                                                                    {{ @$detail->getProduct->name }}
                                                                </td>
                                                                <td>
                                                                    {{ @$detail->price }}
                                                                </td>
                                                                <td>
                                                                    {{ @$detail->quantity }}
                                                                </td>
                                                                <td>
                                                                    {{ @$detail->size }}
                                                                </td>
                                                                <td>
                                                                    {{ @$detail->notes }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                            {{--<td align="center" class="center"> <ul class="icons-list">--}}
                            {{--<li class="text-primary-600"><a href="/edit-project/{{ $object->id }}"><i class="icon-pencil7"></i></a></li>--}}
                            {{--<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/provider-panel/projects/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>--}}
                            {{--<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>--}}
                            {{--</ul>--}}
                            {{--</td>--}}
                        </tr>
                        @php
                            $notification = \App\Models\Notification::where('type',0)->where('status',0)->where('order_id',$object->id)->first();
                            if($notification){
                            $notification->status=1;
                            $notification->save();
                            }
                        @endphp
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

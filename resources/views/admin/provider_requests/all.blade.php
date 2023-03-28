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
    <link rel="stylesheet" href="/site/css/jquery.rateyo.min.css">


@stop
@section('content')


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -
                        عرض طلبات الانضمام كمورد </h4>
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
                    <li class="active"><a href="">عرض طلبات الانضمام كمورد </a></li>
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
                    <h5 class="panel-title">عرض طلبات الانضمام كمورد </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                    <div style=" text-align: center;">
                        <ul class="nav nav-tabs" role="tablist">
                            <li
                                class="{{ !app('request')->input('status') || app('request')->input('status') == 'all' ? 'active' : '' }}">
                                <a href="/admin-panel/provider-requests">الكل</a>
                            </li>
                            <li class="{{ app('request')->input('status') == 'waiting' ? 'active' : '' }}"><a
                                    href="/admin-panel/provider-requests?status=waiting">فى انتظار التأكيد</a></li>
                            <li class="{{ app('request')->input('status') == 'accepted' ? 'active' : '' }}"><a
                                    href="/admin-panel/provider-requests?status=accepted">تم التأكيد</a></li>
                            <li class="{{ app('request')->input('status') == 'canceled' ? 'active' : '' }}"><a
                                    href="/admin-panel/provider-requests?status=canceled">ملغية</a></li>

                        </ul>
                    </div>

                </div>


                <table class="table datatable-colvis-basic">
                    <thead>
                        <tr>
                            <th>الرقم التعريفي</th>
                            <th>الاسم</th>
                            <th>اسم الموظف</th>
                            <th>رقم الجوال</th>
                            <th>المنطقة</th>
                            <th>الحالة</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($objects as $object)
                            @php
                                $status = '';
                                $color = '';
                                
                                if ($object->status == 0) {
                                    $status = 'انتظار';
                                    $color = '#1c43b1';
                                } elseif ($object->status == 2) {
                                    $status = 'ملغى';
                                    $color = 'red';
                                } elseif ($object->status == 1) {
                                    $status = 'تم التاكيد';
                                    $color = 'green';
                                }
                            @endphp
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ $object->name }}</td>
                                <td>{{ @$object->employee->username }}</td>
                                <td style="direction: ltr">966 {{ $object->phone }}</td>
                                <td>{{ @$object->region->name }}
                                    - {{ @$object->state->name }}</td>
                                <td><span style="color: {{ $color }}">{{ $status }}</span></td>
                                <td>
                                    @if ($object->status == 0 || $object->status == 2)
                                        <div class="btn btn-success">
                                            <a data-toggle="modal" data-target="#offers{{ $object->id }}"
                                                onclick="return false;" href="#" style="color: #fff;"><i
                                                    class="icon-check"></i> موافقة
                                            </a>

                                        </div>

                                        <div class="modal fade" id="offers{{ $object->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">موافقة على الطلب</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>
                                                            هل انت متأكد من قبول الطلب ؟
                                                        </p>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                                            إغلاق
                                                        </button>
                                                        <a href="/admin-panel/approve-provider-request/{{ $object->id }}"
                                                            class="btn btn-success">نعم </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($object->status == 0)
                                        <div class="btn btn-danger">
                                            <a onclick="return false;" data-toggle="modal"
                                                data-target="#myModal{{ $object->id }}555" href="#"
                                                style="color: #fff;">
                                                <i class="icon-cancel-square"></i> الغاء الطلب
                                            </a>
                                        </div>

                                        <div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1"
                                            role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="modal-title" id="myModalLabel">رسالة إلغاء الطلب</h4>

                                                    </div>
                                                    <form method="post"
                                                        action="/admin-panel/cancel_provider_request/{{ $object->id }}">
                                                        {!! csrf_field() !!}

                                                        <div class="modal-body">

                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"></label>
                                                                <textarea name="reason_of_cancel" class="form-control" placeholder="سبب الغاء الطلب"></textarea>
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
                                    @endif


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
    <script src="/site/js/jquery.rateyo.min.js"></script>
    <!-- /main content -->
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '.drag_name', function() {
                var vals = $(this).val();
                var user_id = $(this).attr('user_id');
                $.get('/admin-panel/change_drag_name/' + user_id + '/' + vals, function(data) {

                })
            })
        });
    </script>
    </body>

    </html>

@stop

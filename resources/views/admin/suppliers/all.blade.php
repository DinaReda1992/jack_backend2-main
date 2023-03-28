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
    <script type="text/javascript" src="/assets/js/notify.js"></script>

    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>
    <script>
        $(document).on("click", ".switchery", function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var id = $(this).parent().find('input').attr('object_id');
            var d_url = $(this).parent().find('input').attr('delete_url');

            $.ajax({
                url: d_url,
                type: 'post',
                data: '',
                success: function() {
                    notify.initialization("تم تغيير حالة المورد بنجاح ", "success");

                },
                error: function() {
                    notify.initialization("حدث خطأ غير متوقع . ", "failed");

                }
            })
        });
    </script>


@stop
@section('content')


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض
                        الموردين <a class="btn btn-success" href="/admin-panel/export-excel-suppliers">تصدير اكسيل</a>
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/suppliers/create"><button type="button" class="btn btn-success"
                                name="button"> اضافة جديد +</button></a>
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
                    <li class="active"><a href="">عرض الموردين </a></li>
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
                    <h5 class="panel-title">عرض الموردين </h5>
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
                            <th>اسم العضو</th>
                            {{-- <th>رسائل العضو</th> --}}
                            <th>رقم الجوال</th>
                            <th>البريد الالكتروني</th>
                            <th>عدد المنتجات</th>
                            <th>الحالة</th>

                            <th>
                                حظر العضو
                            </th>
                            {{-- <th>عدد طلبات العضو</th> --}}
                            {{-- <th>ترقية العضوية</th> --}}
                            <th>الاجراء المتخذ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($objects as $object)
                            {{--								@php --}}
                            {{--									$rate_maners = \App\Models\UserRating::where('rated_user',$object->id)->avg('rate'); --}}
                            {{--									$rate_maners = $rate_maners ? round($rate_maners) : 0; --}}
                            {{--								@endphp --}}
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ $object->username }}</td>
                                <td style="direction: ltr">+({{ $object->phonecode }}){{ $object->phone }}</td>
                                <td>{{ $object->email }}</td>

                                <td><a href="/admin-panel/products?user_id={{ $object->id }}">({{ $object->products->count() }})
                                        منتج</a></td>
                                <td>
                                    <div class="checkbox checkbox-switchery switchery-sm switchery-double">
                                        <input type="checkbox" object_id="{{ $object->id }}"
                                            delete_url="/admin-panel/stop_supplier/{{ $object->id }}"
                                            class="switchery sweet_switch"
                                            {{ $object->supplier && @$object->supplier->stop == 0 ? 'checked' : '' }} />
                                    </div>
                                </td>

                                {{-- <td><a href="/show-user-messages/{{ $object->id }}"> {{ \App\Models\Messages::where('reciever_id',$object->id)->orwhere('sender_id',$object->id)->count() }} رسائل</a></td> --}}
                                {{-- <td> --}}
                                {{-- {!!   $object->activate == 1 ? "مفعل" : '<a href="/admin-panel/suppliers/active_user/'.$object->id.'"> --}}
                                {{-- <button type="button" name="button" class="btn btn-success"> تفعيل رقم العصو</button> --}}
                                {{-- </a>' !!} --}}
                                {{-- </td> --}}

                                {{--									<td > --}}
                                {{--										<div user_id="{{ $object->id }}" id="rateYo{{ $object->id }}" style="margin: auto;"></div> --}}
                                {{--										<script> --}}
                                {{--											window.addEventListener('load',function () { --}}
                                {{--												$('#rateYo{{ $object->id }}').rateYo({ --}}
                                {{--													rating: {{ $rate_maners }}, --}}
                                {{--													starWidth: '20px', --}}
                                {{--													fullStar: true, --}}
                                {{--													readOnly: true, --}}
                                {{--													rtl:true, --}}
                                {{--												}); --}}

                                {{--											}); --}}
                                {{--										</script> --}}
                                {{--									</td> --}}


                                <td>
                                    @if ($object->id != 235)
                                        <a href="/admin-panel/suppliers/block_user/{{ $object->id }}">
                                            <button type="button" name="button"
                                                class="btn {{ $object->block == 1 ? 'btn-success' : 'btn-danger' }}">
                                                {{ $object->block == 1 ? 'فك الحظر' : 'حظر العضو' }}</button>
                                        </a>
                                    @endif
                                </td>
                                {{-- <td>{{ $object->user_type_id==3 ? $object->offer_count($object->id) . " عرض"  :  $object->project_count($object->id) . " مشروع"  }}</td> --}}
                                {{-- <td>{{ $object->orders_count($object->id) }}</td> --}}
                                {{-- <td> --}}
                                {{-- @foreach (\App\Models\Packages::all() as $package) --}}
                                {{-- <a class="btn btn-success" href="/admin-panel/adv_user_package/{{ $object->id }}/{{ $package->id }}">{{ $package->name }}</a> --}}
                                {{-- @endforeach --}}
                                {{-- </td> --}}
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-primary-600"><a
                                                href="/admin-panel/suppliers/{{ $object->id }}/edit"><i
                                                    class="icon-pencil7"></i></a></li>
                                        @if ($object->id != 235)
                                            <li class="text-danger-600"><a onclick="return false;"
                                                    object_id="{{ $object->id }}"
                                                    delete_url="/admin-panel/suppliers/{{ $object->id }}"
                                                    class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
                                        @endif
                                        <!--		<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
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

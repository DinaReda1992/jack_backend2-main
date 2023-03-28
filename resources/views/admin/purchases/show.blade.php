@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <link rel="stylesheet" href="/assets/css/jquery.fancybox.min.css" type="text/css" media="screen" />
    <script type="text/javascript" src="/assets/js/jquery.fancybox.min.js"></script>


@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> -
                        المخزن</h4>
                </div>

            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li><a href="/admin-panel/purchases-orders">طلبات الشراء</a></li>
                    <li class="active">طلب رقم {{ $object->id }} </li>
                </ul>

            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

            @include('admin.message')

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">طلب رقم {{ $object->id }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">
                    <div class="w-100" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog1" role="document">
                            <div class="modal-content">
                                <div class="modal-header pb-3">

                                    <h4 class="modal-title pull-left" id="myModalLabel">تفاصيل الطلب</h4>
                                    <div class="pull-right">

                                        @if ($object->status != 5)
                                            <span class=" mx-5">
                                                حالة الطلب:
                                                <span class=" ">
                                                    {{ @$object->orderStatus->name }}
                                                </span>
                                            </span>
                                        @endif
                                        @if ($object->status == 5)
                                            <span style="color:red">تم الغاء الطلب</span>
                                        @else
                                            @if ($object->status == 7)
                                                <span style="color:green">مكتمل </span>
                                                {{-- @elseif(in_array($object->status, [1, 2, 3, 4, 6]))
                                                <span>تغيير إلي</span>
                                                <a onclick="return false;" data-toggle="modal"
                                                    data-target="#myModal{{ $object->id }}555" href="#">
                                                    <button type="button" name="button" class="btn btn-primary">
                                                        {{ $object->orderStatus->btn_text }}</button>
                                                </a>
                                                <div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel">رسالة تغيير حالة
                                                                    الطلب</h4>
                                                            </div>
                                                            <form method="post"
                                                                action="/admin-panel/purchases-orders/change_order_status/{{ $object->id }}">
                                                                <div class="modal-body">

                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1"></label>
                                                                        هل انت متأكد من تغيير حالة هذا الطلب الى
                                                                        ({{ $object->orderStatus->btn_text }})
                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a type="button" class="btn btn-default"
                                                                        data-dismiss="modal">اغلاق</a>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">ارسال</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            @endif

                                        @endif

                                        {{-- @if ($object->status != 7)

													<a  href="/admin-panel/warehouse/approve_order/{{$object->id}}">
														<button type="button" name="button" class="btn btn-primary">
															{{@$object->orderStatus->btn_text}}
														</button>
													</a>
												@endif --}}

                                        <a href="/admin-panel/purchases-orders/{{ $object->id }}/edit" target="_blank"
                                            class="btn btn-default "><i class="fa fa-print"></i> Print</a>
                                    </div>
                                </div>
                                <div class="modal-body">


                                    <div class="row">
                                        <label class="col-sm-4"><strong>المورد</strong></label>

                                        <div class="col-sm-12">
                                            <div class="box-body box-profile">
                                                <img class="profile-user-img img-responsive img-circle"
                                                    src="/uploads/{{ @$object->provider->photo ?: 'default-user.png' }}"
                                                    style="height: 100px;" alt="User profile picture">
                                                <h3 class="profile-username text-center">{{ @$object->provider->username }}
                                                </h3>
                                                <p class="text-muted text-center">{{ @$object->provider->phone }}</p>
                                                <div class="text-center">
                                                    {{-- @if ($object->sent_sms == 0)
																<p  class="text-center">تم ارسال الفاتورة للعميل</p>
																<a  href="/admin-panel/orders/send-invoice/{{$object->id}}">
																	<button type="button" name="button" class="btn btn-primary">
																		إرسال مرة اخري
																	</button>
																</a>
															@else
																<a  href="/admin-panel/orders/send-invoice/{{$object->id}}">
																	<button type="button" name="button" class="btn btn-primary">
																		إرسال الفاتورة للعميل
																	</button>
																</a>
															@endif --}}
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <label class="col-sm-4"><strong> السائق</strong></label>
                                        <div class="col-sm-8">
                                            @if ($object->driver != null)
                                                <a
                                                    href="{{ '/admin-panel/drivers/' . $object->driver_id . '/edit' }}">{{ @$object->driver->username }}</a>
                                                {{-- @if ($object->status < 4)
                                                    <form
                                                        action="/admin-panel/purchases-orders/select-driver/{{ $object->id }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group mt-3">
                                                            <label> تغيير السائق</label>
                                                            <select required class="form-control select2" name="driver_id">
                                                                @foreach (\App\Models\User::where(['user_type_id' => 6, 'is_archived' => 0])->select('id', 'username')->get() as $driver)
                                                                    <option value="{{ $driver->id }}">
                                                                        {{ $driver->id . ' - ' . $driver->username }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-primary">حفظ</button>
                                                        </div>
                                                    </form>
                                                @endif --}}
                                                {{-- @else
                                                <form
                                                    action="/admin-panel/purchases-orders/select-driver/{{ $object->id }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group mt-3">
                                                        <label> اختر السائق</label>
                                                        <select required class="form-control select2" name="driver_id">
                                                            <option></option>
                                                            @foreach (\App\Models\User::where(['user_type_id' => 6, 'is_archived' => 0])->select('id', 'username')->get() as $driver)
                                                                <option value="{{ $driver->id }}">
                                                                    {{ $driver->id . ' - ' . $driver->username }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary">حفظ</button>
                                                    </div>
                                                </form> --}}
                                            @endif

                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <label class="col-sm-4"><strong> طريقة الدفع </strong></label>
                                        <div class="col-sm-8">
                                            <p>{{ @$object->paymentMethod->name }}</p>
                                            {{-- @if ($object->payment_method == 4)
													<img width="300" src="/uploads/{{@$object->transfer_photo->photo}}" />
													@endif --}}
                                            @if ($object->transfer_photo != '')
                                                @php
                                                    $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                                                    $contentType = @mime_content_type('uploads/' . @$object->transfer_photo);
                                                    
                                                @endphp
                                                @if (in_array($contentType, $allowedMimeTypes))
                                                    <img data-fancybox data-caption="صورة التحويل" width="300"
                                                        src="/uploads/{{ @$object->transfer_photo }}" />
                                                @else
                                                    <a target="_blank" href="/uploads/{{ @$object->transfer_photo }}">شاهد
                                                        الملف</a>
                                                @endif

                                            @endif

                                            <form action="/admin-panel/purchases-orders/upload-invoice/{{ $object->id }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group mt-3">
                                                    <label> اضافة ايصال السداد</label>
                                                    <input class="form-control" type="file" name="photo" />
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">حفظ</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <hr>


                                    <div class="row">
                                        <label class="col-sm-4"><strong> شروط الدفع</strong></label>
                                        <div class="col-sm-8">
                                            {{ @$object->paymentTerm->name }}
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <label class="col-sm-4"><strong> سعر الطلب</strong></label>
                                        <div class="col-sm-8">
                                            {{ @$object->final_price }} ريال
                                        </div>
                                    </div>

                                    <hr>
                                    @if ($object->purchase_item->count() > 0)
                                        <div class="row">
                                            <label class="col-sm-12"><strong> تفاصيل الطلب</strong></label>
                                            <div class="col-sm-12">
                                                <table class="table table-bordered text-center">
                                                    <tr style="background: #e8e8e8">
                                                        <th>المنتج</th>
                                                        <th>الكمية المطلوبة</th>
                                                        <th>الكمية المستلمة</th>
                                                        <th>فرق الكمية</th>
                                                        <th>سعر الوحدة</th>
                                                        <th>السعر للكمية المطلوبة</th>
                                                        <th>السعر للكمية المستلمة</th>
                                                        <th>فرق السعر</th>
                                                    </tr>
                                                    @php
                                                        $total_difference = 0;
                                                    @endphp
                                                    @foreach ($object->purchase_item as $key2 => $item)
                                                        <tr>
                                                            <td>{{ @$item->product->title }}</td>
                                                            <td>{{ @$item->quantity }} </td>
                                                            <td>{{ @$item->delivered_quantity }} </td>
                                                            <td>{{ @$item->quantity - @$item->delivered_quantity }} </td>
                                                            <td>{{ @$item->price }} ريال </td>
                                                            <td>{{ @$item->price * $item->quantity }} ريال </td>
                                                            <td>{{ @$item->price * $item->delivered_quantity }} ريال </td>
                                                            <td>{{ @$item->price * (@$item->quantity - @$item->delivered_quantity) }}
                                                                ريال </td>
                                                        </tr>
                                                        @php
                                                            $total_difference += @$item->price * (@$item->quantity - @$item->delivered_quantity);
                                                        @endphp
                                                    @endforeach

                                                    <tr style="background: #daefff">
                                                        <td colspan="5">المجموع</td>
                                                        <td>{{ $object->order_price }} ريال </td>
                                                        <td colspan="2" class="text-center">{{ $total_difference }}ريال </td>
                                                    </tr>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-12"><strong> </strong></label>
                                            <div class="col-sm-12">
                                                <h3>اجمالي الطلب</h3>
                                                <table class="table table-bordered">
                                                    <tr style="background: #e8e8e8">
                                                        <td colspan="4">تكلفة المنتجات</td>
                                                        <td>{{ $object->order_price }} ريال </td>
                                                    </tr>

                                                    @if ($object->taxes > 0)
                                                        <tr style="background: #e8e8e8">
                                                            <td colspan="4">الضريبة</td>
                                                            <td> {{ $object->taxes }} ريال </td>
                                                        </tr>
                                                    @endif

                                                    <tr style="background: #daefff">
                                                        <td colspan="4">المجموع</td>
                                                        <td>{{ $object->final_price }} ريال </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
@stop

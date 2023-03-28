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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> - الطلبات</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li><a href="/admin-panel/incomplete-orders">عرض الطلبات غير المكتملة</a></li>
                </ul>

            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

            @include('admin.message')

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="w-100" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog1" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">تفاصيل </h4>
                                    <div class="pull-right">
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <label class="col-sm-4"><strong> سعر المنتجات</strong></label>
                                        <div class="col-sm-8">
                                            {{ @$total }} {{__('dashboard.sar')}}
                                        </div>
                                    </div>

                                    <hr>
                                    @if ($object->cart_items)
                                        <div class="row">
                                            <label class="col-sm-12"><strong> {{__('dashboard.order_details')}}</strong></label>
                                            <div class="col-sm-8">
                                                <table class="table table-bordered text-center">
                                                    <tr style="background: #e8e8e8">
                                                        <th>{{__('dashboard.product')}}</th>
                                                        <th>{{__('dashboard.quantity')}}</th>
                                                        <th>{{__('dashboard.unit_price')}}</th>
                                                        <th>السعر الكلي</th>

                                                    </tr>
                                                    @foreach ($object->cart_items as $key => $item)
                                                        <tr>
                                                            <td>{{ @$item->product->title }}</td>
                                                            <td>{{ @$item->quantity }} </td>
                                                            <td>{{ @$item->price }} {{__('dashboard.sar')}}</td>
                                                            <td>{{ @$item->price * $item->quantity }} {{__('dashboard.sar')}}</td>

                                                        </tr>
                                                    @endforeach

                                                    <tr style="background: #daefff">
                                                        <td colspan="3">{{__('dashboard.products_price')}}</td>
                                                        <td>{{ $total }} {{__('dashboard.sar')}}</td>
                                                    </tr>
                                                </table>

                                            </div>

                                        </div>
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

@extends('providers.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/visualization/d3/d3.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/ui/moment/moment.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/pickers/daterangepicker.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/dashboard.js"></script>
    <!-- /theme JS files -->

@stop
@section('content')
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">الرئيسية</span> -
                        لوحة التحكم</h4>
                </div>

                {{--<div class="heading-elements">--}}
                {{--<div class="heading-btn-group">--}}
                {{--<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>--}}
                {{--<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>--}}
                {{--<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>--}}
                {{--</div>--}}
                {{--</div>--}}
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li class="active">لوحة التحكم</li>
                </ul>

                {{-- <ul class="breadcrumb-elements">
                    <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-gear position-left"></i>
                            Settings
                            <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
                            <li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
                            <li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><i class="icon-gear"></i> All settings</a></li>
                        </ul>
                    </li>
                </ul> --}}
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
        @include('admin.message')
        <!-- Main charts -->
            <div class="row">

                <div class="col-lg-12">
                    <!-- Sales stats -->
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h6 class="panel-title">احصائيات لوحة تحكم المتاجر </h6>

                        </div>

                        <div class="container-fluid">
                            <div class="row text-center">

                                {{--
                                                                {{--<div class="col-md-3">--}}
                                {{--<a href="/admin-panel/new_orders">--}}
                                {{--<div class="content-group">--}}
                                {{--<h5 class="text-semibold no-margin"><i class="glyphicon glyphicon-envelope position-left text-slate"></i>{{ \App\Models\Messages::where('status',0)->where('reciever_id',0)->count() }}</h5>--}}
                                {{--<span class="text-muted text-size-small">اشعارات الرسائل الجديدة</span>--}}
                                {{--</div>--}}
                                {{--</a>--}}
                                {{--</div>--}}

                                @if(auth()->user()->user_type_id==3)
                                    <?php $privileges = \App\Models\Privileges::where('is_provider', 1)->where('model', '!=', "")->where("hidden", 0)->orderBy('orders', 'ASC')->get(); ?>
                                @elseif(Auth::User()->user_type_id==2 && !empty(Auth::user()->privilege_id))
                                    <?php
                                    $pr = \App\Models\PrivilegesGroupsDetails::where('privilege_group_id', Auth::user()->privilege_id)->pluck('privilege_id')->toArray();
                                    $privileges = \App\Models\Privileges::whereIn("id", $pr)->where('is_provider', 1)->where('model', '!=', "")->where("parent_id", 0)->orderBy('orders', 'ASC')->get();
                                    ?>
                                @else
                                    <?php
                                    $privileges = \App\Models\Privileges::where('is_provider', 1)->where('model', '!=', "")->orderBy('orders', 'ASC')->get();
                                    ?>
                                @endif
                                @if(count($privileges)>0)
                                    @foreach($privileges as $privilege)
                                        <div class="col-lg-3 col-xs-6">
                                            <!-- small box -->
                                            <div class="small-box bg-aqua"
                                                 style="background-color: {{$privilege->card_color}} !important;">
                                                <div class="inner">
                                                    <h3>@php
                                                            @eval("echo $privilege->model;");
                                                        @endphp
                                                    </h3>
                                                    <p>{{$privilege->privilge}}</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="{{$privilege->icon}}"
                                                       style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                                </div>
                                                <a href="@if($privilege->url) {{ $privilege->url }}   @else {{ @\App\Models\Privileges::where('parent_id',$privilege->id)->orderBy('id','desc')->first()->url }} @endif"
                                                   class="small-box-footer">More info <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div><!-- ./col -->
                                    @endforeach
                                @endif
                            </div>


                        </div>
                        <div class="chart content-group-sm" id="app_sales"></div>
                        <div class="chart" id="monthly-sales-stats"></div>
                    </div>
                    <!-- /sales stats -->
                </div>
            </div>
            <!-- /main charts -->
            <!-- Footer -->
        @include('providers.footer')
        <!-- /footer -->
        </div>
        <!-- /content area -->
    </div>
@stop

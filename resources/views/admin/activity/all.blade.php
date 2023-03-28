@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
    @else
        <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis_en.js"></script>
    @endif
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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">{{__('dashboard.dashboard')}}</span> - {{__('dashboard.employees_activity')}}
                    </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{__('dashboard.home')}}</a></li>
                    <li class="active"><a href="">{{__('dashboard.employees_activity')}}</a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{__('dashboard.employees_activity')}}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <table class="table datatable-colvis-basic text-center" data-order='[[ 0, "desc" ]]'>
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.employee') }}</th>
                            <th>{{ __('dashboard.message') }}</th>
                            <th>{{ __('dashboard.order_id') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ @$object->created_at->translatedFormat('Y-m-d h:i A') }}</td>
                                <td>{{ @$object->getSender->username }}</td>
                                <td>{{ @$object->message }}</td>
                                <td>{{ @$object->order_id }}</td>
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

@stop

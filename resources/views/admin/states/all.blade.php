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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> - {{ __('dashboard.view_cities') }}
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/states/create"><button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.add_new') }} +</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_cities') }}</a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_cities') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <table class="table datatable-colvis-basic text-center" data-page-length='25'>
                    <thead>
                        <tr>

                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.city_name_ar') }}</th>
                            <th>{{ __('dashboard.city_name_en') }}</th>
                            <th>{{ __('dashboard.route_code') }}</th>
                            <th>{{ __('dashboard.country') }}</th>
                            <th>{{ __('dashboard.region') }}</th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">

                                <td>{{ $object->id }}</td>
                                <td>{{ $object->name }}</td>
                                <td>{{ $object->name_en }}</td>
                                <td>{{ $object->routeCode }}</td>

                                <td>{{ @$object->getCountry->name }}</td>
                                <td>{{ @$object->getRegion->name }}</td>

                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-primary-600"><a
                                                href="/admin-panel/states/{{ $object->id }}/edit"><i
                                                    class="icon-pencil7"></i></a></li>
                                        <li class="text-danger-600"><a onclick="return false;"
                                                object_id="{{ $object->id }}"
                                                delete_url="/admin-panel/states/{{ $object->id }}" class="sweet_warning"
                                                href="#"><i class="icon-trash"></i></a></li>
                                        <!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
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
    <!-- /main content -->
    <script type="text/javascript">
        // Warning alert
    </script>
    </body>

    </html>

@stop

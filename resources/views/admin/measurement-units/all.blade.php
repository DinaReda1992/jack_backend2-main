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
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> -
                        {{ __('dashboard.view_units_of_measurement') }} </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/measurement-units/create"><button type="button" class="btn btn-success"
                                name="button"> {{ __('dashboard.add_new') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_units_of_measurement') }} </a></li>
                </ul>

            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_units_of_measurement') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <table class="table datatable-colvis-basic text-center">
                    <thead>
                        <tr>

                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.unit_of_measurement_ar') }} </th>
                            <th>{{ __('dashboard.unit_of_measurement_en') }}</th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody class="row_position">
                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ $object->name }}</td>
                                <td>{{ $object->name_en }}</td>
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-primary-600"><a
                                                href="/admin-panel/measurement-units/{{ $object->id }}/edit"><i
                                                    class="icon-pencil7"></i></a></li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    {{--	<script src="https://code.jquery.com/jquery-1.12.4.js"></script> --}}

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    </script>


@stop

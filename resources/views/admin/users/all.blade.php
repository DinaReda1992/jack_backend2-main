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
    <link rel="stylesheet" href="/site/css/jquery.rateyo.min.css">


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
                        {{ __('dashboard.client_registration_requests') }} </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.client_registration_requests') }} </a></li>
                </ul>

            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            @include('admin.message')
            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.client_registration_requests') }} </h5>
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
                            <th>{{ __('dashboard.member_name') }}</th>
                            <th>{{ __('dashboard.type_of_membership') }}</th>
                            <th>{{ __('dashboard.phone') }}</th>
                            <th>{{ __('dashboard.email') }}</th>
                            <th>{{ __('dashboard.block_member') }}</th>
                            <th>{{ __('dashboard.addresses') }}</th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ $object->username }}</td>
                                <td>
                                    @if ($object->user_type_id == 3)
                                        {{ __('dashboard.supplier') }}
                                    @elseif($object->user_type_id == 4)
                                        {{ __('dashboard.superviser') }}
                                    @elseif($object->user_type_id == 5)
                                        {{ __('dashboard.client') }}
                                    @endif
                                </td>
                                <td style="direction: ltr">+({{ $object->phonecode }}){{ $object->phone }}</td>
                                <td>{{ $object->email }}</td>
                                <td>
                                    @if ($object->id != 235)
                                        <a href="/admin-panel/all-users/block_user/{{ $object->id }}">
                                            <button type="button" name="button"
                                                class="btn {{ $object->block == 1 ? 'btn-success' : 'btn-danger' }}">
                                                {{ $object->block == 1 ? __('dashboard.unblock') : __('dashboard.block') }}</button>
                                        </a>
                                    @endif
                                </td>
                                <td align="center" class="center">
                                    @if ($object->user_type_id == 5)
                                        <ul class="icons-list">
                                            <li class=""><a type="button" class="text-white btn btn-success"
                                                    href="/admin-panel/all-users/addresses/{{ $object->id }}">{{ __('dashboard.add_address') }}</a>
                                            </li>
                                        </ul>
                                    @endif
                                </td>
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-primary-600"><a
                                                href="/admin-panel/all-users/{{ $object->id }}/edit"><i
                                                    class="icon-pencil7"></i></a></li>
                                        @if ($object->id != 235)
                                            <li class="text-danger-600"><a onclick="return false;"
                                                    object_id="{{ $object->id }}"
                                                    delete_url="/admin-panel/all-users/{{ $object->id }}"
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

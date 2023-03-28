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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> -
                        {{ __('dashboard.communication_messages') }} </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }} </a>
                    </li>
                    <li class="active"><a href="">{{ __('dashboard.view') }} -
                            {{ __('dashboard.communication_messages') }} </a></li>
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
                    <h5 class="panel-title">{{ __('dashboard.view') }} - {{ __('dashboard.communication_messages') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                @php
                    $i = 1;
                @endphp
                <table class="table datatable-colvis-basic text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('dashboard.message_title') }}</th>
                            <th>{{ __('dashboard.message') }}</th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $i }}</td>
                                <td>{{ @$object->subject }}</td>
                                {{--							<td>{{ @$object->email }}</td> --}}
                                {{--							<th>{{ @$object->complain==0?'رسالة تواصل':'شكوى' }}</th> --}}
                                {{--							<td>{{ @$object->complain==1?@$object->getContactType->name:'______' }}</td> --}}
                                <td>{{ $object->message }}</td>
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-danger-600"><a onclick="return false;"
                                                object_id="{{ $object->id }}"
                                                delete_url="/admin-panel/contacts/{{ $object->id }}"
                                                class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
                                    </ul>
                                </td>
                            </tr>
                            @php
                                $object->status = 1;
                                $object->save();
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

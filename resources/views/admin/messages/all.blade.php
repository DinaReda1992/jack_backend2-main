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
                        {{ __('dashboard.view_messages') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_messages') }}</a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_messages') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                    <div style=" text-align: center;">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="{{ !app('request')->input('status') ? 'active' : '' }}"><a
                                    href="/admin-panel/messages">{{ __('dashboard.all') }} </a></li>

                            <li class="{{ app('request')->input('status') == 'opened' ? 'active' : '' }}"><a
                                    href="/admin-panel/messages?status=opened">{{ __('dashboard.open_tickets') }} </a></li>
                            <li class="{{ app('request')->input('status') == 'closed' ? 'active' : '' }}"><a
                                    href="/admin-panel/messages?status=closed">{{ __('dashboard.close_tickets') }} </a>
                            </li>

                        </ul>
                    </div>
                </div>


                <table class="table datatable-colvis-basic text-center">
                    <thead>
                        <tr>
                            <th style="display: none;">{{ __('dashboard.id') }}</th>

                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.time_send') }}</th>
                            <th>{{ __('dashboard.order_id') }}</th>
                            <th>{{ __('dashboard.last_message') }}</th>
                            <th>{{ __('dashboard.user_name') }}</th>
                            <th>{{ __('dashboard.ticket_status') }}</th>
                            <th>{{ __('dashboard.view') }}</th>
                            <th>{{ __('dashboard.delete') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($objects as $object)
                            @php
                                if ($object->status == 0) {
                                    $msg = \App\Models\Messages::find($object->id);
                                    $msg->status = 1;
                                    $msg->save();
                                }
                            @endphp
                            <tr parent_id="{{ $object->id }}">
                                <td style="display: none">{{ $i }}</td>
                                <td>{{ $object->id }}</td>
                                <td>{{ $object->created_at->diffForHumans() }}</td>
                                <td>{{ @$object->getTicket->order_id }}</td>
                                <td>{{ @$object->message }}</td>
                                <td>{{ $object->reciever_id != 1 ? @$object->getRecieverUser->username . ' ' : @$object->getSenderUser->username . ' ' }}
                                </td>
                                <td>{!! @$object->getTicket->closed == 0
                                    ? "<span style='color:green'>".__('dashboard.opened')."</span>"
                                    : "<span style='color:red'>".__('dashboard.closed')."</span>" !!}</td>
                                <td align="center" class="center">
                                    <a
                                        href="/admin-panel/message/{{ $object->ticket_id }}">{{ @$object->getTicket->name }}</a>
                                </td>
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-danger-600"><a
                                                href="/admin-panel/delete-ticket/{{ $object->ticket_id }}"><i
                                                    class="icon-trash"></i></a></li>
                                        <!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
                                    </ul>
                                </td>

                            </tr>
                            @php
                                $i++;
                            @endphp
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
    <script type="text/javascript">
        // Warning alert
        $(document).ready(function() {
            $(document).on('change', '.car_order', function() {
                var car_id = $(this).attr('car_id');
                var order = $(this).val();
                $.get('/admin-panel/save_order_car/' + car_id + "/" + order, function(data) {

                })
            })
        })
    </script>
    </body>

    </html>

@stop

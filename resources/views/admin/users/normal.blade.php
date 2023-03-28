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
    <link rel="stylesheet" href="/site/css/jquery.rateyo.min.css">

    <!-- /theme JS files -->
@stop
@section('content')


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4 style="display: inline-block"><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> - {{ __('dashboard.clients') }}
                    </h4>
                    <a class="btn btn-success" href="/admin-panel/clients-users/excel">{{ __('dashboard.export_excel') }}</a>
                </div>
                <div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i>
                            {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.clients') }} </a></li>
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
                    <h5 class="panel-title">{{ __('dashboard.clients') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                <div class="panel-body">
                    <div class="row table-responsive">
                        <table class="table custom-datatable text-center" dir="{{__('dashboard.dir')}}">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.id') }}</th>
                                    <th>{{ __('dashboard.user_name') }}</th>
                                    <th>{{ __('dashboard.the_date_of_joining') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.block') }}</th>
                                    <th>{{ __('dashboard.activation_code') }}</th>
                                    <th>{{ __('dashboard.region') }}</th>
                                    <th>{{ __('dashboard.addresses') }}</th>
                                    <th>{{ __('dashboard.count_orders') }}</th>
                                    <th>{{ __('dashboard.action_taken') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
    <script src="/site/js/jquery.rateyo.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '.drag_name', function() {
                var vals = $(this).val();
                var user_id = $(this).attr('user_id');
                $.get('/admin-panel/change_drag_name/' + user_id + '/' + vals, function(data) {})
            });

            generateTable();
        });

        function destroyTable() {
            if ($.fn.DataTable.isDataTable('.custom-datatable')) {
                $('.custom-datatable').DataTable().destroy();
            }
        }

        function toggleTable(status) {
            destroyTable();
            generateTable(status);
            if (status === 'deleted') {
                $('#deleted_id').css('display', 'none');
                $('#active_id').css('display', 'block');
            } else {
                $('#deleted_id').css('display', 'block');
                $('#active_id').css('display', 'none');
            }
        }

        function generateTable(status = '') {
            $('.custom-datatable').DataTable({
                buttons: [{
                    extend: 'colvis',
                    className: 'btn btn-default'
                }],
                @if (app()->getLocale() == 'en')
                    language: {
                        search: '<span>' + '{{ __('dashboard.search') }}' + ':</span> _INPUT_',
                        lengthMenu: '<span>' + '{{ __('dashboard.show') }}' + ':</span> _MENU_',
                        "lengthMenu": "Display _MENU_ records per page",
                        "zeroRecords": "Nothing found - sorry",
                        "info": "Showing page _PAGE_ of _PAGES_",
                        "infoEmpty": "No records available",
                        "infoFiltered": "(filtered from _MAX_ total records)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        },
                    },
                @else
                    language: {
                        search: '<span>' + '{{ __('dashboard.search') }}' + ':</span> _INPUT_',
                        lengthMenu: '<span>' + '{{ __('dashboard.show') }}' + ':</span> _MENU_',
                    },
                @endif
                processing: true,
                serverSide: true,
                ajax: '{{ url('/admin-panel/get/clients-data') }}',
                columns: [{
                        data: 'id',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'username',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'phone',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'block',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {
                        data: 'activation_code',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'region',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'addresses',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {
                        data: 'products_count',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    }
                ],
                "fnDrawCallback": function(setting) {
                    var switchElem = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
                    switchElem.forEach(function(html) {
                        var switchery = new Switchery(html, {
                            color: '#64bd63',
                            secondaryColor: '#B2BABB'
                        });
                    });
                },
            });
        }
    </script>
    </body>

    </html>

@stop

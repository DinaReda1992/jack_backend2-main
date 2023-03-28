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
    <!-- Data Tables -->
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('backend/datatables/dataTables.bs4.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/datatables/dataTables.bs4-custom.css') }}" />
    <link href="{{ asset('backend/datatables/buttons.bs.css') }}" rel="stylesheet" />
    <script src="{{ asset('backend/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript">
        var table = $('#Subject-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admins.datatable') }}",
                data: function(d) {
                    d.type = $('#type').val()
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
        });

        $('#type').change(function() {
            table.draw();
        });

        function deleteData(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            swal({
                title: 'هل انت متأكد؟',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'نعم',
                cancelButtonText: 'إلغاء'
            }).then(function() {
                $.ajax({
                    url: "{{ url('dashboard/admins/delete/') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_token': csrf_token
                    },
                    success: function(data) {
                        table.ajax.reload();
                        toastr.success(data.message)
                    },
                    error: function() {
                        toastr.error(data.message)
                    }
                });
            });
        }
    </script>
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
                        {{ __('dashboard.view_all_orders') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li class="active"><a href="">{{ __('dashboard.view_all_orders') }}</a></li>
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
                    <h5 class="panel-title">{{ __('dashboard.view_all_orders') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="clearfix"></div>
                <br>
                <form method="get" action="/admin-panel/filter-all">
                    <div class="row">
                        <div align="center" class="col-md-12">
                            <div class="col-md-5" align="center">
                                <input name="order_id" type="text" class="form-control" value="{{ @$order_id }}"
                                    placeholder="{{ __('dashboard.search_by_order_id') }}">
                            </div>
                            <div class="col-md-2" align="center">
                                <button class="btn btn-success">{{ __('dashboard.search') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="clearfix"></div>
                <br>

                <div class="table-responsive text-center text-center">
                    <table class="table table-striped table-bordered dt-responsive nowrap" id="Subject-table">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.order_id') }}</th>
                                <th style="width: 200px">{{ __('dashboard.order_date') }}</th>
                                <th>{{ __('dashboard.city') }}</th>
                                <th>{{ __('dashboard.order_owner') }}</th>
                                <th>{{ __('dashboard.payment_type') }}</th>
                                <th>{{ __('dashboard.order_status') }}</th>
                                <th>{{ __('dashboard.order_details') }}</th>
                                <th>{{ __('dashboard.action_taken') }}</th>
                                <th style="width:100px; min-width:100px;" class="text-center text-danger"><i
                                        class="fa fa-bolt"> </i></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>



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
    </script>
    </body>

    </html>

@stop

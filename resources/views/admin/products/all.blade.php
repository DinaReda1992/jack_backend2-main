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
    <script type="text/javascript" src="/assets/js/notify.js"></script>

    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>
    <!-- /theme JS files -->
    <script>
        function setSwitchery(switchElement, checkedBool) {
            if ((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }

        $(document).on("click", ".switchery", function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var id = $(this).parent().find('input').attr('object_id');
            var d_url = $(this).parent().find('input').attr('delete_url');

            $.ajax({
                url: d_url,
                type: 'post',
                data: '',
                success: function() {
                    notify.initialization("تم تغيير حالة المنتج بنجاح ", "success");

                },
                error: function() {
                    notify.initialization("حدث خطأ غير متوقع . ", "failed");

                }
            })
        });
    </script>

@stop
@section('content')


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-{{ __('dashboard.right') }}"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> -
                        {{ __('dashboard.view_products') }}
                        {{ app('request')->input('type') == 'deleted' ? __('dashboard.deleted') : '' }}
                        {{ isset($this_supplier) ? ' للمورد ' . $this_supplier->supplier_name : '' }}
                        <a class="btn btn-success"
                            href="/admin-panel/export-excel-product">{{ __('dashboard.export_excel') }}</a>
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a
                            href="/admin-panel/products/create{{ app('request')->input('supplier_id') ? '?supplier_id=' . app('request')->input('supplier_id') : '' }}">
                            <button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.add_product') }}</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_products') }}</a></li>
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
                    <h5 class="panel-title">{{ __('dashboard.view_products') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li>
                                @if (app('request')->input('type') == 'deleted')
                                    <a style="color: blue" href="/admin-panel/products?type=">
                                        {{ __('dashboard.active') }} <i class="fa fa-gift"></i></a>
                                @else
                                    <a style="color: red" href="/admin-panel/products?type=deleted">
                                        {{ __('dashboard.deleted') }} <i class="icon-trash"></i></a>
                                @endif
                            </li>
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">
                    <div id="spinner-containerr">
                        <div id="loading-spinnerr"></div>
                    </div>
                    <div class="row table-responsive">
                        <table class="table custom-datatable text-center" dir="rtl">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.id') }}</th>
                                    <th>{{ __('dashboard.product_name') }}</th>
                                    {{-- <th>{{ __('dashboard.supplier') }}</th> --}}
                                    <th>{{ __('dashboard.unit_of_measurement') }}</th>
                                    <th>{{ __('dashboard.price') }}</th>
                                    {{-- <th>{{ __('dashboard.available_quantity') }}</th> --}}
                                    <th>{{ __('dashboard.product_image') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
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
        // Warning alert

        $(document).ready(function() {
            $(document).on('change', '#supplier_id', function(e) {
                var supplier_id = $(this).val();
                window.location.href = "/admin-panel/products?" + "supplier_id=" + supplier_id +
                    "type={{ app('request')->input('type') ?: '' }}"
            });

        });
    </script>
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
            const supplier_id = '{{ request('supplier_id') }}';
            const type = '{{ request('type') }}';
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
                ajax: '{{ url('/admin-panel/products-data') }}?type=' + type + '&supplier_id=' + supplier_id,
                columns: [{
                        data: 'id',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'title',
                        orderable: true,
                        searchable: true
                    },
                    // {
                    //     data: 'supplier',
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function (data, type, full, meta) {
                    //         return $("<div/>").html(data).text();
                    //     }
                    // },
                    {
                        data: 'measurement',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'price',
                        orderable: true,
                        searchable: true
                    },
                    // {
                    //     data: 'quantity',
                    //     orderable: true,
                    //     searchable: true,
                    // },
                    {
                        data: 'image',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {
                        data: 'status',
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

@stop

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
    </script>

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
                        {{ __('dashboard.view_offers') }}
                        {{ app('request')->input('type') == 'deleted' ? __('dashboard.deleted') : '' }}
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/offers/create">
                            <button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.add_offer') }}</button>
                        </a>
                    </div>
                </div>


                <!-- 						<div class="heading-elements"> -->
                <!-- 							<div class="heading-btn-group"> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a> -->
                <!-- 							</div> -->
                <!-- 						</div> -->
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i>
                            {{ __('dashboard.home') }}</a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_offers') }}</a></li>
                </ul>
                <div class="col-12 col-lg-6 form-group" style="float: left;">

                </div>

                <!-- 						<ul class="breadcrumb-elements"> -->
                <!-- 							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li> -->
                <!-- 							<li class="dropdown"> -->
                <!-- 								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
                <!-- 									<i class="icon-gear position-left"></i> -->
                <!-- 									Settings -->
                <!-- 									<span class="caret"></span> -->
                <!-- 								</a> -->

                <!-- 								<ul class="dropdown-menu dropdown-menu-right"> -->
                <!-- 									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li> -->
                <!-- 									<li class="divider"></li> -->
                <!-- 									<li><a href="#"><i class="icon-gear"></i> All settings</a></li> -->
                <!-- 								</ul> -->
                <!-- 							</li> -->
                <!-- 						</ul> -->
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            @include('admin.message')
            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_offers') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            {{-- <li>
                                <a class="btn btn-default" href="#" onclick="downloadExcel()">
                                    {{ __('dashboard.export_excel') }} <i class="fa fa-file-excel"></i></a>
                            </li> --}}
                            <li>
                                <a id="active_id" style="color: blue;display: none;"
                                    href="#"onclick="toggleTable('')">{{ __('dashboard.active') }}<i
                                        class="fa fa-gift"></i></a>

                                <a id="deleted_id" style="color: red" href="#"
                                    onclick="toggleTable('deleted')">{{ __('dashboard.deleted') }} <i
                                        class="icon-trash"></i></a>
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
                                    <th>{{ __('dashboard.products') }}</th>
                                    <th>{{ __('dashboard.offer_type') }}</th>
                                    <th>{{ __('dashboard.start_offer_date') }}</th>
                                    <th>{{ __('dashboard.end_offer_date') }}</th>
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
        {{-- $(document).ready(function () { --}}
        {{-- $(document).on('change', '#user_id', function (e) { --}}
        {{-- var user_id = $(this).val(); --}}
        {{-- window.location.href = "/admin-panel/products?" + "user_id=" + user_id+"&&type={{app('request')->input('type')?:''}}" --}}
        {{-- }); --}}

        {{-- }); --}}
    </script>

    <script>
        $(function() {
            generateTable()



            $(document).on("click", ".switchery", function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                var id = $(this).parent().find('input').attr('object_id');
                var d_url = $(this).parent().find('input').attr('delete_url');
                $.ajax({
                    url: d_url,
                    type: 'post',
                    data: '',
                    success: function() {
                        notify.initialization("تم تغيير حالة العرض بنجاح ", "success");
                    },
                    error: function() {
                        notify.initialization("حدث خطأ غير متوقع . ", "failed");
                    }
                })
            });
        });

        function destroyTable() {
            if ($.fn.DataTable.isDataTable('.custom-datatable')) {
                $('.custom-datatable').DataTable().destroy();
            }
        }

        function toggleTable(status) {
            let user_id = locale = $('#user_id').find('option:selected').val();
            destroyTable();
            generateTable(user_id, status);
            if (status === 'deleted') {
                $('#deleted_id').css('display', 'none');
                $('#active_id').css('display', 'block');
            } else {
                $('#deleted_id').css('display', 'block');
                $('#active_id').css('display', 'none');
            }
        }

        function generateTable(user_id, status = '') {
            $('.custom-datatable').DataTable({
                buttons: [{
                    extend: 'colvis',
                    className: 'btn btn-default'
                }],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('/admin-panel/get/offers/data') }}' + '?status=' + status,
                    data: function(d) {
                        d.page_name = 'offers'
                    }
                },
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
                columns: [{
                        data: 'id',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'main_items',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {
                        data: 'type_id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {
                        data: 'start_date',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'end_date',
                        orderable: true,
                        searchable: true
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

        function downloadExcel() {
            const url = '/admin-panel/download-offers-excel';
            const a = document.createElement("a");
            a.href = url;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }
    </script>

@stop

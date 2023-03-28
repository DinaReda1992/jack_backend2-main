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
                    notify.initialization("تم تغيير حالة القسم بنجاح ", "success");

                },
                error: function() {
                    notify.initialization("حدث خطأ غير متوقع . ", "failed");

                }
            })
        });
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
                        {{ __('dashboard.view_main_categories') }} </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/main-categories/create"><button type="button" class="btn btn-success"
                                name="button"> {{ __('dashboard.add_new') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_main_categories') }} </a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_main_categories') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.category_name') }}</th>
                            <th>{{ __('dashboard.category_status') }}</th>
                            <th>{{ __('dashboard.branches') }} </th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody class="row_position">
                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ app()->getLocale() == 'ar' ? $object->name : $object->name_en }} </td>
                                <td>
                                    <div class="checkbox checkbox-switchery switchery-sm switchery-double">
                                        <input type="checkbox" object_id="{{ $object->id }}"
                                            delete_url="/{{ app()->getLocale() }}/admin-panel/stop_main_category/{{ $object->id }}"
                                            class="switchery sweet_switch" {{ $object->stop == 0 ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <a href="/admin-panel/categories?parent_id={{ $object->id }}">
                                        ({{ @$object->subCategories->count() }})
                                        {{ __('dashboard.branch') }}
                                    </a>
                                </td>
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-primary-600"><a
                                                href="/admin-panel/main-categories/{{ $object->id }}/edit"><i
                                                    class="icon-pencil7"></i></a></li>
                                        <li class="text-danger-600"><a onclick="return false;"
                                                object_id="{{ $object->id }}"
                                                delete_url="/admin-panel/main-categories/{{ $object->id }}"
                                                class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
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
    <link rel="stylesheet" href="/assets/js/plugins/jquery-ui-1.12.1/jquery-ui.css">

    {{--			<script src="https://code.jquery.com/jquery-1.12.4.js"></script> --}}
    <script src="/assets/js/plugins/jquery-ui-1.12.1/jquery-ui.js"></script>


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $(".row_position").sortable({
            delay: 150,
            stop: function() {
                var selectedData = new Array();
                $('.row_position>tr').each(function() {
                    selectedData.push($(this).attr("parent_id"));
                });
                updateOrder(selectedData);
            }
        });


        function updateOrder(data) {
            console.log(data);
            $.ajax({
                url: "/admin-panel/change-sort-main-categories",
                type: 'post',
                data: {
                    position: data
                },
                success: function() {}
            })
        }
    </script>


@stop

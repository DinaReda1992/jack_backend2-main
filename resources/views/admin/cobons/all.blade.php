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
                        {{ __('dashboard.view_discount_coupons') }}</h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/cobons/create"><button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.add_new_coupon') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_discount_coupons') }}</a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_discount_coupons') }}</h5>
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
                            <th>#</th>
                            <th>{{__('dashboard.creation_time')}}</th>
                            <th>{{__('dashboard.discount_coupon')}}</th>
                            <th>{{ __('dashboard.discount_percentage') }} </th>
                            <th>{{__('dashboard.count_days')}}</th>
                            <th>{{__('dashboard.the_number_of_times_the_coupon_is_used')}} </th>
                            <th>{{__('dashboard.coupon_validity_period')}}</th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1 @endphp
                        @foreach ($objects as $object)
                            <tr parent_id="{{ @$object->id }}">
                                <td>{{ $i }}</td>
                                <td>{{ @$object->created_at->diffForHumans() }}</td>

                                <td>{{ @$object->code }}</td>
                                <td>{{ @$object->percent }} %</td>
                                <td>{{ @$object->days }}</td>
                                <td>{{ @$object->usage_quota }}</td>
                                <td>{{ date('Y-m-d', strtotime(date('Y-m-d', strtotime($object->created_at)) . ' +' . $object->days . ' days')) }}
                                </td>
                                {{--									<td> --}}
                                {{--										{{ \App\Models\Orders::where('cobon',$object->code)->whereIn('status',[1,2,4])->count() }} --}}
                                {{--									</td> --}}
                                {{-- <td><img alt="" width="50" height="50" src="/uploads/{{@$object -> photo }}"></td> --}}
                                {{--									<td>{{@$object->created_at->format("jS \of F, Y g:i:s a") }}</td> --}}
                                {{-- <td><input value="{{@$object->orders }}" type="text" class="car_order" car_id="{{@$object->id }}" style="width: 50px;padding: 5px;text-align: center"></td> --}}
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        @if ($object->checkCobon() == 0)
                                            <li class="text-primary-600"><a
                                                    href="/admin-panel/cobons/{{ @$object->id }}/edit"><i
                                                        class="icon-pencil7"></i></a></li>
                                            <li class="text-danger-600"><a onclick="return false;"
                                                    object_id="{{ @$object->id }}"
                                                    delete_url="/admin-panel/cobons/{{ @$object->id }}"
                                                    class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
                                        @endif
                                        <!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
                                    </ul>
                                </td>
                            </tr>
                            @php $i++ @endphp
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

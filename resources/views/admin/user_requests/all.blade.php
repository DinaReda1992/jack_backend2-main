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
                    <div style=" text-align: center;">
                        <ul class="nav nav-tabs" role="tablist">
                            <li
                                class="{{ !app('request')->input('status') || app('request')->input('status') == 'all' ? 'active' : '' }}">
                                <a href="/admin-panel/user-requests">{{ __('dashboard.all') }}</a>
                            </li>
                            <li class="{{ app('request')->input('status') == 'waiting' ? 'active' : '' }}"><a
                                    href="/admin-panel/user-requests?status=waiting">{{ __('dashboard.waiting_for_confirmation') }}</a>
                            </li>
                            <li class="{{ app('request')->input('status') == 'canceled' ? 'active' : '' }}"><a
                                    href="/admin-panel/user-requests?status=canceled">{{ __('dashboard.cancelled') }}</a>
                            </li>

                        </ul>
                    </div>

                </div>


                <table class="table datatable-colvis-basic text-center">
                    <thead>
                        <tr>

                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.member_name') }}</th>
                            <th>{{ __('dashboard.phone') }}</th>
                            <th>{{ __('dashboard.region') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.details') }}</th>
                            <th>{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($objects as $object)
                            @php
                                $status = '';
                                $color = '';
                                
                                if ($object->approved == 0) {
                                    $status = __('dashboard.waiting');
                                    $color = '#1c43b1';
                                } elseif ($object->approved == 2) {
                                    $status = __('dashboard.cancelled');
                                    $color = 'red';
                                }
                            @endphp
                            <tr parent_id="{{ $object->id }}">
                                <td>{{ $object->id }}</td>
                                <td>{{ $object->username }}</td>
                                <td style="direction: ltr">+({{ $object->phonecode }}){{ $object->phone }}</td>
                                <td>{{ @$object->region->name }}
                                    - {{ @$object->state->name }}</td>

                                <td><span style="color: {{ $color }}">{{ $status }}</span></td>
                                <td>
                                    <a data-toggle="modal" data-target="#myModal{{ $object->id }}"
                                        onclick="return false;" href="/user-details/{{ $object->id }}">
                                        {{ __('dashboard.client_details') }}
                                    </a>
                                    <div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content" style="width: 860px;">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        {{ __('dashboard.client_details') }}</h4>
                                                </div>
                                                <div class="modal-body">


                                                    <div class="row">
                                                        <label
                                                            class="col-sm-4"><strong>{{ __('dashboard.order_owner') }}</strong></label>

                                                        <div class="col-sm-12">
                                                            <div class="box-body box-profile">
                                                                <img class="profile-user-img img-responsive img-circle"
                                                                    src="/uploads/{{ @$object->photo ?: 'default-user.png' }}"
                                                                    style="height: 100px;" alt="User profile picture">
                                                                <h3 class="profile-username text-center">
                                                                    {{ @$object->username }}</h3>
                                                                <p class="text-muted text-center">{{ @$object->phone }}</p>


                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label
                                                            class="col-sm-4"><strong>{{ __('dashboard.email') }}</strong></label>
                                                        <div class="col-sm-8">{{ @$object->email }}</div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <label class="col-sm-4"><strong>
                                                                {{ __('dashboard.activity_type') }}</strong></label>
                                                        <div class="col-sm-8">
                                                            {{ @$object->clientType->name }}
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> {{ __('dashboard.address') }}
                                                            </strong></label>
                                                        <div class="col-sm-8">
                                                            {{ @$object->country->name }} - {{ @$object->region->name }}
                                                            - {{ @$object->state->name }}
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong>
                                                                {{ __('dashboard.commercial_registration_no') }}</strong></label>
                                                        <div class="col-sm-8">
                                                            {{ @$object->commercial_no }}
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong>
                                                                {{ __('dashboard.commercial_registration_end_date') }}
                                                            </strong></label>
                                                        <div class="col-sm-8">
                                                            {{ $object->commercial_end_date }}
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong>
                                                                {{ __('dashboard.tax_number') }}</strong></label>
                                                        <div class="col-sm-8">
                                                            {{ $object->tax_number }}
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label
                                                            class="col-sm-4"><strong>{{ __('dashboard.commercial_registration_image') }}</strong></label>
                                                        <div class="col-sm-8">
                                                            <img class=" img-responsive "
                                                                src="/uploads/{{ @$object->commercial_id ?: 'default-user.png' }}">
                                                        </div>
                                                    </div>
                                                    <hr>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{ __('dashboard.close') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    @if ($object->approved == 0 || $object->approved == 2)
                                        <div class="btn btn-success">
                                            <a data-toggle="modal" data-target="#offers{{ $object->id }}"
                                                onclick="return false;" href="#" style="color: #fff;"><i
                                                    class="icon-check"></i> {{ __('dashboard.accept') }}
                                            </a>

                                        </div>

                                        <div class="modal fade" id="offers{{ $object->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">
                                                            {{ __('dashboard.accept_order') }}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>
                                                            {{ __('dashboard.are_you_sure_to_accept_the_order') }}
                                                        </p>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">
                                                            {{ __('dashboard.close') }}
                                                        </button>
                                                        <a href="/admin-panel/approve-user-request/{{ $object->id }}"
                                                            class="btn btn-success"> {{ __('dashboard.yes') }} </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($object->approved == 0)
                                        <div class="btn btn-danger">
                                            <a onclick="return false;" data-toggle="modal"
                                                data-target="#myModal{{ $object->id }}555" href="#"
                                                style="color: #fff;">
                                                <i class="icon-cancel-square"></i> {{ __('dashboard.cancel_request') }}
                                            </a>
                                        </div>

                                        <div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1"
                                            role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="modal-title" id="myModalLabel">
                                                            {{ __('dashboard.message_cancel_order') }}</h4>

                                                    </div>
                                                    <form method="post"
                                                        action="/admin-panel/cancel_user_request/{{ $object->id }}">
                                                        {!! csrf_field() !!}

                                                        <div class="modal-body">

                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"></label>
                                                                <textarea name="reason_of_cancel" class="form-control" placeholder="{{ __('dashboard.reason_of_cancel') }}"></textarea>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" class="btn btn-default"
                                                                data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ __('dashboard.send') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="btn btn-primary"><a style="color: #fff;"
                                            href="/admin-panel/user-requests/{{ $object->id }}/edit"><i
                                                class="icon-pencil7"></i> {{ __('dashboard.edit') }}</a></div>


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

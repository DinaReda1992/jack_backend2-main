@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    {{--	<script type="text/javascript" src="/assets/js/core/app.js"></script> --}}

    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>

    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
    <!-- InputMask -->
    <script src="/assets/js/plugins/input-mask/jquery.inputmask.js"></script>
    <script src="/assets/js/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="/assets/js/plugins/input-mask/jquery.inputmask.extensions.js"></script>

    <!-- /theme JS files -->
    <!-- /theme JS files -->

    {{--	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script> --}}

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

    <link href="/site/js/datepicker/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css">
    <link href="/site/js/datepicker/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css">


    <script src="/site/js/datepicker/js/bootstrap-datepicker.js"></script>

    <!-- /theme JS files -->
    <script type="text/javascript">
        $(document).ready(function() {
            $("[data-mask]").inputmask();
            $('#licence_end_date').datepicker();
            $('.country_id').change(function() {
                var country_id = $(this).val();
                $('.currency').val(country_id);
                $('.phonecode').val(country_id);
            });

            $('.user_type_id').change(function() {
                var change_val = $(this).val();
                if (change_val == 3) {
                    $('.profit_rate').show();
                } else {
                    $('.profit_rate').hide();
                }

            })
            $('.country_id').change(function() {
                var country_id = $('.country_id').val();
                $.ajax({
                    url: '/admin-panel/getRegions/' + country_id,
                    success: function(data) {
                        $('.region_id').html(data);
                    }
                });

            });

            $('.region_id').change(function() {
                var country_id = $('.region_id').val();
                $.ajax({
                    url: '/admin-panel/getRegionStates/' + country_id,
                    success: function(data) {
                        $('.state_id').html(data);
                    }
                });

            });
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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.driver') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/drivers">{{ __('dashboard.view_drivers') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.driver') }}</li>
                </ul>
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

            @include('admin.message')

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.driver') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">

                    <form enctype="multipart/form-data" method="post" class="form-horizontal"
                        action="{{ isset($object) ? '/'.app()->getLocale().'/admin-panel/drivers/' . $object->id : '/'.app()->getLocale().'/admin-panel/drivers' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.user_name') }} *</label>
                                <div class="col-lg-10">
                                    <input type="text" name="username"
                                        value="{{ isset($object) ? $object->username : old('username') }}"
                                        class="form-control" placeholder="{{ __('dashboard.user_name') }}">
                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.email') }} </label>
                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <div class="input-group-addon">
                                            <i class="fa fa-envelope-o"></i>
                                        </div>
                                        <input type="text" name="email"
                                            value="{{ isset($object) ? $object->email : old('email') }}"
                                            class="form-control" placeholder="{{ __('dashboard.email') }}">
                                    </div><!-- /.input group -->

                                </div>

                            </div>
                            <div
                                class="form-group{{ $errors->has('phone') ? ' has-error' : '' }} {{ $errors->has('phonecode') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.phone') }} *</label>

                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <div class="input-group-addon">
                                            <i class="fa fa-phone"></i>
                                        </div>
                                        <input type="text" name="phone"
                                            value="{{ isset($object) ? $object->phone : old('phone') }}"
                                            placeholder="{{ __('dashboard.phone') }}" class="form-control"
                                            data-inputmask='"mask": "999999999"' data-mask>
                                    </div><!-- /.input group -->

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('licence_number') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.commercial_no')}}</label>
                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <input type="number" name="licence_number"
                                            value="{{ isset($object) ? $object->licence_number : old('licence_number') }}"
                                            class="form-control" placeholder="{{__('dashboard.commercial_no')}}">
                                    </div><!-- /.input group -->
                                    @if ($errors->has('licence_number'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('licence_number') }}</strong>
                                        </span>
                                    @endif

                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('licence_end_date') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.license_end_date') }}</label>
                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <input type="text" id="licence_end_date" name="licence_end_date"
                                            value="{{ isset($object) ? $object->licence_end_date : old('licence_end_date') }}"
                                            class="form-control" placeholder="{{ __('dashboard.license_end_date') }}">
                                    </div><!-- /.input group -->
                                    @if ($errors->has('licence_end_date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('licence_end_date') }}</strong>
                                        </span>
                                    @endif

                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('licence_photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.licence_photo') }}</label>
                                <div class="col-lg-10">

                                    <input type="file" name="licence_photo" class="file-input"
                                        data-show-caption="false" data-show-upload="false"
                                        data-browse-class="btn btn-primary btn-xs"
                                        data-remove-class="btn btn-default btn-xs">
                                    @if ($errors->has('licence_photo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('licence_photo') }}</strong>
                                        </span>
                                    @endif



                                </div>

                            </div>
                            @if (isset($object) && $object->licence_photo)
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ __('dashboard.current_image') }}</label>
                                    <div class="col-lg-10">
                                        <img alt="" width="100" height="75"
                                            src="/uploads/{{ $object->licence_photo }}">
                                    </div>

                                </div>
                            @endif





                            <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.profile_photo') }}</label>
                                <div class="col-lg-10">

                                    <input type="file" name="photo" class="file-input" data-show-caption="false"
                                        data-show-upload="false" data-browse-class="btn btn-primary btn-xs"
                                        data-remove-class="btn btn-default btn-xs">
                                    @if ($errors->has('photo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('photo') }}</strong>
                                        </span>
                                    @endif



                                </div>

                            </div>
                            @if (isset($object) && $object->photo)
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ __('dashboard.current_image') }}</label>
                                    <div class="col-lg-10">
                                        <img alt="" width="100" height="75"
                                            src="/uploads/{{ $object->photo }}">
                                    </div>

                                </div>
                            @endif

                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.driver') }} <i class="icon-arrow-left13 position-right"></i></button>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
            @include('admin.footer')

        </div>

        <script type="text/javascript">
            // Warning alert
        </script>
    </div>
@stop

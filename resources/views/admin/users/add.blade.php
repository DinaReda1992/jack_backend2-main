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
            $('#commercial_end_date').datepicker();
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
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.client') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/all-users">{{ __('dashboard.clients') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.client') }}</li>
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
                        {{ __('dashboard.client') }} </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() . '/admin-panel/all-users/' . $object->id : '/' . app()->getLocale() . '/admin-panel/all-users' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <!-- 									<legend class="text-bold">Basic inputs</legend> -->
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
                                <label class="control-label col-lg-2">{{ __('dashboard.email') }} *</label>
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
                            <div class=" form-group{{ $errors->has('client_type') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.activity_type') }}</label>
                                <div class="col-lg-10">
                                    <select name="client_type" class="form-control">

                                        <option value="">{{ __('dashboard.activity_type') }}</option>
                                        @foreach (\App\Models\ClientTypes::all() as $shopType)
                                            <option value="{{ $shopType->id }}"
                                                {{ isset($object) && $object->client_type == $shopType->id ? 'selected' : (old('client_type') == $shopType->id ? 'selected' : '') }}>
                                                {{ $shopType->name }} </option>
                                        @endforeach

                                    </select>
                                    @if ($errors->has('client_type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('client_type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.select_country') }}</label>
                                <div class="col-lg-10">
                                    <select name="country_id" class="form-control country_id">
                                        <option value="0">{{ __('dashboard.select_country') }}</option>
                                        @foreach (\App\Models\Countries::all() as $country)
                                            <option value="{{ $country->id }}"
                                                {{ isset($object) && $object->country_id == $country->id ? 'selected' : (old('country_id') == $country->id ? 'selected' : '') }}>
                                                {{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('country_id') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('region_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.select_region') }} *</label>
                                <div class="col-lg-10">
                                    <select name="region_id" class="form-control region_id">

                                        <option value="">{{ __('dashboard.select_region') }}</option>
                                        @if (isset($object))
                                            @foreach (\App\Models\Regions::where('country_id', $object->country_id)->get() as $region)
                                                <option value="{{ $region->id }}"
                                                    {{ isset($object) && $object->region_id == $region->id ? 'selected' : (old('region_id') == $region->id ? 'selected' : '') }}>
                                                    {{ $region->name }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('region_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('region_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.select_city') }} *</label>
                                <div class="col-lg-10">
                                    <select name="state_id" class="form-control state_id">

                                        <option value="">{{ __('dashboard.select_city') }}</option>
                                        @if (isset($object))
                                            @foreach (\App\Models\States::where('region_id', $object->region_id)->get() as $state)
                                                <option value="{{ $state->id }}"
                                                    {{ isset($object) && $object->state_id == $state->id ? 'selected' : (old('state_id') == $state->id ? 'selected' : '') }}>
                                                    {{ $state->name }} </option>
                                            @endforeach
                                        @endif


                                    </select>
                                    @if ($errors->has('state_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('state_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('commercial_no') ? ' has-error' : '' }}">
                                <label
                                    class="control-label col-lg-2">{{ __('dashboard.commercial_registration_no') }}</label>
                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <input type="number" name="commercial_no"
                                            value="{{ isset($object) ? $object->commercial_no : old('commercial_no') }}"
                                            class="form-control"
                                            placeholder="{{ __('dashboard.commercial_registration_no') }}">
                                    </div><!-- /.input group -->
                                    @if ($errors->has('commercial_no'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('commercial_no') }}</strong>
                                        </span>
                                    @endif

                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('tax_number') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.tax_number') }}</label>
                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <input type="number" name="tax_number"
                                            value="{{ isset($object) ? $object->tax_number : old('tax_number') }}"
                                            class="form-control" placeholder="{{ __('dashboard.tax_number') }}">
                                    </div><!-- /.input group -->
                                    @if ($errors->has('tax_number'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('tax_number') }}</strong>
                                        </span>
                                    @endif

                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('commercial_end_date') ? ' has-error' : '' }}">
                                <label
                                    class="control-label col-lg-2">{{ __('dashboard.commercial_registration_end_date') }}</label>
                                <div class="col-lg-10" style="float: left;">
                                    <div class="input-group" style="direction: ltr;">
                                        <input type="text" id="commercial_end_date" name="commercial_end_date"
                                            value="{{ isset($object) ? $object->commercial_end_date : old('commercial_end_date') }}"
                                            class="form-control"
                                            placeholder="{{ __('dashboard.commercial_registration_end_date') }} التجارى">
                                    </div><!-- /.input group -->
                                    @if ($errors->has('commercial_end_date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('commercial_end_date') }}</strong>
                                        </span>
                                    @endif

                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('commercial_id') ? ' has-error' : '' }}">
                                <label
                                    class="control-label col-lg-2">{{ __('dashboard.commercial_registration_image') }}</label>
                                <div class="col-lg-10">

                                    <input type="file" name="commercial_id" class="file-input"
                                        data-show-caption="false" data-show-upload="false"
                                        data-browse-class="btn btn-primary btn-xs"
                                        data-remove-class="btn btn-default btn-xs">
                                    @if ($errors->has('commercial_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('commercial_id') }}</strong>
                                        </span>
                                    @endif



                                </div>

                            </div>
                            @if (isset($object) && $object->commercial_id)
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ __('dashboard.current_image') }}</label>
                                    <div class="col-lg-10">
                                        <img alt="" width="100" height="75"
                                            src="/uploads/{{ $object->commercial_id }}">
                                    </div>

                                </div>
                            @endif


                            @php
                                $long = @$object->longitude ?: old('longitude');
                                $lat = @$object->latitude ?: old('latitude');
                            @endphp
                            <div
                                class="form-group{{ $errors->has('longitude') || $errors->has('latitude') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.location_on_map') }}</label>
                                <div class="col-lg-10">
                                    <input type="hidden" id='lon' name="longitude" />
                                    <input type="hidden" id='lat' name="latitude" />
                                    <input type="hidden" name="country_code" value="">
                                    <input type="hidden" name="user_type_id" value="3">

                                    @include('admin.items.mapPlace')
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.address_description_ar') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="address"
                                        value="{{ isset($object) ? $object->address : old('address') }}"
                                        class="form-control" placeholder="{{ __('dashboard.address') }}">
                                    @if ($errors->has('address'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!--
                           <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="control-label col-lg-2">{{ __('dashboard.password') }} *</label>
                            <div class="col-lg-10">
                             <input type="password" name="password" class="form-control" placeholder="{{ __('dashboard.password') }}">
                             @if ($errors->has('password'))
    <span class="help-block">
                      <strong>{{ $errors->first('password') }}</strong>
                      </span>
    @endif
                            </div>
                           </div>

                           <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="control-label col-lg-2">{{ __('dashboard.password_confirmation') }}</label>
                            <div class="col-lg-10">
                             <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('dashboard.password_confirmation') }}">
                             @if ($errors->has('password_confirmation'))
    <span class="help-block">
                      <strong>{{ $errors->first('password_confirmation') }}</strong>
                      </span>
    @endif
                            </div>
                           </div>
                    -->

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
                                {{ __('dashboard.client') }}
                                <i class="icon-arrow-left13 position-right"></i></button>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->
        <script src="{{ asset('js/mapPlace.js') }}"></script>
        <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkQ8_neT4uZpVaXG1SbZNWKH1fnQHnbGk&libraries=places&callback=initMap&language={{ App::getLocale() }}"
            async defer></script>

        <script type="text/javascript">
            // Warning alert
            $(window).load(function() {
                @if (!isset($object))
                    getCurrentLocation();
                @else
                    {
                        @if ($object->latitude && $object->longitude)
                            getLocationName({{ $object->latitude }}, {{ $object->longitude }});
                        @else
                            getCurrentLocation();
                        @endif
                    }
                @endif
            });
            // var elem1 = document.querySelector('.js-switch1');
            // var init = new Switchery(elem1);
            // var elem2 = document.querySelector('.js-switch2');
            // var init = new Switchery(elem2);
            // var elem3 = document.querySelector('.js-switch3');
            // var init = new Switchery(elem3);
        </script>
    </div>
@stop
